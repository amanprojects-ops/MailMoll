<?php

use App\Helpers\Env;
use App\Helpers\Session;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return base_path('app' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];
        $parts = explode('.', $key);
        $file = $parts[0];

        if (!isset($configs[$file])) {
            $filePath = base_path("config/{$file}.php");
            if (file_exists($filePath)) {
                $configs[$file] = require $filePath;
            } else {
                $configs[$file] = [];
            }
        }

        $current = $configs[$file];
        for ($i = 1; $i < count($parts); $i++) {
            $sub = $parts[$i];
            if (is_array($current) && array_key_exists($sub, $current)) {
                $current = $current[$sub];
            } else {
                return $default;
            }
        }

        return $current;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
                || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
            $scheme = $isHttps ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];

            // Determine subfolder dynamically from SCRIPT_NAME or configured app.url
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
            $subfolder = preg_replace('#/public/?$#i', '', $scriptDir);
            if ($subfolder === '/' || $subfolder === '.' || $subfolder === '\\') {
                $subfolder = '';
            }

            // Fallback to configured path if scriptDir is empty but app.url has path
            if (empty($subfolder)) {
                $configuredPath = parse_url(config('app.url', ''), PHP_URL_PATH);
                if (!empty($configuredPath) && $configuredPath !== '/') {
                    $subfolder = preg_replace('#/public/?$#i', '', $configuredPath);
                }
            }

            $root = "{$scheme}://{$host}" . $subfolder;
        } else {
            // CLI fallback (workers, cron, CLI maintenance)
            $root = config('app.url', 'http://localhost/MailMoll');
        }

        $root = rtrim((string)$root, '/');
        // Always normalize by removing trailing /public if configured
        $root = preg_replace('#/public$#i', '', $root);
        $path = ltrim($path, '/');
        return $path ? "{$root}/{$path}" : $root;
    }
}

if (!function_exists('asset')) {
    function asset(string $path = ''): string
    {
        $cleanPath = ltrim($path, '/');
        $url = url('assets/' . $cleanPath);

        // Resolve local file to determine modification timestamp for cache-busting
        $localFile = base_path('assets/' . $cleanPath);
        if (!file_exists($localFile)) {
            $localFile = public_path('assets/' . $cleanPath);
        }
        $v = file_exists($localFile) ? filemtime($localFile) : '2.0.2';
        return "{$url}?v={$v}";
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return Session::getOldInput($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $message = null): mixed
    {
        if ($message === null) {
            return Session::getFlash($key);
        }
        Session::setFlash($key, $message);
        return null;
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        return Session::get('user');
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return Session::has('user');
    }
}
