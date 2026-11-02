<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\AuditLog;
use App\Database\Connection;
use App\Services\ContactImportService;
use Exception;

class ContactController
{
    public function index(): void
    {
        $db = Connection::getInstance();

        $selectedList = isset($_GET['list_id']) ? (int)$_GET['list_id'] : null;

        if ($selectedList) {
            $contacts = $db->query("
                SELECT c.* FROM contacts c
                INNER JOIN contact_list_members m ON c.id = m.contact_id
                WHERE m.list_id = {$selectedList}
                ORDER BY c.id DESC
                LIMIT 500
            ")->fetchAll();
        } else {
            $contacts = Contact::all('id DESC', 500);
        }

        $lists = ContactList::allWithCounts();

        View::render('contacts/index', [
            'title' => 'Contacts & Audiences',
            'activeNav' => 'contacts',
            'contacts' => $contacts,
            'lists' => $lists,
            'selectedList' => $selectedList,
        ]);
    }

    public function store(): void
    {
        $validator = Validator::make($_POST, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/contacts');
        }

        $email = trim($_POST['email']);
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $listId = !empty($_POST['list_id']) ? (int)$_POST['list_id'] : null;

        $contactId = Contact::createOrUpdate($email, $firstName, $lastName, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        if ($listId) {
            Contact::attachToList($contactId, $listId);
        }

        AuditLog::log('add_contact', 'contacts', $contactId, ['email' => $email]);
        Session::setFlash('success', "Contact {$email} added successfully.");
        Response::redirect('/contacts');
    }

    public function storeList(): void
    {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            Session::setFlash('error', 'List name is required.');
            Response::redirect('/contacts');
        }

        $id = ContactList::create([
            'name' => $name,
            'description' => trim($_POST['description'] ?? ''),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLog::log('create_contact_list', 'contact_lists', $id, ['name' => $name]);
        Session::setFlash('success', "List '{$name}' created.");
        Response::redirect('/contacts');
    }

    /**
     * AJAX Endpoint: Inspect and preview file structure before importing.
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
            $service = new ContactImportService();
            $inspection = $service->inspect($tmpPath, $origName);
            Response::success('File inspected successfully.', $inspection);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    /**
     * Bulk Contact Import supporting .csv, .xlsx, .xls, .json, .txt with dedicated filtering.
     */
    public function import(): void
    {
        $fileKey = isset($_FILES['import_file']) ? 'import_file' : (isset($_FILES['csv_file']) ? 'csv_file' : null);

        if (!$fileKey || empty($_FILES[$fileKey]['tmp_name']) || !is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
            Session::setFlash('error', 'Please choose a valid file to import (.csv, .xlsx, .xls, .json, .txt).');
            Response::redirect('/contacts');
        }

        $tmpPath = $_FILES[$fileKey]['tmp_name'];
        $origName = $_FILES[$fileKey]['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        $allowedExts = ['csv', 'tsv', 'txt', 'json', 'xlsx', 'xlsm', 'xls'];
        if (!in_array($ext, $allowedExts, true)) {
            Session::setFlash('error', "Unsupported file format (.{$ext}). Allowed formats: .csv, .xlsx, .xls, .json, .txt");
            Response::redirect('/contacts');
        }

        $options = [
            'list_id' => !empty($_POST['list_id']) ? (int)$_POST['list_id'] : null,
            'new_list_name' => trim($_POST['new_list_name'] ?? ''),
            'email_column' => $_POST['email_column'] ?? 'auto',
            'first_name_column' => $_POST['first_name_column'] ?? 'auto',
            'last_name_column' => $_POST['last_name_column'] ?? 'auto',
            'full_name_column' => $_POST['full_name_column'] ?? 'auto',
            'has_header' => isset($_POST['has_header']) ? (bool)$_POST['has_header'] : true,
            'duplicate_mode' => in_array($_POST['duplicate_mode'] ?? '', ['skip', 'update'], true) ? $_POST['duplicate_mode'] : 'update',
            'skip_suppressed' => isset($_POST['skip_suppressed']) ? (bool)$_POST['skip_suppressed'] : true,
            'extract_regex' => isset($_POST['extract_regex']) ? (bool)$_POST['extract_regex'] : true,
        ];

        try {
            $service = new ContactImportService();
            $stats = $service->import($tmpPath, $origName, $options);

            $msg = "Import completed: {$stats['imported']} new contacts added";
            if ($stats['updated'] > 0) {
                $msg .= ", {$stats['updated']} updated";
            }
            if ($stats['skipped_duplicate'] > 0) {
                $msg .= ", {$stats['skipped_duplicate']} existing skipped";
            }
            if ($stats['skipped_suppressed'] > 0) {
                $msg .= ", {$stats['skipped_suppressed']} suppressed filtered";
            }
            if ($stats['skipped_invalid'] > 0) {
                $msg .= ", {$stats['skipped_invalid']} invalid skipped";
            }
            $msg .= '.';

            if (!empty($stats['list_name'])) {
                $msg .= " Assigned to list: '{$stats['list_name']}'.";
            }

            Session::setFlash('success', $msg);

            if (!empty($stats['list_id'])) {
                Response::redirect('/contacts?list_id=' . $stats['list_id']);
            } else {
                Response::redirect('/contacts');
            }
        } catch (Exception $e) {
            Session::setFlash('error', 'Import failed: ' . $e->getMessage());
            Response::redirect('/contacts');
        }
    }

    /**
     * Backward-compatible alias for importCsv route
     */
    public function importCsv(): void
    {
        $this->import();
    }

    /**
     * Download sample contact template files (csv, xlsx, json, txt)
     */
    public function downloadSample(?string $format = null): void
    {
        $format = $format ?: ($_GET['format'] ?? 'csv');
        $service = new ContactImportService();
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

    public function delete(int $id): void
    {
        Contact::delete($id);
        Session::setFlash('success', 'Contact deleted.');
        Response::redirect('/contacts');
    }

    public function deleteList(int $id): void
    {
        ContactList::delete($id);
        Session::setFlash('success', 'Contact list deleted.');
        Response::redirect('/contacts');
    }
}
