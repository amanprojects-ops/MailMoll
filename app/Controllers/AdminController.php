<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Models\Model;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\AppSetting;
use PDO;

class AdminController
{
    public function dashboard(): void
    {
        $db = Model::getDb();

        // 1. User metrics
        $totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $activeUsers = (int)$db->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
        $newUsersWeek = (int)$db->query("SELECT COUNT(*) FROM users WHERE created_at >= datetime('now', '-7 days')")->fetchColumn();

        // 2. Campaign & Queue metrics
        $totalCampaigns = (int)$db->query("SELECT COUNT(*) FROM campaigns")->fetchColumn();
        $totalQueued = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE status = 'pending'")->fetchColumn();
        $totalProcessing = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE status = 'processing'")->fetchColumn();
        $totalSent = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE status = 'sent'")->fetchColumn();
        $totalFailed = (int)$db->query("SELECT COUNT(*) FROM email_queue WHERE status = 'failed'")->fetchColumn();

        // 3. Storage and Database Telemetry
        $dbPath = config('database.path', __DIR__ . '/../../storage/database/mailmoll.sqlite');
        $dbSize = file_exists($dbPath) ? round(filesize($dbPath) / 1024 / 1024, 2) : 0;
        $diskFree = round(disk_free_space(__DIR__ . '/../../storage') / 1024 / 1024 / 1024, 2);
        $diskTotal = round(disk_total_space(__DIR__ . '/../../storage') / 1024 / 1024 / 1024, 2);

        // 4. Worker Heartbeat
        $workerHeartbeat = AppSetting::get('worker_last_heartbeat', 'Never');
        $workerActive = false;
        if ($workerHeartbeat && $workerHeartbeat !== 'Never') {
            $diff = time() - strtotime($workerHeartbeat);
            $workerActive = ($diff <= 180);
        }

        // 5. Recent security audit events
        $recentSecurity = $db->query("
            SELECT a.*, u.email as user_email 
            FROM audit_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.id DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        // 6. Recent users
        $recentUsers = $db->query("SELECT * FROM users ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/dashboard', [
            'title' => 'Executive Control Center - MailFlow Admin',
            'adminNav' => 'dashboard',
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'newUsersWeek' => $newUsersWeek,
            'totalCampaigns' => $totalCampaigns,
            'totalQueued' => $totalQueued,
            'totalProcessing' => $totalProcessing,
            'totalSent' => $totalSent,
            'totalFailed' => $totalFailed,
            'dbSize' => $dbSize,
            'diskFree' => $diskFree,
            'diskTotal' => $diskTotal,
            'workerActive' => $workerActive,
            'workerHeartbeat' => $workerHeartbeat,
            'recentSecurity' => $recentSecurity,
            'recentUsers' => $recentUsers,
        ], 'layouts/admin');
    }

