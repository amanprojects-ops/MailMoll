<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Models\AuditLog;
use App\Database\Connection;

class LogController
{
    public function deliveryLogs(): void
    {
        $db = Connection::getInstance();

        $logs = $db->query("
            SELECT e.*, c.title as campaign_title, s.name as smtp_name
            FROM delivery_events e
            LEFT JOIN campaigns c ON e.campaign_id = c.id
            LEFT JOIN smtp_accounts s ON e.smtp_account_id = s.id
            ORDER BY e.id DESC
            LIMIT 300
        ")->fetchAll();

        View::render('logs/delivery', [
            'title' => 'Delivery Logs',
            'activeNav' => 'delivery_logs',
            'logs' => $logs,
        ]);
    }

    public function auditLogs(): void
    {
        $db = Connection::getInstance();

        $logs = $db->query("
            SELECT a.*, u.name as user_name, u.email as user_email
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.id DESC
            LIMIT 200
        ")->fetchAll();

        View::render('logs/audit', [
            'title' => 'System Audit Logs',
            'activeNav' => 'system_settings',
            'logs' => $logs,
        ]);
    }
}
