<?php

namespace App\Models;

class Campaign extends Model
{
    protected static string $table = 'campaigns';

    public static function updateProgress(int $campaignId): void
    {
        $db = self::getDb();

        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM email_queue
            WHERE campaign_id = :id
        ");
        $stmt->execute([':id' => $campaignId]);
        $stats = $stmt->fetch();

        $total = (int)($stats['total'] ?? 0);
        $sent = (int)($stats['sent'] ?? 0);
        $failed = (int)($stats['failed'] ?? 0);

        $statusUpdate = null;
        if ($total > 0 && ($sent + failed) >= $total) {
            $statusUpdate = 'completed';
        }

        $fields = [
            'total_recipients' => $total,
            'sent_count' => $sent,
            'failed_count' => $failed,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($statusUpdate === 'completed') {
            $fields['status'] = 'completed';
            $fields['completed_at'] = date('Y-m-d H:i:s');
        }

        self::update($campaignId, $fields);
    }
}
