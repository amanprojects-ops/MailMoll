<?php

namespace App\Models;

class AppSetting extends Model
{
    protected static string $table = 'app_settings';

    public static function get(string $key, mixed $default = null): mixed
    {
        $stmt = self::getDb()->prepare("SELECT value, type FROM app_settings WHERE key = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        if (!$row) {
            return $default;
        }

        return match ($row['type']) {
            'integer' => (int) $row['value'],
            'boolean' => (bool) $row['value'],
            'json' => json_decode($row['value'], true),
            default => $row['value'],
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $valStr = is_array($value) ? json_encode($value) : (string) $value;
        $stmt = self::getDb()->prepare("
            INSERT INTO app_settings (key, value, type, updated_at)
            VALUES (:key, :val, :type, CURRENT_TIMESTAMP)
            ON CONFLICT(key) DO UPDATE SET value = :val2, type = :type2, updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            ':key' => $key,
            ':val' => $valStr,
            ':type' => $type,
            ':val2' => $valStr,
            ':type2' => $type,
        ]);
    }
}
