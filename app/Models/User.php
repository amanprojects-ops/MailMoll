<?php

namespace App\Models;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return self::firstWhere('email', strtolower(trim($email)));
    }

    public static function recordLogin(int $userId): void
    {
        self::update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
