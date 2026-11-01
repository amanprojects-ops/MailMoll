<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Env;
use App\Database\Connection;

Env::load(__DIR__ . '/../.env');

echo "[" . date('Y-m-d H:i:s') . "] Starting MailMoll cleanup tasks...\n";

try {
    $db = Connection::getInstance();

    // 1. Release stale queue locks older than 15 minutes
    $staleLocks = $db->exec("
        UPDATE email_queue 
        SET status = 'pending', locked_at = NULL, locked_by = NULL 
        WHERE status = 'processing' AND locked_at < datetime('now', '-15 minutes')
    ");
    echo "[INFO] Released {$staleLocks} stale job locks.\n";

    // 2. Prune old audit logs older than 90 days
    $prunedAudit = $db->exec("
        DELETE FROM audit_logs WHERE created_at < datetime('now', '-90 days')
    ");
    echo "[INFO] Pruned {$prunedAudit} old audit logs.\n";

    // 3. VACUUM SQLite database if needed to reclaim space
    $db->exec("PRAGMA optimize;");
    echo "[INFO] SQLite pragmas optimized successfully.\n";

} catch (\Throwable $e) {
    echo "[ERROR] Cleanup failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Cleanup finished.\n";
