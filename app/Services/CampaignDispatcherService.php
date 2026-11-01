<?php

namespace App\Services;

use App\Database\Connection;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\EmailTemplate;
use PDO;

class CampaignDispatcherService
{
    /**
     * Dispatch or queue a campaign to recipient contacts
     */
    public function dispatchCampaign(int $campaignId): int
    {
        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            throw new \RuntimeException("Campaign not found.");
        }

        $db = Connection::getInstance();

        // 1. Resolve recipients
        $listIds = json_decode($campaign['target_list_ids'] ?? '[]', true) ?: [];
        if (empty($listIds)) {
            throw new \RuntimeException("No recipient contact lists selected.");
        }

        $placeholders = implode(',', array_fill(0, count($listIds), '?'));
        $sql = "
            SELECT DISTINCT c.id, c.email, c.first_name, c.last_name
            FROM contacts c
            INNER JOIN contact_list_members m ON c.id = m.contact_id
            WHERE m.list_id IN ({$placeholders})
              AND c.status = 'subscribed'
              AND c.email NOT IN (SELECT email FROM suppressions)
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($listIds);
        $contacts = $stmt->fetchAll();

        if (empty($contacts)) {
            throw new \RuntimeException("No active, opted-in recipients found in the selected lists.");
        }

        $db->beginTransaction();
        try {
            $queueIns = $db->prepare("
                INSERT INTO email_queue (
                    campaign_id, contact_id, recipient_email, recipient_name,
                    subject, body_html, body_plain, headers_json,
                    status, scheduled_at
                ) VALUES (
                    :cid, :contact_id, :email, :name,
                    :subject, :html, :plain, :headers,
                    'pending', :scheduled_at
                )
            ");

            $tokenIns = $db->prepare("
                INSERT INTO unsubscribe_tokens (token, email, campaign_id)
                VALUES (:token, :email, :cid)
            ");

            $scheduledAt = $campaign['scheduled_at'] ?: gmdate('Y-m-d H:i:s');
            $appKey = config('app.key');
            $queuedCount = 0;

            foreach ($contacts as $contact) {
                $email = $contact['email'];
                $firstName = $contact['first_name'] ?: 'Valued Subscriber';
                $lastName = $contact['last_name'] ?: '';
                $fullName = trim("{$firstName} {$lastName}");

                // Generate secure token
                $token = hash_hmac('sha256', "{$email}:{$campaignId}:" . microtime(), $appKey);
                $tokenIns->execute([
                    ':token' => $token,
                    ':email' => $email,
                    ':cid' => $campaignId,
                ]);

                $unsubscribeUrl = url("unsubscribe?token={$token}");

                // Replace merge tags
                $search = ['{{first_name}}', '{{last_name}}', '{{email}}', '{{unsubscribe_url}}'];
                $replace = [$firstName, $lastName, $email, $unsubscribeUrl];

                $personalizedSubject = str_replace($search, $replace, $campaign['subject']);
                $personalizedHtml = str_replace($search, $replace, $campaign['body_html']);
                $personalizedPlain = str_replace($search, $replace, $campaign['body_plain'] ?? '');

                // RFC 8058 List-Unsubscribe Header
                $headers = [
                    'List-Unsubscribe' => "<{$unsubscribeUrl}>",
                    'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                    'X-MailMoll-Campaign-ID' => (string)$campaignId,
                ];

                $queueIns->execute([
                    ':cid' => $campaignId,
                    ':contact_id' => $contact['id'],
                    ':email' => $email,
                    ':name' => $fullName,
                    ':subject' => $personalizedSubject,
                    ':html' => $personalizedHtml,
                    ':plain' => $personalizedPlain,
                    ':headers' => json_encode($headers),
                    ':scheduled_at' => $scheduledAt,
                ]);

                $queuedCount++;
            }

            // Update campaign status
            $isScheduledFuture = !empty($campaign['scheduled_at']) && strtotime($campaign['scheduled_at']) > time();
            $newStatus = $isScheduledFuture ? 'scheduled' : 'queued';

            $campUpdate = $db->prepare("
                UPDATE campaigns
                SET status = :status, total_recipients = :total, started_at = CURRENT_TIMESTAMP
                WHERE id = :cid
            ");
            $campUpdate->execute([
                ':status' => $newStatus,
                ':total' => $queuedCount,
                ':cid' => $campaignId,
            ]);

            $db->commit();
            return $queuedCount;

        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
