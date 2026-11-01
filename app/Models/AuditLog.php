<?php

namespace App\Models;

class AuditLog extends Model
{
    protected static string $table = 'audit_logs';

    public static function log(string $action, string $resourceType, ?int $resourceId = null, array $details = []): void
    {
        $userId = auth_user()['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        self::create([
            'user_id' => $userId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'details_json' => json_encode($details),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
