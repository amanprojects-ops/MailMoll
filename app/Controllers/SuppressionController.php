<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\Suppression;
use App\Models\Contact;
use App\Models\AuditLog;
use App\Database\Connection;

class SuppressionController
{
    public function index(): void
    {
        $suppressions = Suppression::all('id DESC');

        View::render('suppressions/index', [
            'title' => 'Suppression & Unsubscribe List',
            'activeNav' => 'suppressions',
            'suppressions' => $suppressions,
        ]);
    }

    public function store(): void
    {
        $validator = Validator::make($_POST, [
            'email' => 'required|email',
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/suppressions');
        }

        $email = strtolower(trim($_POST['email']));
        Suppression::add($email, $_POST['reason']);

        // Update contact status if exists
        $contact = Contact::findByEmail($email);
        if ($contact) {
            Contact::update((int)$contact['id'], ['status' => 'unsubscribed']);
        }

        AuditLog::log('manual_suppression', 'suppressions', null, ['email' => $email, 'reason' => $_POST['reason']]);
        Session::setFlash('success', "Suppressed email {$email}.");
        Response::redirect('/suppressions');
    }

    public function delete(int $id): void
    {
        $suppression = Suppression::find($id);
        if ($suppression) {
            Suppression::delete($id);
            AuditLog::log('remove_suppression', 'suppressions', $id, ['email' => $suppression['email']]);
            Session::setFlash('success', "Removed {$suppression['email']} from suppression list.");
        }
        Response::redirect('/suppressions');
    }

    /**
     * Public Unsubscribe Handler (RFC-8058 compliant)
     */
    public function handleUnsubscribe(): void
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';

        if (empty($token)) {
            View::render('auth/unsubscribe_result', [
                'success' => false,
                'message' => 'Invalid or missing unsubscribe token.',
            ], 'layouts/auth');
            return;
        }

        $db = Connection::getInstance();
        $stmt = $db->prepare("SELECT * FROM unsubscribe_tokens WHERE token = :token LIMIT 1");
        $stmt->execute([':token' => $token]);
        $record = $stmt->fetch();

        if (!$record) {
            View::render('auth/unsubscribe_result', [
                'success' => false,
                'message' => 'This unsubscribe link is invalid or has expired.',
            ], 'layouts/auth');
            return;
        }

        $email = $record['email'];
        $campaignId = $record['campaign_id'];

        // Add to suppressions
        Suppression::add($email, 'unsubscribe', $campaignId ? (int)$campaignId : null);

        // Update contact status
        $contact = Contact::findByEmail($email);
        if ($contact) {
            Contact::update((int)$contact['id'], ['status' => 'unsubscribed']);
        }

        // Mark token used
        $up = $db->prepare("UPDATE unsubscribe_tokens SET used_at = CURRENT_TIMESTAMP WHERE id = :id");
        $up->execute([':id' => $record['id']]);

        // If one-click POST from mail client
        if (isset($_POST['List-Unsubscribe']) && $_POST['List-Unsubscribe'] === 'One-Click') {
            Response::json(['status' => 'unsubscribed']);
        }

        View::render('auth/unsubscribe_result', [
            'success' => true,
            'email' => $email,
            'message' => 'You have been successfully removed from our mailing list.',
        ], 'layouts/auth');
    }
}
