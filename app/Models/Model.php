<?php

namespace App\Models;

use App\Database\Connection;
use PDO;

abstract class Model
{
    protected static string $table = '';

    public static function getDb(): PDO
    {
        return Connection::getInstance();
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $stmt = static::getDb()->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function all(string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM {$table} ORDER BY {$orderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = static::getDb()->query($sql);
        return $stmt->fetchAll();
    }

    public static function where(string $column, mixed $value, string $operator = '='): array
    {
        $table = static::$table;
        $stmt = static::getDb()->prepare("SELECT * FROM {$table} WHERE {$column} {$operator} :val");
        $stmt->execute([':val' => $value]);
        return $stmt->fetchAll();
    }

    public static function firstWhere(string $column, mixed $value, string $operator = '='): ?array
    {
        $table = static::$table;
        $stmt = static::getDb()->prepare("SELECT * FROM {$table} WHERE {$column} {$operator} :val LIMIT 1");
        $stmt->execute([':val' => $value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $table = static::$table;
        $fields = array_keys($data);
        $columns = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = static::getDb()->prepare($sql);

        $binds = [];
        foreach ($data as $key => $value) {
            $binds[':' . $key] = $value;
        }

        $stmt->execute($binds);
        return (int) static::getDb()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $table = static::$table;
        $setClauses = [];
        $binds = [':id' => $id];

        foreach ($data as $key => $val) {
            $setClauses[] = "{$key} = :{$key}";
            $binds[':' . $key] = $val;
        }

        $setSql = implode(', ', $setClauses);
        $sql = "UPDATE {$table} SET {$setSql} WHERE id = :id";
        $stmt = static::getDb()->prepare($sql);
        return $stmt->execute($binds);
    }

    public static function delete(int $id): bool
    {
        $table = static::$table;
        $stmt = static::getDb()->prepare("DELETE FROM {$table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function count(string $where = '', array $binds = []): int
    {
        $table = static::$table;
        $sql = "SELECT COUNT(*) FROM {$table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $stmt = static::getDb()->prepare($sql);
        $stmt->execute($binds);
        return (int) $stmt->fetchColumn();
    }

    public static function query(string $sql, array $binds = []): array
    {
        $stmt = static::getDb()->prepare($sql);
        $stmt->execute($binds);
        return $stmt->fetchAll();
    }
}
