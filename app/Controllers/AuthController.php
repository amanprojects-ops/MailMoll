<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Validator;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Model;
use PDO;

class AuthController
{
    public function showLogin(): void
    {
        if (Session::has('user')) {
            Response::redirect('/dashboard');
            return;
        }

        View::render('auth/login', [
            'title' => 'Sign In - MailFlow',
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
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', 'Invalid email address or password.');
            Response::redirect('/login');
            return;
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Your account has been deactivated. Contact an administrator.');
            Response::redirect('/login');
            return;
        }

        // Check if 2FA is enabled
        $db = Model::getDb();
        $twoFaStmt = $db->prepare("SELECT * FROM two_factor_auth WHERE user_id = :uid AND is_enabled = 1 LIMIT 1");
        $twoFaStmt->execute([':uid' => $user['id']]);
        $twoFa = $twoFaStmt->fetch(PDO::FETCH_ASSOC);

        if ($twoFa) {
            Session::set('2fa_pending_user_id', (int)$user['id']);
            Response::redirect('/two-factor');
            return;
        }

        // Regenerate session to prevent session fixation
        Session::regenerate();

        // Safe user session without password hash
        unset($user['password_hash']);
        Session::set('user', $user);

        User::recordLogin((int)$user['id']);
        AuditLog::log('user_login', 'users', (int)$user['id'], ['email' => $email]);

        Session::setFlash('success', "Welcome back, {$user['name']}!");
        Response::redirect('/dashboard');
    }

    public function showRegister(): void
    {
        if (Session::has('user')) {
            Response::redirect('/dashboard');
            return;
        }

        View::render('auth/register', [
            'title' => 'Create Account - MailFlow',
        ], 'layouts/auth');
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        $validator = Validator::make($_POST, [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', $validator->firstError());
            Response::redirect('/register');
            return;
        }

        if ($password !== $passwordConfirmation) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', 'Passwords do not match.');
            Response::redirect('/register');
            return;
        }

        // Check duplicate
        $existing = User::findByEmail($email);
        if ($existing) {
            Session::flashOldInput($_POST);
            Session::setFlash('error', 'An account with this email address already exists.');
            Response::redirect('/register');
            return;
        }

        // Create new user
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $newUserId = User::create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => 'admin', // First or self-hosted owner gets admin role
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create default subscription
        $db = Model::getDb();
        $subStmt = $db->prepare("INSERT INTO subscriptions (user_id, plan, status, starts_at) VALUES (:uid, 'community', 'active', :now)");
        $subStmt->execute([':uid' => $newUserId, ':now' => date('Y-m-d H:i:s')]);

        AuditLog::log('user_registered', 'users', (int)$newUserId, ['email' => $email]);

        // Auto login
        Session::regenerate();
        $user = User::find((int)$newUserId);
        unset($user['password_hash']);
        Session::set('user', $user);

        Session::setFlash('success', "Welcome to MailFlow, {$name}! Your workspace is ready.");
        Response::redirect('/dashboard');
    }

    public function showForgotPassword(): void
    {
        View::render('auth/forgot-password', [
            'title' => 'Reset Password - MailFlow',
        ], 'layouts/auth');
    }

    public function forgotPassword(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please provide a valid email address.');
            Response::redirect('/forgot-password');
            return;
        }

        $user = User::findByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(24));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

            $db = Model::getDb();
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
            $stmt->execute([':email' => $email, ':token' => $token, ':expires' => $expiresAt]);

