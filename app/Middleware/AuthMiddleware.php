<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        Session::start();

        if (!Session::has('user')) {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (str_contains($uri, '/api/')) {
                Response::error('Unauthenticated session. Please log in.', 401);
            }

            Session::setFlash('error', 'Please log in to continue.');
            Response::redirect('/login');
        }
    }
}
