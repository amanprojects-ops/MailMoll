<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Env;
use App\Services\QueueWorkerService;

Env::load(__DIR__ . '/../.env');

// Parse CLI options
$options = getopt('', ['daemon', 'sleep::', 'max-jobs::', 'max-time::']);

$isDaemon = isset($options['daemon']);
$sleepSeconds = (int)($options['sleep'] ?? 5);
$maxJobs = (int)($options['max-jobs'] ?? 100);
$maxExecutionTime = (int)($options['max-time'] ?? 55);

$startTime = time();
$worker = new QueueWorkerService();

echo "[" . date('Y-m-d H:i:s') . "] MailFlow Queue Worker started.\n";

do {
    try {
        $stats = $worker->processBatch(25);

        if ($stats['claimed'] > 0) {
            echo sprintf(
                "[%s] Processed batch: %d claimed | %d sent | %d retries | %d failed | %d suppressed\n",
                date('H:i:s'),
                $stats['claimed'],
                $stats['sent'],
                $stats['retry'],
                $stats['failed'],
                $stats['suppressed']
            );
        }

        // Check single-run timeout to prevent overlap on shared hosting crons
        if (!$isDaemon && (time() - $startTime) >= $maxExecutionTime) {
            echo "[" . date('Y-m-d H:i:s') . "] Reached max execution time window ({$maxExecutionTime}s). Exiting.\n";
            break;
        }

        if ($stats['claimed'] === 0) {
            if (!$isDaemon) {
                break; // Exit if nothing is pending in cron mode
            }
            sleep($sleepSeconds);
        }

    } catch (\Throwable $e) {
        echo "[ERROR " . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n";
        if (!$isDaemon) {
            exit(1);
        }
        sleep($sleepSeconds);
    }

} while ($isDaemon);

echo "[" . date('Y-m-d H:i:s') . "] MailMoll Queue Worker completed.\n";
