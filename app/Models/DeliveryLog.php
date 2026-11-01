<?php

namespace App\Models;

class DeliveryLog extends Model
{
    protected static string $table = 'delivery_events';

    public static function record(
        ?int $campaignId,
        ?int $smtpAccountId,
        string $email,
        string $eventType,
        array $metadata = []
    ): int {
        return self::create([
            'campaign_id' => $campaignId,
            'smtp_account_id' => $smtpAccountId,
            'recipient_email' => $email,
            'event_type' => $eventType,
            'meta_json' => json_encode($metadata),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
