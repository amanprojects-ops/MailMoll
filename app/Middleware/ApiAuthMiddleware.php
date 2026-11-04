<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;
use App\Database\Connection;
use PDO;

class ApiAuthMiddleware
{
    public function handle(): void
    {
        Session::start();

        // 1. Extract Bearer Token or X-API-Key
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
            ?? '';

        $apiKey = '';
        if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            $apiKey = trim($matches[1]);
        } elseif (!empty($_SERVER['HTTP_X_API_KEY'])) {
            $apiKey = trim((string)$_SERVER['HTTP_X_API_KEY']);
        } elseif (!empty($_GET['api_key'])) {
            $apiKey = trim((string)$_GET['api_key']);
        }

        // 2. If API Key is present, authenticate against api_keys table
        if (!empty($apiKey)) {
            $keyHash = hash('sha256', $apiKey);
            $db = Connection::getInstance();

            $stmt = $db->prepare("
                SELECT a.*, u.id as uid, u.name, u.email, u.role
                FROM api_keys a
                JOIN users u ON a.user_id = u.id
                WHERE a.key_hash = :hash
                LIMIT 1
            ");
            $stmt->execute([':hash' => $keyHash]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record) {
                // Update last_used_at
                $update = $db->prepare("UPDATE api_keys SET last_used_at = :now WHERE id = :id");
                $update->execute([
                    ':now' => date('Y-m-d H:i:s'),
                    ':id' => $record['id'],
                ]);

                // Set user context
                $user = [
                    'id' => (int)$record['uid'],
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'role' => $record['role'],
                ];
                $_SERVER['API_USER'] = $user;
                Session::set('user', $user);
                return;
            }

            Response::error('Invalid, unrecognized, or revoked API key.', 401);
        }

        // 3. Fallback to active browser session if available
        if (Session::has('user')) {
            return;
        }

        // 4. Neither API Key nor active Session found
        Response::error('Unauthorized. Provide a valid Bearer token (mflw_live_...) or active session.', 401);
    }
}
