<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\User;
use App\Models\AuditLog;

class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login', [
            'title' => 'Sign In - MailMoll',
        ], 'layouts/auth');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = Validator::make($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/login');
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', 'Invalid email address or password.');
            Response::redirect('/login');
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Your account has been deactivated. Contact an administrator.');
            Response::redirect('/login');
        }

        // Regenerate session to prevent session fixation
        Session::regenerate();

        // Safe user session without password hash
        unset($user['password_hash']);
        Session::set('user', $user);

        User::recordLogin((int)$user['id']);
        AuditLog::log('user_login', 'users', (int)$user['id'], ['email' => $email]);

        Session::setFlash('success', "Welcome back, {$user['name']}!");
        Response::redirect('/');
    }

    public function logout(): void
    {
        $user = auth_user();
        if ($user) {
            AuditLog::log('user_logout', 'users', (int)$user['id']);
        }

        Session::destroy();
        Response::redirect('/login');
    }
}
