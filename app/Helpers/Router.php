<?php

namespace App\Helpers;

class Router
{
    private static array $routes = [];
    private static array $groupMiddleware = [];

    public static function get(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('GET', $path, $handler, $middleware);
    }

    public static function post(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('POST', $path, $handler, $middleware);
    }

    public static function put(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('PUT', $path, $handler, $middleware);
    }

    public static function delete(string $path, array|callable $handler, array $middleware = []): void
    {
        self::addRoute('DELETE', $path, $handler, $middleware);
    }

    private static function addRoute(string $method, string $path, array|callable $handler, array $middleware): void
    {
        $middleware = array_merge(self::$groupMiddleware, $middleware);
        self::$routes[] = [
            'method' => strtoupper($method),
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public static function group(array $attributes, callable $callback): void
    {
        $previousMiddleware = self::$groupMiddleware;
        if (!empty($attributes['middleware'])) {
            $middlewares = (array)$attributes['middleware'];
            self::$groupMiddleware = array_merge(self::$groupMiddleware, $middlewares);
        }

        $callback();

        self::$groupMiddleware = $previousMiddleware;
    }

    public static function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        // Support method override via _method field in POST
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Normalize URI by removing base script/subfolder path
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }

        // Also handle if url starts with /public
        if (str_starts_with($uri, '/public')) {
            $uri = substr($uri, 7);
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Execute middleware pipeline
                foreach ($route['middleware'] as $mw) {
                    $mwInstance = is_string($mw) ? new $mw() : $mw;
                    if (is_callable([$mwInstance, 'handle'])) {
                        $mwInstance->handle();
                    }
                }

                // Execute handler
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    $controller->$method(...array_values($params));
                    return;
                }

                if (is_callable($handler)) {
                    $handler(...array_values($params));
                    return;
                }
            }
        }

        // Route not found
        http_response_code(404);
        if (str_starts_with($uri, '/api/')) {
            Response::json(['success' => false, 'error' => 'API endpoint not found'], 404);
        } else {
            View::render('layouts/404', ['title' => 'Page Not Found', 'path' => $uri], null);
        }
    }
}