    public function users(): void
    {
        $db = Model::getDb();
        $users = $db->query("
            SELECT u.*, 
                   s.plan as subscription_plan,
                   s.status as subscription_status,
                   (SELECT COUNT(*) FROM campaigns c WHERE 1=1) as campaign_count
            FROM users u
            LEFT JOIN subscriptions s ON u.id = s.user_id
            ORDER BY u.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/users', [
            'title' => 'User Management - MailFlow Admin',
            'adminNav' => 'users',
            'users' => $users,
        ], 'layouts/admin');
    }

    public function updateUserRole(int $id): void
    {
        $role = trim($_POST['role'] ?? 'operator');
        $status = trim($_POST['status'] ?? 'active');

        if (!in_array($role, ['admin', 'operator', 'viewer'])) {
            $role = 'operator';
        }
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $user = User::find($id);
        if ($user) {
            User::update($id, [
                'role' => $role,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            AuditLog::log('admin_updated_user', 'users', $id, ['role' => $role, 'status' => $status]);
            Session::setFlash('success', "Updated user '{$user['email']}' successfully.");
        }

        Response::redirect('/admin/users');
    }

    public function subscriptions(): void
    {
        $db = Model::getDb();
        $subscriptions = $db->query("
            SELECT s.*, u.name as user_name, u.email as user_email 
            FROM subscriptions s 
            JOIN users u ON s.user_id = u.id 
            ORDER BY s.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/subscriptions', [
            'title' => 'Subscriptions & Tiers - MailFlow Admin',
            'adminNav' => 'subscriptions',
            'subscriptions' => $subscriptions,
        ], 'layouts/admin');
    }

    public function systemHealth(): void
    {
        $db = Model::getDb();

        // SQLite Pragmas
        $walMode = $db->query("PRAGMA journal_mode")->fetchColumn();
        $pageCount = (int)$db->query("PRAGMA page_count")->fetchColumn();
        $pageSize = (int)$db->query("PRAGMA page_size")->fetchColumn();
        $dbSizeBytes = $pageCount * $pageSize;

        // PHP Environment
        $extensions = [
            'pdo_sqlite' => extension_loaded('pdo_sqlite'),
            'curl' => extension_loaded('curl'),
            'openssl' => extension_loaded('openssl'),
            'mbstring' => extension_loaded('mbstring'),
            'json' => extension_loaded('json'),
            'gd' => extension_loaded('gd'),
        ];

        // Storage Write Check
        $storageWritable = is_writable(__DIR__ . '/../../storage');
        $dbWritable = is_writable(__DIR__ . '/../../storage/database');
        $logsWritable = is_writable(__DIR__ . '/../../storage/logs');
        $backupsWritable = is_writable(__DIR__ . '/../../storage/backups');

        View::render('admin/system_health', [
            'title' => 'System Telemetry & Diagnostics - MailFlow Admin',
            'adminNav' => 'health',
            'walMode' => $walMode,
            'dbSizeBytes' => $dbSizeBytes,
            'extensions' => $extensions,
            'storageWritable' => $storageWritable,
            'dbWritable' => $dbWritable,
            'logsWritable' => $logsWritable,
            'backupsWritable' => $backupsWritable,
            'memoryLimit' => ini_get('memory_limit'),
            'maxExecution' => ini_get('max_execution_time'),
            'uploadMax' => ini_get('upload_max_filesize'),
        ], 'layouts/admin');
    }

    public function queue(): void
    {
        $db = Model::getDb();
        $queueItems = $db->query("
            SELECT q.*, s.name as smtp_name 
            FROM email_queue q 
            LEFT JOIN smtp_accounts s ON q.selected_smtp_id = s.id 
            ORDER BY q.id DESC LIMIT 100
        ")->fetchAll(PDO::FETCH_ASSOC);

        $counts = $db->query("
            SELECT status, COUNT(*) as cnt 
            FROM email_queue 
            GROUP BY status
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        View::render('admin/queue', [
            'title' => 'Global Queue Inspection - MailFlow Admin',
            'adminNav' => 'queue',
            'queueItems' => $queueItems,
            'counts' => $counts,
        ], 'layouts/admin');
    }

    public function emailLogs(): void
    {
        $db = Model::getDb();
        $attempts = $db->query("
            SELECT a.*, q.recipient_email, q.subject, s.name as smtp_name 
            FROM email_attempts a 
            LEFT JOIN email_queue q ON a.queue_id = q.id 
            LEFT JOIN smtp_accounts s ON a.smtp_account_id = s.id 
            ORDER BY a.id DESC LIMIT 100
        ")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/email_logs', [
            'title' => 'Global SMTP Logs - MailFlow Admin',
            'adminNav' => 'email_logs',
            'attempts' => $attempts,
        ], 'layouts/admin');
    }

    public function security(): void
    {
        $db = Model::getDb();
        $twoFaUsers = $db->query("
            SELECT t.*, u.name, u.email 
            FROM two_factor_auth t 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $passwordResets = $db->query("SELECT * FROM password_resets ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/security', [
            'title' => 'Security & 2FA Enforcement - MailFlow Admin',
            'adminNav' => 'security',
            'twoFaUsers' => $twoFaUsers,
            'passwordResets' => $passwordResets,
        ], 'layouts/admin');
    }

    public function auditLogs(): void
    {
        $db = Model::getDb();
        $auditLogs = $db->query("
            SELECT a.*, u.name as user_name, u.email as user_email 
            FROM audit_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.id DESC LIMIT 200
        ")->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/audit_logs', [
            'title' => 'Platform Audit Trail - MailFlow Admin',
            'adminNav' => 'audit_logs',
            'auditLogs' => $auditLogs,
        ], 'layouts/admin');
    }

    public function settings(): void
    {
        $settings = AppSetting::all();
        $settingsMap = [];
        foreach ($settings as $s) {
            $settingsMap[$s['key']] = $s['value'];
        }

        View::render('admin/settings', [
            'title' => 'Global Platform Settings - MailFlow Admin',
            'adminNav' => 'settings',
            'settings' => $settingsMap,
        ], 'layouts/admin');
    }

    public function updateSettings(): void
    {
        $allowed = ['app_name', 'default_smtp_group', 'queue_batch_size', 'worker_sleep_seconds', 'debug_mode'];
        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                AppSetting::set($key, trim($_POST[$key]));
            }
        }

        AuditLog::log('admin_updated_global_settings', 'app_settings', null);
        Session::setFlash('success', 'Global system settings updated successfully.');
        Response::redirect('/admin/settings');
    }

    public function backups(): void
    {
        $backupDir = __DIR__ . '/../../storage/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $files = glob("{$backupDir}/*.sqlite");
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => round(filesize($file) / 1024 / 1024, 2),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        View::render('admin/backups', [
            'title' => 'Database Snapshots & Backups - MailFlow Admin',
            'adminNav' => 'backups',
            'backups' => $backups,
        ], 'layouts/admin');
    }

    public function createBackup(): void
    {
        $dbPath = config('database.path', __DIR__ . '/../../storage/database/mailmoll.sqlite');
        $backupDir = __DIR__ . '/../../storage/backups';

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupFileName = 'mailflow_backup_' . date('Ymd_His') . '.sqlite';
        $target = "{$backupDir}/{$backupFileName}";

        if (file_exists($dbPath) && copy($dbPath, $target)) {
            AuditLog::log('database_backup_created', 'backups', null, ['filename' => $backupFileName]);
            Session::setFlash('success', "Database snapshot '{$backupFileName}' created successfully.");
        } else {
            Session::setFlash('error', "Failed to create database snapshot. Ensure directory permissions.");
        }

        Response::redirect('/admin/backups');
    }
}
