<?php

namespace App\Models;

class ContactList extends Model
{
    protected static string $table = 'contact_lists';

    public static function getContactCount(int $listId): int
    {
        $stmt = self::getDb()->prepare("
            SELECT COUNT(*) FROM contact_list_members WHERE list_id = :list_id
        ");
        $stmt->execute([':list_id' => $listId]);
        return (int) $stmt->fetchColumn();
    }

    public static function allWithCounts(): array
    {
        $sql = "
            SELECT l.*, COUNT(m.contact_id) as total_contacts
            FROM contact_lists l
            LEFT JOIN contact_list_members m ON l.id = m.list_id
            GROUP BY l.id
            ORDER BY l.id DESC
        ";
        return self::getDb()->query($sql)->fetchAll();
    }
}
