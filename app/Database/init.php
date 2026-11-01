<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Env;
use App\Database\Migration;
use App\Database\Seeder;

Env::load(__DIR__ . '/../../.env');

echo "========================================\n";
echo " MailMoll Database Initializer & Migrator\n";
echo "========================================\n\n";

try {
    $migration = new Migration();
    $applied = $migration->run();

    if (empty($applied)) {
        echo "[INFO] Database schema is already up to date.\n";
    } else {
        foreach ($applied as $m) {
            echo "[SUCCESS] Applied migration: {$m}\n";
        }
    }

    echo "[INFO] Running seeders...\n";
    $seeder = new Seeder();
    $seeder->seed();
    echo "[SUCCESS] Seeding completed.\n\n";
    echo "Default Admin Credentials:\n";
    echo "  Email:    admin@mailmoll.local\n";
    echo "  Password: Admin@12345\n";
    echo "========================================\n";

} catch (\Throwable $e) {
    echo "[ERROR] Initialization failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
