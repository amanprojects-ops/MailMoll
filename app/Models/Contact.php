<?php

namespace App\Models;

class Contact extends Model
{
    protected static string $table = 'contacts';

    public static function isSuppressed(string $email): bool
    {
        $stmt = self::getDb()->prepare("SELECT COUNT(*) FROM suppressions WHERE email = :email");
        $stmt->execute([':email' => strtolower(trim($email))]);
        return $stmt->fetchColumn() > 0;
    }

    public static function findByEmail(string $email): ?array
    {
        return self::firstWhere('email', strtolower(trim($email)));
    }

    public static function createOrUpdate(string $email, ?string $firstName = null, ?string $lastName = null, ?string $ip = null): int
    {
        $email = strtolower(trim($email));
        $contact = self::findByEmail($email);

        if ($contact) {
            self::update($contact['id'], [
                'first_name' => $firstName ?: $contact['first_name'],
                'last_name' => $lastName ?: $contact['last_name'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return (int) $contact['id'];
        }

        return self::create([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'status' => 'subscribed',
            'opt_in_at' => date('Y-m-d H:i:s'),
            'opt_in_ip' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function attachToList(int $contactId, int $listId): void
    {
        $check = self::getDb()->prepare("
            SELECT COUNT(*) FROM contact_list_members WHERE list_id = :list_id AND contact_id = :contact_id
        ");
        $check->execute([':list_id' => $listId, ':contact_id' => $contactId]);
        if ($check->fetchColumn() == 0) {
            $ins = self::getDb()->prepare("
                INSERT INTO contact_list_members (list_id, contact_id) VALUES (:list_id, :contact_id)
            ");
            $ins->execute([':list_id' => $listId, ':contact_id' => $contactId]);
        }
    }
}
