<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

class AdminMiddleware
{
    public function handle(): void
    {
        Session::start();

        if (!Session::has('user')) {
            Session::setFlash('error', 'Please sign in to access the administration panel.');
            Response::redirect('/login');
            return;
        }

        $user = Session::get('user');
        if (($user['role'] ?? '') !== 'admin') {
            Session::setFlash('error', 'Access denied. Super Administrator privileges required.');
            Response::redirect('/dashboard');
            return;
        }
    }
}
