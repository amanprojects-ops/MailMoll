<?php

namespace App\Models;

use App\Services\CryptoService;

class SmtpAccount extends Model
{
    protected static string $table = 'smtp_accounts';

    public static function getDecryptedPassword(array $account): ?string
    {
        if (empty($account['password_encrypted'])) {
            return null;
        }
        $crypto = new CryptoService();
        return $crypto->decrypt($account['password_encrypted']);
    }

    public static function getActiveAccounts(): array
    {
        return self::where('status', 'active');
    }

    public static function getHourlyUsage(int $accountId, ?string $date = null, ?int $hour = null): int
    {
        $date = $date ?? gmdate('Y-m-d');
        $hour = $hour ?? (int)gmdate('G');

        $stmt = self::getDb()->prepare("
            SELECT sent_count FROM smtp_usage
            WHERE smtp_account_id = :id AND year_month_day = :date AND hour_slot = :hour
        ");
        $stmt->execute([':id' => $accountId, ':date' => $date, ':hour' => $hour]);
        return (int) $stmt->fetchColumn();
    }

    public static function getDailyUsage(int $accountId, ?string $date = null): int
    {
        $date = $date ?? gmdate('Y-m-d');

        $stmt = self::getDb()->prepare("
            SELECT SUM(sent_count) FROM smtp_usage
            WHERE smtp_account_id = :id AND year_month_day = :date
        ");
        $stmt->execute([':id' => $accountId, ':date' => $date]);
        return (int) $stmt->fetchColumn();
    }

    public static function incrementUsage(int $accountId, bool $success = true): void
    {
        $date = gmdate('Y-m-d');
        $hour = (int)gmdate('G');

        // Check if row exists
        $stmt = self::getDb()->prepare("
            SELECT id, sent_count, failed_count FROM smtp_usage
            WHERE smtp_account_id = :id AND year_month_day = :date AND hour_slot = :hour
        ");
        $stmt->execute([':id' => $accountId, ':date' => $date, ':hour' => $hour]);
        $row = $stmt->fetch();

        if ($row) {
            $field = $success ? 'sent_count' : 'failed_count';
            $update = self::getDb()->prepare("
                UPDATE smtp_usage SET {$field} = {$field} + 1 WHERE id = :id
            ");
            $update->execute([':id' => $row['id']]);
        } else {
            $sent = $success ? 1 : 0;
            $failed = $success ? 0 : 1;
            $insert = self::getDb()->prepare("
                INSERT INTO smtp_usage (smtp_account_id, year_month_day, hour_slot, sent_count, failed_count)
                VALUES (:id, :date, :hour, :sent, :failed)
            ");
            $insert->execute([':id' => $accountId, ':date' => $date, ':hour' => $hour, ':sent' => $sent, ':failed' => $failed]);
        }
    }
}
