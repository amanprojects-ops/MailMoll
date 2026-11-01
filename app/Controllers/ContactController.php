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

    public function importCsv(): void
    {
        $listId = (int)($_POST['list_id'] ?? 0);
        if (!$listId) {
            Session::setFlash('error', 'Please select a destination list for imported contacts.');
            Response::redirect('/contacts');
        }

        if (empty($_FILES['csv_file']['tmp_name']) || !is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            Session::setFlash('error', 'Please upload a valid CSV file.');
            Response::redirect('/contacts');
        }

        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$file) {
            Session::setFlash('error', 'Failed to open uploaded file.');
            Response::redirect('/contacts');
        }

        $header = fgetcsv($file);
        if (!$header) {
            Session::setFlash('error', 'CSV file appears to be empty.');
            fclose($file);
            Response::redirect('/contacts');
        }

        // Normalize header
        $emailIdx = -1;
        $firstIdx = -1;
        $lastIdx = -1;

        foreach ($header as $idx => $colName) {
            $col = strtolower(trim($colName));
            if (str_contains($col, 'email')) {
                $emailIdx = $idx;
            } elseif (str_contains($col, 'first') || $col === 'name' || $col === 'fname') {
                $firstIdx = $idx;
            } elseif (str_contains($col, 'last') || $col === 'lname' || $col === 'surname') {
                $lastIdx = $idx;
            }
        }

        if ($emailIdx === -1) {
            Session::setFlash('error', 'Could not locate an "email" column in CSV header.');
            fclose($file);
            Response::redirect('/contacts');
        }

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($file)) !== false) {
            $email = isset($row[$emailIdx]) ? trim($row[$emailIdx]) : '';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            $first = ($firstIdx !== -1 && isset($row[$firstIdx])) ? trim($row[$firstIdx]) : null;
            $last = ($lastIdx !== -1 && isset($row[$lastIdx])) ? trim($row[$lastIdx]) : null;

            $contactId = Contact::createOrUpdate($email, $first, $last);
            Contact::attachToList($contactId, $listId);
            $imported++;
        }

        fclose($file);

        AuditLog::log('import_contacts_csv', 'contact_lists', $listId, ['imported' => $imported, 'skipped' => $skipped]);
        Session::setFlash('success', "Import completed: {$imported} contacts imported ({$skipped} skipped).");
        Response::redirect('/contacts?list_id=' . $listId);
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
