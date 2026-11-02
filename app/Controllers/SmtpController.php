<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\SmtpAccount;
use App\Models\SmtpGroup;
use App\Models\AuditLog;
use App\Services\CryptoService;
use App\Services\SmtpManagerService;
use App\Services\SmtpImportService;
use Exception;

class SmtpController
{
    private CryptoService $crypto;
    private SmtpManagerService $smtpManager;

    public function __construct()
    {
        $this->crypto = new CryptoService();
        $this->smtpManager = new SmtpManagerService();
    }

    public function index(): void
    {
        $accounts = SmtpAccount::all('priority DESC, id ASC');
        foreach ($accounts as &$acc) {
            $acc['daily_used'] = SmtpAccount::getDailyUsage((int)$acc['id']);
            $acc['hourly_used'] = SmtpAccount::getHourlyUsage((int)$acc['id']);
            $acc['daily_percent'] = $acc['daily_limit'] > 0 ? min(100, round(($acc['daily_used'] / $acc['daily_limit']) * 100)) : 0;
            $acc['hourly_percent'] = $acc['hourly_limit'] > 0 ? min(100, round(($acc['hourly_used'] / $acc['hourly_limit']) * 100)) : 0;
        }

        $groups = SmtpGroup::all();
        foreach ($groups as &$grp) {
            $grp['members'] = SmtpGroup::getMembers((int)$grp['id']);
        }

        View::render('smtp/index', [
            'title' => 'SMTP Accounts & Groups',
            'activeNav' => 'smtp_accounts',
            'accounts' => $accounts,
            'groups' => $groups,
        ]);
    }

    public function store(): void
    {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:3',
            'host' => 'required',
            'port' => 'required|integer',
            'encryption' => 'required|in:tls,ssl,starttls,none',
            'username' => 'required',
            'password' => 'required',
            'from_email' => 'required|email',
            'from_name' => 'required',
            'daily_limit' => 'required|integer|min:0',
            'hourly_limit' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/smtp');
        }

        $encryptedPassword = $this->crypto->encrypt($_POST['password']);

