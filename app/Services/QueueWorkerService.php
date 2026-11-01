<?php

namespace App\Services;

use App\Models\QueueJob;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\SmtpAccount;
use App\Models\DeliveryLog;
use App\Models\Suppression;
use App\Models\AppSetting;

class QueueWorkerService
{
    private RoutingEngineService $router;
    private SmtpManagerService $smtpManager;
    private string $workerId;

    public function __construct(?string $workerId = null)
    {
        $this->router = new RoutingEngineService();
        $this->smtpManager = new SmtpManagerService();
        $this->workerId = $workerId ?: 'worker-' . getmypid() . '-' . substr(md5((string)microtime(true)), 0, 6);
    }

    /**
     * Process a batch of queued jobs
     */
    public function processBatch(int $limit = 50): array
    {
        $stats = [
            'claimed' => 0,
            'sent' => 0,
            'retry' => 0,
            'failed' => 0,
            'suppressed' => 0,
        ];

        // Update heartbeat
        AppSetting::set('worker_heartbeat', gmdate('Y-m-d H:i:s'));

        $jobs = QueueJob::claimBatch($limit, $this->workerId);
        $stats['claimed'] = count($jobs);

        if (empty($jobs)) {
            return $stats;
        }

        foreach ($jobs as $job) {
            $campaign = $job['campaign_id'] ? Campaign::find((int)$job['campaign_id']) : null;
            $email = $job['recipient_email'];

            // 1. Check suppression list
            if (Contact::isSuppressed($email)) {
                QueueJob::update((int)$job['id'], [
                    'status' => 'cancelled',
                    'locked_at' => null,
                    'locked_by' => null,
                    'last_error' => 'Recipient is in suppression list.',
                ]);
                $stats['suppressed']++;
                if ($campaign) {
                    Campaign::updateProgress((int)$campaign['id']);
                }
                continue;
            }

            // 2. Select eligible SMTP Account
            $smtpAccount = null;
            if ($campaign) {
                $smtpAccount = $this->router->selectSmtpAccount($campaign);
            } elseif (!empty($job['selected_smtp_id'])) {
                $smtpAccount = SmtpAccount::find((int)$job['selected_smtp_id']);
            }

            // If no campaign config, pick any active SMTP with quota
            if (!$smtpAccount) {
                $active = SmtpAccount::where('status', 'active');
                foreach ($active as $acc) {
                    if ($this->router->hasAvailableQuota($acc)) {
                        $smtpAccount = $acc;
                        break;
                    }
                }
            }

            // If still no SMTP available (e.g. all accounts exceeded hourly/daily quotas)
            if (!$smtpAccount) {
                // Re-queue for next 10 minutes without burning attempt
                QueueJob::update((int)$job['id'], [
                    'status' => 'pending',
                    'scheduled_at' => gmdate('Y-m-d H:i:s', time() + 600),
                    'locked_at' => null,
                    'locked_by' => null,
                    'last_error' => 'All SMTP accounts have reached quota or are inactive. Postponed.',
                ]);
                $stats['retry']++;
                continue;
            }

            // 3. Dispatch Email via SMTP
            $headers = !empty($job['headers_json']) ? (json_decode($job['headers_json'], true) ?: []) : [];
            $result = $this->smtpManager->send(
                $smtpAccount,
                $email,
                $job['recipient_name'],
                $job['subject'],
                $job['body_html'],
                $job['body_plain'],
                $headers
            );

            if ($result['success']) {
                // Sent successfully
                QueueJob::markSent((int)$job['id'], (int)$smtpAccount['id']);
                SmtpAccount::incrementUsage((int)$smtpAccount['id'], true);

                DeliveryLog::record(
                    $campaign ? (int)$campaign['id'] : null,
                    (int)$smtpAccount['id'],
                    $email,
                    'accepted',
                    ['duration_ms' => $result['duration_ms']]
                );

                $stats['sent']++;
            } else {
                // Dispatch failed
                SmtpAccount::incrementUsage((int)$smtpAccount['id'], false);
                $isPermanent = $result['is_permanent'] ?? false;
                $currentAttempts = (int)$job['attempts'];
                $maxAttempts = (int)$job['max_attempts'];

                if ($isPermanent || $currentAttempts >= $maxAttempts) {
                    // Terminal failure
                    QueueJob::markFailed((int)$job['id'], (int)$smtpAccount['id'], $result['message']);
                    $stats['failed']++;

                    if ($isPermanent) {
                        Suppression::add($email, 'hard_bounce', $campaign ? (int)$campaign['id'] : null);
                    }

                    DeliveryLog::record(
                        $campaign ? (int)$campaign['id'] : null,
                        (int)$smtpAccount['id'],
                        $email,
                        'rejected',
                        ['error' => $result['message']]
                    );
                } else {
                    // Transient retry with exponential backoff
                    $delay = min(3600, 300 * (int)pow(2, $currentAttempts));
                    QueueJob::markRetry((int)$job['id'], (int)$smtpAccount['id'], $result['message'], $delay);
                    $stats['retry']++;
                }
            }

            if ($campaign) {
                Campaign::updateProgress((int)$campaign['id']);
            }
        }

        return $stats;
    }
}
