<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Models\Model;
use App\Models\AuditLog;
use PDO;

class ApiKeyController
{
    public function index(): void
    {
        $user = auth_user();
        $db = Model::getDb();
        $stmt = $db->prepare("SELECT * FROM api_keys WHERE user_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $user['id']]);
        $apiKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

        View::render('api_keys/index', [
            'title' => 'REST API Keys - MailFlow',
            'activeNav' => 'api_keys',
            'apiKeys' => $apiKeys,
        ]);
    }

    public function store(): void
    {
        $user = auth_user();
        $name = trim($_POST['name'] ?? 'Production Key');
        if (empty($name)) {
            $name = 'Secret Key (' . date('M j, Y') . ')';
        }

        // Generate key: prefix + random 32 bytes hex
        $rawSecret = bin2hex(random_bytes(24));
        $fullKey = 'mflw_live_' . $rawSecret;
        $prefix = substr($fullKey, 0, 14) . '...';
        $keyHash = hash('sha256', $fullKey);

        $db = Model::getDb();
        $stmt = $db->prepare("
            INSERT INTO api_keys (user_id, name, key_prefix, key_hash, permissions_json, created_at)
            VALUES (:uid, :name, :prefix, :hash, :perms, :created)
        ");
        $stmt->execute([
            ':uid' => $user['id'],
            ':name' => $name,
            ':prefix' => $prefix,
            ':hash' => $keyHash,
            ':perms' => json_encode(['send', 'read']),
            ':created' => date('Y-m-d H:i:s'),
        ]);

        $keyId = (int)$db->lastInsertId();
        AuditLog::log('api_key_created', 'api_keys', $keyId, ['name' => $name, 'prefix' => $prefix]);

        // Flash raw key for one-time display to user
        Session::setFlash('new_api_key', $fullKey);
        Session::setFlash('success', 'API key generated successfully. Copy it now, as it cannot be shown again.');
        Response::redirect('/api-keys');
    }

    public function delete(int $id): void
    {
        $user = auth_user();
        $db = Model::getDb();
        $stmt = $db->prepare("DELETE FROM api_keys WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $id, ':uid' => $user['id']]);

        AuditLog::log('api_key_revoked', 'api_keys', $id);

        Session::setFlash('success', 'API key has been revoked and can no longer be used.');
        Response::redirect('/api-keys');
    }
}