        $id = SmtpAccount::create([
            'name' => trim($_POST['name']),
            'provider' => $_POST['provider'] ?? 'generic',
            'host' => trim($_POST['host']),
            'port' => (int)$_POST['port'],
            'encryption' => $_POST['encryption'],
            'auth_type' => strtoupper($_POST['auth_type'] ?? 'LOGIN'),
            'username' => trim($_POST['username']),
            'password_encrypted' => $encryptedPassword,
            'from_email' => trim($_POST['from_email']),
            'from_name' => trim($_POST['from_name']),
            'reply_to' => !empty($_POST['reply_to']) ? trim($_POST['reply_to']) : null,
            'daily_limit' => (int)$_POST['daily_limit'],
            'hourly_limit' => (int)$_POST['hourly_limit'],
            'priority' => (int)($_POST['priority'] ?? 10),
            'weight' => (int)($_POST['weight'] ?? 1),
            'status' => $_POST['status'] ?? 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('create_smtp_account', 'smtp_accounts', $id, ['name' => $_POST['name']]);
        Session::setFlash('success', "SMTP Account '{$_POST['name']}' created successfully.");
        Response::redirect('/smtp');
    }

    public function update(int $id): void
    {
        $account = SmtpAccount::find($id);
        if (!$account) {
            Session::setFlash('error', 'SMTP account not found.');
            Response::redirect('/smtp');
        }

        $validator = Validator::make($_POST, [
            'name' => 'required|min:3',
            'host' => 'required',
            'port' => 'required|integer',
            'encryption' => 'required|in:tls,ssl,starttls,none',
            'username' => 'required',
            'from_email' => 'required|email',
            'from_name' => 'required',
            'daily_limit' => 'required|integer|min:0',
            'hourly_limit' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/smtp');
        }

        $updateData = [
            'name' => trim($_POST['name']),
            'provider' => $_POST['provider'] ?? $account['provider'],
            'host' => trim($_POST['host']),
            'port' => (int)$_POST['port'],
            'encryption' => $_POST['encryption'],
            'auth_type' => strtoupper($_POST['auth_type'] ?? $account['auth_type'] ?? 'LOGIN'),
            'username' => trim($_POST['username']),
            'from_email' => trim($_POST['from_email']),
            'from_name' => trim($_POST['from_name']),
            'reply_to' => !empty($_POST['reply_to']) ? trim($_POST['reply_to']) : null,
            'daily_limit' => (int)$_POST['daily_limit'],
            'hourly_limit' => (int)$_POST['hourly_limit'],
            'priority' => (int)($_POST['priority'] ?? $account['priority']),
            'weight' => (int)($_POST['weight'] ?? $account['weight']),
            'status' => $_POST['status'] ?? $account['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $updateData['password_encrypted'] = $this->crypto->encrypt($_POST['password']);
        }

        SmtpAccount::update($id, $updateData);
        AuditLog::log('update_smtp_account', 'smtp_accounts', $id, ['name' => $_POST['name']]);

        Session::setFlash('success', "SMTP Account '{$_POST['name']}' updated.");
        Response::redirect('/smtp');
    }

    public function toggle(int $id): void
    {
        $account = SmtpAccount::find($id);
        if ($account) {
            $newStatus = $account['status'] === 'active' ? 'inactive' : 'active';
            SmtpAccount::update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
            AuditLog::log('toggle_smtp_status', 'smtp_accounts', $id, ['new_status' => $newStatus]);
            Session::setFlash('success', "SMTP Account '{$account['name']}' is now {$newStatus}.");
        }
        Response::redirect('/smtp');
    }

    public function delete(int $id): void
    {
        $account = SmtpAccount::find($id);
        if ($account) {
            SmtpAccount::delete($id);
            AuditLog::log('delete_smtp_account', 'smtp_accounts', $id, ['name' => $account['name']]);
            Session::setFlash('success', "SMTP Account '{$account['name']}' deleted.");
        }
        Response::redirect('/smtp');
    }

    public function test(int $id): void
    {
        $account = SmtpAccount::find($id);
        if (!$account) {
            Response::error('SMTP Account not found.', 404);
        }

        $result = $this->smtpManager->testConnection($account);

        // Update last test results
        SmtpAccount::update($id, [
            'last_tested_at' => date('Y-m-d H:i:s'),
            'last_test_result' => $result['success'] ? 'SUCCESS' : 'FAILED',
            'consecutive_failures' => $result['success'] ? 0 : ((int)$account['consecutive_failures'] + 1),
        ]);

        Response::json([
            'success' => $result['success'],
            'message' => $result['message'],
            'debug' => $result['debug'],
        ]);
    }

    public function storeGroup(): void
    {
        $name = trim($_POST['name'] ?? '');
        $strategy = $_POST['routing_strategy'] ?? 'round_robin';
        $accountIds = $_POST['account_ids'] ?? [];

        if (empty($name)) {
            Session::setFlash('error', 'Group name is required.');
            Response::redirect('/smtp');
        }

        $groupId = SmtpGroup::create([
            'name' => $name,
            'routing_strategy' => $strategy,
            'description' => trim($_POST['description'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (!empty($accountIds)) {
            SmtpGroup::syncMembers($groupId, $accountIds);
        }

        AuditLog::log('create_smtp_group', 'smtp_groups', $groupId, ['name' => $name]);
        Session::setFlash('success', "SMTP Group '{$name}' created.");
        Response::redirect('/smtp');
    }

    public function updateGroup(int $id): void
    {
        $group = SmtpGroup::find($id);
        if (!$group) {
            Session::setFlash('error', 'Group not found.');
            Response::redirect('/smtp');
        }

        $name = trim($_POST['name'] ?? '');
        $strategy = $_POST['routing_strategy'] ?? 'round_robin';
        $accountIds = $_POST['account_ids'] ?? [];

        SmtpGroup::update($id, [
            'name' => $name,
            'routing_strategy' => $strategy,
            'description' => trim($_POST['description'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        SmtpGroup::syncMembers($id, $accountIds);
        AuditLog::log('update_smtp_group', 'smtp_groups', $id, ['name' => $name]);

        Session::setFlash('success', "SMTP Group '{$name}' updated.");
        Response::redirect('/smtp');
    }

    public function deleteGroup(int $id): void
    {
        $group = SmtpGroup::find($id);
        if ($group) {
            SmtpGroup::delete($id);
            AuditLog::log('delete_smtp_group', 'smtp_groups', $id, ['name' => $group['name']]);
            Session::setFlash('success', "SMTP Group '{$group['name']}' deleted.");
        }
        Response::redirect('/smtp');
    }

    /**
     * Download sample SMTP template file in CSV, XLSX, JSON, or TXT.
     */
    public function downloadSample(?string $format = null): void
    {
        $format = $format ?: ($_GET['format'] ?? 'csv');
        $service = new SmtpImportService();
        $sample = $service->generateSample($format);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $sample['mime']);
        header('Content-Disposition: attachment; filename="' . $sample['filename'] . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($sample['content']));

        if (ob_get_level() > 0) {
            ob_clean();
        }

        echo $sample['content'];
        exit;
    }

    /**
     * AJAX Endpoint: Inspect and preview uploaded SMTP file structure.
     */
    public function previewImport(): void
    {
        $fileKey = isset($_FILES['import_file']) ? 'import_file' : (isset($_FILES['csv_file']) ? 'csv_file' : null);

        if (!$fileKey || empty($_FILES[$fileKey]['tmp_name']) || !is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
            Response::error('Please upload a valid file to preview.');
        }

        $tmpPath = $_FILES[$fileKey]['tmp_name'];
        $origName = $_FILES[$fileKey]['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        $allowedExts = ['csv', 'tsv', 'txt', 'json', 'xlsx', 'xlsm', 'xls'];
        if (!in_array($ext, $allowedExts, true)) {
            Response::error('Unsupported file format. Supported: .csv, .xlsx, .xls, .json, .txt');
        }

        try {
            $service = new SmtpImportService();
            $inspection = $service->inspect($tmpPath, $origName);
            Response::success('SMTP file inspected successfully.', $inspection);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Bulk SMTP Import supporting .csv, .xlsx, .xls, .json, .txt with auto-mapping.
     */
    public function import(): void
    {
        $fileKey = isset($_FILES['import_file']) ? 'import_file' : (isset($_FILES['csv_file']) ? 'csv_file' : null);

        if (!$fileKey || empty($_FILES[$fileKey]['tmp_name']) || !is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
            Session::setFlash('error', 'Please select a valid SMTP list file (.csv, .xlsx, .xls, .json, .txt).');
            Response::redirect('/smtp');
        }

        $tmpPath = $_FILES[$fileKey]['tmp_name'];
        $origName = $_FILES[$fileKey]['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        $allowedExts = ['csv', 'tsv', 'txt', 'json', 'xlsx', 'xlsm', 'xls'];
        if (!in_array($ext, $allowedExts, true)) {
            Session::setFlash('error', "Unsupported format (.{$ext}). Allowed: .csv, .xlsx, .xls, .json, .txt");
            Response::redirect('/smtp');
        }

        $options = [
            'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
            'new_group_name' => trim($_POST['new_group_name'] ?? ''),
            'name_column' => $_POST['name_column'] ?? 'auto',
            'host_column' => $_POST['host_column'] ?? 'auto',
            'port_column' => $_POST['port_column'] ?? 'auto',
            'encryption_column' => $_POST['encryption_column'] ?? 'auto',
            'auth_type_column' => $_POST['auth_type_column'] ?? 'auto',
            'username_column' => $_POST['username_column'] ?? 'auto',
            'password_column' => $_POST['password_column'] ?? 'auto',
            'from_email_column' => $_POST['from_email_column'] ?? 'auto',
            'from_name_column' => $_POST['from_name_column'] ?? 'auto',
            'daily_limit_column' => $_POST['daily_limit_column'] ?? 'auto',
            'hourly_limit_column' => $_POST['hourly_limit_column'] ?? 'auto',
            'default_port' => !empty($_POST['default_port']) ? (int)$_POST['default_port'] : 587,
            'default_encryption' => !empty($_POST['default_encryption']) ? $_POST['default_encryption'] : 'tls',
            'default_auth_type' => !empty($_POST['default_auth_type']) ? $_POST['default_auth_type'] : 'LOGIN',
            'default_daily_limit' => isset($_POST['default_daily_limit']) ? (int)$_POST['default_daily_limit'] : 500,
            'default_hourly_limit' => isset($_POST['default_hourly_limit']) ? (int)$_POST['default_hourly_limit'] : 100,
            'duplicate_mode' => in_array($_POST['duplicate_mode'] ?? '', ['skip', 'update'], true) ? $_POST['duplicate_mode'] : 'update',
            'has_header' => isset($_POST['has_header']) ? (bool)$_POST['has_header'] : true,
        ];

        try {
            $service = new SmtpImportService();
            $stats = $service->import($tmpPath, $origName, $options);

            $msg = "SMTP Import completed: {$stats['imported']} new accounts added";
            if ($stats['updated'] > 0) {
                $msg .= ", {$stats['updated']} updated";
            }
            if ($stats['skipped_duplicate'] > 0) {
                $msg .= ", {$stats['skipped_duplicate']} existing skipped";
            }
            if ($stats['skipped_invalid'] > 0) {
                $msg .= ", {$stats['skipped_invalid']} invalid rows skipped";
            }
            $msg .= '.';

            if (!empty($stats['group_name'])) {
                $msg .= " Assigned to Routing Group: '{$stats['group_name']}'.";
            }

            Session::setFlash('success', $msg);
            Response::redirect('/smtp');
        } catch (Exception $e) {
            Session::setFlash('error', 'SMTP Import failed: ' . $e->getMessage());
            Response::redirect('/smtp');
        }
    }
}
