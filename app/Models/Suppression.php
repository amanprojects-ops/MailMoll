<?php

namespace App\Models;

class Suppression extends Model
{
    protected static string $table = 'suppressions';

    public static function add(string $email, string $reason = 'manual', ?int $campaignId = null): void
    {
        $email = strtolower(trim($email));
        $stmt = self::getDb()->prepare("SELECT COUNT(*) FROM suppressions WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() == 0) {
            self::create([
                'email' => $email,
                'reason' => $reason,
                'source_campaign_id' => $campaignId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
