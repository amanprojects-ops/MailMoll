<?php

namespace App\Models;

class SmtpGroup extends Model
{
    protected static string $table = 'smtp_groups';

    public static function getMembers(int $groupId): array
    {
        $sql = "
            SELECT a.* FROM smtp_accounts a
            INNER JOIN smtp_group_members m ON a.id = m.smtp_account_id
            WHERE m.group_id = :group_id
            ORDER BY a.priority DESC, a.id ASC
        ";
        $stmt = self::getDb()->prepare($sql);
        $stmt->execute([':group_id' => $groupId]);
        return $stmt->fetchAll();
    }

    public static function syncMembers(int $groupId, array $accountIds): void
    {
        $db = self::getDb();
        $db->beginTransaction();
        try {
            $del = $db->prepare("DELETE FROM smtp_group_members WHERE group_id = :group_id");
            $del->execute([':group_id' => $groupId]);

            $ins = $db->prepare("INSERT INTO smtp_group_members (group_id, smtp_account_id) VALUES (:group_id, :account_id)");
            foreach ($accountIds as $accId) {
                $ins->execute([':group_id' => $groupId, ':account_id' => (int)$accId]);
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function addMember(int $groupId, int $accountId): void
    {
        $stmt = self::getDb()->prepare("
            INSERT OR IGNORE INTO smtp_group_members (group_id, smtp_account_id) VALUES (:group_id, :account_id)
        ");
        $stmt->execute([':group_id' => $groupId, ':account_id' => $accountId]);
    }
}
