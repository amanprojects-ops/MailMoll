<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Env;
use App\Helpers\Session;
use App\Helpers\Router;

// 1. Load Environment Configuration
Env::load(__DIR__ . '/../.env');

// 2. Setup Error Handling
if (config('app.debug', false)) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 3. Timezone
date_default_timezone_set(config('app.timezone', 'UTC'));

// 4. Session Start
Session::start();

// 5. Load Routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// 6. Dispatch Request
try {
    Router::dispatch();
} catch (\Throwable $e) {
    if (config('app.debug', false)) {
        http_response_code(500);
        echo "<div style='font-family: monospace; padding: 20px; background: #fff0f0; border-left: 4px solid #ef4444;'>";
        echo "<h2 style='color: #b91c1c;'>Uncaught Exception: " . htmlspecialchars($e->getMessage()) . "</h2>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
        echo "<pre style='background: #1e293b; color: #f8fafc; padding: 15px; border-radius: 6px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        echo "<h1>Internal Server Error</h1><p>An unexpected error occurred. Please try again later.</p>";
    }
}
