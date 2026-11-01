<?php

namespace App\Helpers;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $sessionConfig = config('security.session', []);
        $lifetime = $sessionConfig['lifetime'] ?? 7200;
        $path = $sessionConfig['path'] ?? '/';
        $domain = $sessionConfig['domain'] ?? '';
        $secure = $sessionConfig['secure'] ?? (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $httponly = $sessionConfig['httponly'] ?? true;
        $samesite = $sessionConfig['samesite'] ?? 'Lax';

        session_name($sessionConfig['name'] ?? 'mailmoll_session');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ]);

        session_start();
        self::$started = true;

        // Auto-generate CSRF token if missing
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        // Age old flash data
        if (isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash'] = $_SESSION['_flash_next'];
            unset($_SESSION['_flash_next']);
        } else {
            unset($_SESSION['_flash']);
        }

        // Age old input
        if (isset($_SESSION['_old_input_next'])) {
            $_SESSION['_old_input'] = $_SESSION['_old_input_next'];
            unset($_SESSION['_old_input_next']);
        } else {
            unset($_SESSION['_old_input']);
        }
    }

    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        if (empty($token) || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function setFlash(string $key, mixed $message): void
    {
        self::start();
        $_SESSION['_flash_next'][$key] = $message;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function flashOldInput(array $data): void
    {
        self::start();
        // Remove sensitive fields
        unset($data['password'], $data['password_confirmation'], $data['_csrf_token']);
        $_SESSION['_old_input_next'] = $data;
    }

    public static function getOldInput(string $key, mixed $default = ''): mixed
    {
        self::start();
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}