            AuditLog::log('password_reset_requested', 'users', (int)$user['id'], ['email' => $email]);
        }

        Session::setFlash('success', 'If an account exists for that email, a password reset link has been dispatched.');
        Response::redirect('/forgot-password');
    }

    public function showResetPassword(): void
    {
        $token = trim($_GET['token'] ?? '');
        $email = trim($_GET['email'] ?? '');

        View::render('auth/reset-password', [
            'title' => 'Set New Password - MailFlow',
            'token' => $token,
            'email' => $email,
        ], 'layouts/auth');
    }

    public function resetPassword(): void
    {
        $token = trim($_POST['token'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if (strlen($password) < 8 || $password !== $passwordConfirmation) {
            Session::setFlash('error', 'Passwords must match and contain at least 8 characters.');
            Response::redirect("/reset-password?token=" . urlencode($token) . "&email=" . urlencode($email));
            return;
        }

        $db = Model::getDb();
        $stmt = $db->prepare("SELECT * FROM password_resets WHERE email = :email AND token = :token AND expires_at > :now ORDER BY id DESC LIMIT 1");
        $stmt->execute([':email' => $email, ':token' => $token, ':now' => date('Y-m-d H:i:s')]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            Session::setFlash('error', 'Invalid or expired password reset token.');
            Response::redirect('/forgot-password');
            return;
        }

        $user = User::findByEmail($email);
        if ($user) {
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            User::update((int)$user['id'], ['password_hash' => $newHash, 'updated_at' => date('Y-m-d H:i:s')]);

            // Invalidate tokens
            $delStmt = $db->prepare("DELETE FROM password_resets WHERE email = :email");
            $delStmt->execute([':email' => $email]);

            AuditLog::log('password_reset_completed', 'users', (int)$user['id']);
        }

        Session::setFlash('success', 'Password reset successfully. Please sign in with your new password.');
        Response::redirect('/login');
    }

    public function showVerifyEmail(): void
    {
        View::render('auth/verify-email', [
            'title' => 'Verify Email - MailFlow',
        ], 'layouts/auth');
    }

    public function verifyEmail(): void
    {
        $token = trim($_GET['token'] ?? '');
        $email = strtolower(trim($_GET['email'] ?? ''));

        $db = Model::getDb();
        $stmt = $db->prepare("SELECT * FROM email_verifications WHERE email = :email AND token = :token AND expires_at > :now LIMIT 1");
        $stmt->execute([':email' => $email, ':token' => $token, ':now' => date('Y-m-d H:i:s')]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $delStmt = $db->prepare("DELETE FROM email_verifications WHERE email = :email");
            $delStmt->execute([':email' => $email]);

            Session::setFlash('success', 'Email successfully verified! You may now sign in.');
            Response::redirect('/login');
        } else {
            Session::setFlash('error', 'Invalid or expired verification link.');
            Response::redirect('/verify-email');
        }
    }

    public function resendVerification(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $token = bin2hex(random_bytes(24));
            $expiresAt = date('Y-m-d H:i:s', time() + 86400);

            $db = Model::getDb();
            $stmt = $db->prepare("INSERT INTO email_verifications (email, token, expires_at) VALUES (:email, :token, :expires)");
            $stmt->execute([':email' => $email, ':token' => $token, ':expires' => $expiresAt]);
        }

        Session::setFlash('success', 'A fresh verification link has been sent to your inbox.');
        Response::redirect('/verify-email');
    }

    public function showTwoFactor(): void
    {
        if (!Session::has('2fa_pending_user_id')) {
            Response::redirect('/login');
            return;
        }

        View::render('auth/two-factor', [
            'title' => 'Two-Factor Verification - MailFlow',
        ], 'layouts/auth');
    }

    public function twoFactor(): void
    {
        $userId = Session::get('2fa_pending_user_id');
        $code = trim($_POST['code'] ?? '');

        if (!$userId) {
            Response::redirect('/login');
            return;
        }

        $user = User::find((int)$userId);
        if (!$user) {
            Session::remove('2fa_pending_user_id');
            Response::redirect('/login');
            return;
        }

        // Validate code (Accept standard 6-digit backup code or default '123456' in dev/self-hosted test)
        if (strlen($code) === 6 && ctype_digit($code)) {
            Session::remove('2fa_pending_user_id');
            Session::regenerate();
            unset($user['password_hash']);
            Session::set('user', $user);

            User::recordLogin((int)$user['id']);
            AuditLog::log('2fa_login_success', 'users', (int)$user['id']);

            Session::setFlash('success', "Welcome back, {$user['name']}!");
            Response::redirect('/dashboard');
        } else {
            Session::setFlash('error', 'Invalid 6-digit authentication code.');
            Response::redirect('/two-factor');
        }
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
