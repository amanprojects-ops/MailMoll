<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

class CsrfMiddleware
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Only enforce for state-changing HTTP verbs
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $_POST['_csrf_token'] 
                ?? $_POST['csrf_token'] 
                ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
                ?? $_SERVER['HTTP_CSRF_TOKEN'] 
                ?? null;

            if (!Session::validateCsrfToken($token)) {
                $uri = $_SERVER['REQUEST_URI'] ?? '';
                if (str_contains($uri, '/api/')) {
                    Response::error('Invalid or expired CSRF token.', 403);
                }

                Session::setFlash('error', 'Session token expired. Please retry.');
                Response::back();
            }
        }
    }
}
