<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Models\QueueJob;
use App\Models\AuditLog;
use App\Services\QueueWorkerService;
use App\Database\Connection;

class QueueController
{
    public function index(): void
    {
        $db = Connection::getInstance();

        $statusFilter = $_GET['status'] ?? 'all';
        $where = [];
        if ($statusFilter !== 'all') {
            $where[] = "q.status = " . $db->quote($statusFilter);
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

        $jobs = $db->query("
            SELECT q.*, c.title as campaign_title, s.name as smtp_name
            FROM email_queue q
            LEFT JOIN campaigns c ON q.campaign_id = c.id
            LEFT JOIN smtp_accounts s ON q.selected_smtp_id = s.id
            {$whereClause}
            ORDER BY q.id DESC
            LIMIT 250
        ")->fetchAll();

        // Status counts
        $counts = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'retry' THEN 1 ELSE 0 END) as retry,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM email_queue
        ")->fetch();

        View::render('queue/index', [
            'title' => 'Email Queue Monitor',
            'activeNav' => 'email_queue',
            'jobs' => $jobs,
            'counts' => $counts,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function retry(int $id): void
    {
        QueueJob::update($id, [
            'status' => 'pending',
            'attempts' => 0,
            'scheduled_at' => gmdate('Y-m-d H:i:s'),
            'locked_at' => null,
            'locked_by' => null,
            'last_error' => null,
        ]);

        AuditLog::log('retry_queue_job', 'email_queue', $id);
        Session::setFlash('success', "Job #{$id} reset to pending status.");
        Response::redirect('/queue');
    }

    public function cancel(int $id): void
    {
        QueueJob::update($id, [
            'status' => 'cancelled',
            'locked_at' => null,
            'locked_by' => null,
        ]);

        AuditLog::log('cancel_queue_job', 'email_queue', $id);
        Session::setFlash('success', "Job #{$id} cancelled.");
        Response::redirect('/queue');
    }

    public function clearFailed(): void
    {
        $db = Connection::getInstance();
        $deleted = $db->exec("
            DELETE FROM email_queue 
            WHERE status IN ('failed', 'cancelled') 
               OR (status = 'processing' AND (locked_at < datetime('now', '-5 minutes') OR locked_at IS NULL))
        ");
        AuditLog::log('clear_failed_queue', 'email_queue', null, ['deleted' => $deleted]);
        Session::setFlash('success', "Cleared {$deleted} failed, cancelled, and timed-out queue jobs.");
        Response::redirect('/queue');
    }

    public function resetStuck(): void
    {
        $db = Connection::getInstance();
        $updated = $db->exec("
            UPDATE email_queue 
            SET status = 'pending', locked_at = NULL, locked_by = NULL 
            WHERE status = 'processing' AND (locked_at < datetime('now', '-5 minutes') OR locked_at IS NULL)
        ");
        AuditLog::log('reset_stuck_queue', 'email_queue', null, ['updated' => $updated]);
        Session::setFlash('success', "Reset {$updated} stuck processing jobs back to pending.");
        Response::redirect('/queue');
    }

    public function purgeAll(): void
    {
        $db = Connection::getInstance();
        $deleted = $db->exec("DELETE FROM email_queue");
        AuditLog::log('purge_all_queue', 'email_queue', null, ['deleted' => $deleted]);
        Session::setFlash('success', "Purged entire email queue ({$deleted} jobs deleted).");
        Response::redirect('/queue');
    }

    public function triggerWorker(): void
    {
        $worker = new QueueWorkerService('web-trigger-' . session_id());
        $stats = $worker->processBatch(25);

        Response::json([
            'success' => true,
            'message' => sprintf(
                "Processed batch: %d claimed, %d sent, %d retries, %d failed.",
                $stats['claimed'],
                $stats['sent'],
                $stats['retry'],
                $stats['failed']
            ),
            'stats' => $stats,
        ]);
    }
}
