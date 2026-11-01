<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\AuditLog;
use App\Database\Connection;

class SettingsController
{
    public function index(): void
    {
        $settings = [
            'app_title' => AppSetting::get('app_title', 'MailMoll'),
            'company_name' => AppSetting::get('company_name', 'MailMoll Enterprise'),
            'default_from_email' => AppSetting::get('default_from_email', 'no-reply@mailmoll.local'),
            'default_from_name' => AppSetting::get('default_from_name', 'MailMoll System'),
            'max_retry_attempts' => AppSetting::get('max_retry_attempts', 3),
            'batch_size' => AppSetting::get('batch_size', 50),
            'enable_dark_mode' => AppSetting::get('enable_dark_mode', false),
            'worker_heartbeat' => AppSetting::get('worker_heartbeat'),
        ];

        View::render('settings/index', [
            'title' => 'System Settings',
            'activeNav' => 'system_settings',
            'settings' => $settings,
        ]);
    }

    public function update(): void
    {
        $fields = ['app_title', 'company_name', 'default_from_email', 'default_from_name', 'max_retry_attempts', 'batch_size'];

        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                AppSetting::set($f, trim($_POST[$f]));
            }
        }

        AppSetting::set('enable_dark_mode', isset($_POST['enable_dark_mode']) ? '1' : '0', 'boolean');

        AuditLog::log('update_settings', 'app_settings');
        Session::setFlash('success', 'System settings saved successfully.');
        Response::redirect('/settings');
    }

    public function profile(): void
    {
        $user = auth_user();
        View::render('profile/index', [
            'title' => 'User Profile',
            'activeNav' => 'profile',
            'user' => $user,
        ]);
    }

    public function updateProfile(): void
    {
        $user = auth_user();
        $userId = (int)$user['id'];

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/profile');
        }

        $update = [
            'name' => trim($_POST['name']),
            'email' => strtolower(trim($_POST['email'])),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($_POST['new_password'])) {
            if (strlen($_POST['new_password']) < 6) {
                Session::setFlash('error', 'New password must be at least 6 characters.');
                Response::redirect('/profile');
            }
            $update['password_hash'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        }

        User::update($userId, $update);

        // Refresh session
        $freshUser = User::find($userId);
        unset($freshUser['password_hash']);
        Session::set('user', $freshUser);

        AuditLog::log('update_profile', 'users', $userId);
        Session::setFlash('success', 'Profile updated successfully.');
        Response::redirect('/profile');
    }

    public function backup(): void
    {
        $dbPath = config('database.connections.sqlite.database');
        if (!file_exists($dbPath)) {
            Session::setFlash('error', 'Database file not found.');
            Response::redirect('/settings');
        }

        $backupFileName = 'mailmoll-backup-' . date('Y-m-d_His') . '.sqlite';
        $backupDir = storage_path('backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $dest = $backupDir . DIRECTORY_SEPARATOR . $backupFileName;
        copy($dbPath, $dest);

        AuditLog::log('create_backup', 'database', null, ['filename' => $backupFileName]);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $backupFileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($dest));
        readfile($dest);
        exit;
    }
}
