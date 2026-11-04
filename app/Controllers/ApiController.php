<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\QueueJob;
use App\Models\Contact;
use App\Models\SmtpAccount;
use App\Models\SmtpGroup;
use App\Models\AppSetting;
use App\Models\AuditLog;
use App\Services\QueueWorkerService;
use App\Services\RoutingEngineService;
use App\Database\Connection;
use PDO;

class ApiController
{
    /**
     * Parse incoming request payload (JSON or form-data)
     */
    private function getPayload(): array
    {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                return $json;
            }
        }
        return $_POST ?? [];
    }

    /**
     * POST /api/v1/emails/send
     * Enqueue a transactional email to be dispatched by the worker
     */
    public function sendEmail(): void
    {
        $data = $this->getPayload();

        $to = trim((string)($data['to'] ?? $data['recipient_email'] ?? ''));
        $name = trim((string)($data['name'] ?? $data['recipient_name'] ?? ''));
        $subject = trim((string)($data['subject'] ?? ''));
        $bodyHtml = (string)($data['body_html'] ?? $data['html'] ?? '');
        $bodyPlain = (string)($data['body_plain'] ?? $data['plain'] ?? strip_tags($bodyHtml));
        $smtpGroupId = !empty($data['smtp_group_id']) ? (int)$data['smtp_group_id'] : null;
        $smtpId = !empty($data['smtp_id']) ? (int)$data['smtp_id'] : null;
        $headers = !empty($data['headers']) && is_array($data['headers']) ? $data['headers'] : [];

        // Validation
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Response::error('A valid recipient email address is required in "to".', 422);
        }

        if (empty($subject)) {
            Response::error('Email "subject" is required.', 422);
        }

        if (empty($bodyHtml)) {
            Response::error('Email content "body_html" or "html" is required.', 422);
        }

        // Check suppression list
        if (Contact::isSuppressed($to)) {
            Response::error("Recipient '{$to}' is currently in the suppression list (bounced or unsubscribed).", 422, [
                'suppressed' => true,
                'recipient' => $to,
            ]);
        }

        // Resolve SMTP routing if group specified
        $selectedSmtpId = $smtpId;
        if (!$selectedSmtpId && $smtpGroupId) {
            $router = new RoutingEngineService();
            $group = SmtpGroup::find($smtpGroupId);
            if ($group) {
                $account = $router->selectFromGroup($group);
                if ($account) {
                    $selectedSmtpId = (int)$account['id'];
                }
            }
        }

        // Queue Job
        $jobId = QueueJob::create([
            'campaign_id' => null,
            'selected_smtp_id' => $selectedSmtpId,
            'recipient_email' => $to,
            'recipient_name' => $name,
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'body_plain' => $bodyPlain,
            'headers_json' => !empty($headers) ? json_encode($headers) : null,
            'status' => 'pending',
            'attempts' => 0,
            'max_attempts' => 3,
            'scheduled_at' => gmdate('Y-m-d H:i:s'),
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);

        AuditLog::log('api_email_enqueued', 'email_queue', $jobId, [
            'recipient' => $to,
            'subject' => $subject,
        ]);

        Response::json([
            'success' => true,
            'status' => 'queued',
            'message' => 'Email enqueued successfully.',
            'job_id' => $jobId,
            'message_id' => 'msg_' . str_pad((string)$jobId, 8, '0', STR_PAD_LEFT),
            'recipient' => $to,
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
        ], 200);
    }

    /**
     * GET /api/v1/emails/{id}
     * Inspect delivery status of an enqueued message
     */
    public function getEmail(int $id): void
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare("
            SELECT id, campaign_id, selected_smtp_id, recipient_email, recipient_name,
                   subject, status, attempts, max_attempts, last_error, scheduled_at,
                   updated_at, created_at
            FROM email_queue
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            Response::error("Queued message #{$id} was not found.", 404);
        }

        Response::json([
            'success' => true,
            'message_id' => 'msg_' . str_pad((string)$job['id'], 8, '0', STR_PAD_LEFT),
            'job' => $job,
        ], 200);
    }

    /**
     * GET /api/v1/status
     * Telemetry, worker heartbeat, and queue metrics
     */
    public function status(): void
    {
        $db = Connection::getInstance();

        $counts = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'retry' THEN 1 ELSE 0 END) as retry,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM email_queue
        ")->fetch(PDO::FETCH_ASSOC);

        $heartbeat = AppSetting::get('worker_heartbeat');
        $isHealthy = false;
        if (!empty($heartbeat)) {
            $isHealthy = (time() - strtotime($heartbeat)) < 300;
        }

        Response::json([
            'success' => true,
            'platform' => config('app.name', 'MailFlow'),
            'version' => config('app.version', '2.0.2'),
            'status' => 'operational',
            'worker' => [
                'heartbeat' => $heartbeat,
                'healthy' => $isHealthy,
            ],
            'queue_metrics' => [
                'total' => (int)($counts['total'] ?? 0),
                'pending' => (int)($counts['pending'] ?? 0),
                'processing' => (int)($counts['processing'] ?? 0),
                'sent' => (int)($counts['sent'] ?? 0),
                'retry' => (int)($counts['retry'] ?? 0),
                'failed' => (int)($counts['failed'] ?? 0),
                'cancelled' => (int)($counts['cancelled'] ?? 0),
            ],
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
        ], 200);
    }

    /**
     * POST /api/v1/cron/run or /api/v1/worker/run
     * Trigger queue worker batch execution via API token (for web-based crons)
     */
    public function runWorker(): void
    {
        $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 25;
        $worker = new QueueWorkerService('api-trigger-' . substr(md5((string)microtime(true)), 0, 8));
        $stats = $worker->processBatch($limit);

        AuditLog::log('api_worker_run', 'email_queue', null, $stats);

        Response::json([
            'success' => true,
            'message' => sprintf(
                "Processed batch of %d: %d claimed, %d sent, %d retries, %d failed.",
                $limit,
                $stats['claimed'],
                $stats['sent'],
                $stats['retry'],
                $stats['failed']
            ),
            'stats' => $stats,
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
        ], 200);
    }
}
