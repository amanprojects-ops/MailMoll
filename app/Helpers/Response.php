<?php

namespace App\Helpers;

class Response
{
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(string $message, mixed $data = [], int $statusCode = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): void
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    public static function redirect(string $path, int $statusCode = 302): void
    {
        $target = str_starts_with($path, 'http') ? $path : url($path);
        header("Location: {$target}", true, $statusCode);
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        header("Location: {$referer}", true, 302);
        exit;
    }
}
