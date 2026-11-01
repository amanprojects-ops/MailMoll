<?php

namespace App\Database;

use PDO;

class Migration
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration TEXT NOT NULL UNIQUE,
                batch INTEGER NOT NULL,
                applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function run(): array
    {
        $applied = [];
        $stmt = $this->db->query("SELECT migration FROM migrations");
        $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $migrationDir = __DIR__ . '/migrations';
        $files = glob("{$migrationDir}/*.sql");
        sort($files);

        // Get max batch
        $batch = (int) $this->db->query("SELECT MAX(batch) FROM migrations")->fetchColumn() + 1;

        foreach ($files as $file) {
            $name = basename($file);
            if (!in_array($name, $executed)) {
                $sql = file_get_contents($file);
                
                $this->db->beginTransaction();
                try {
                    $this->db->exec($sql);
                    $ins = $this->db->prepare("INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)");
                    $ins->execute([':migration' => $name, ':batch' => $batch]);
                    $this->db->commit();
                    $applied[] = $name;
                } catch (\Throwable $e) {
                    $this->db->rollBack();
                    throw new \RuntimeException("Migration {$name} failed: " . $e->getMessage());
                }
            }
        }

        return $applied;
    }
}
