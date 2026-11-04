<?php

use App\Helpers\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\ApiAuthMiddleware;
use App\Middleware\CsrfMiddleware;

use App\Controllers\ApiController;
use App\Controllers\SmtpController;
use App\Controllers\CampaignController;
use App\Controllers\QueueController;
use App\Controllers\AnalyticsController;
use App\Controllers\ContactController;

// ==================================================
// REST API v1 (Stateless Bearer Token & API Key Authentication)
// ==================================================
Router::group(['middleware' => [ApiAuthMiddleware::class]], function () {
    // Send / Enqueue Transactional Email
    Router::post('/api/v1/emails/send', [ApiController::class, 'sendEmail']);
    Router::post('/api/v1/send', [ApiController::class, 'sendEmail']);

    // Inspect Email Dispatch & Delivery Status
    Router::get('/api/v1/emails/{id}', [ApiController::class, 'getEmail']);

    // System Telemetry, Worker Heartbeat, and Queue Metrics
    Router::get('/api/v1/status', [ApiController::class, 'status']);

    // Programmatic Worker / Cron Execution (For Web Crons & Remote Dispatch)
    Router::post('/api/v1/worker/run', [ApiController::class, 'runWorker']);
    Router::post('/api/v1/cron/run', [ApiController::class, 'runWorker']);
    Router::get('/api/v1/cron/run', [ApiController::class, 'runWorker']);
});

// ==================================================
// Customer Dashboard Internal Web AJAX Endpoints (Session Protected)
// ==================================================
Router::group(['middleware' => [AuthMiddleware::class]], function () {
    // Live SMTP connection test
    Router::post('/api/smtp/test/{id}', [SmtpController::class, 'test'], [CsrfMiddleware::class]);

    // Send Test Email for campaign preview
    Router::post('/api/campaigns/test-send', [CampaignController::class, 'testSend'], [CsrfMiddleware::class]);

    // Trigger Worker batch run from web UI
    Router::post('/api/worker/run', [QueueController::class, 'triggerWorker'], [CsrfMiddleware::class]);

    // Analytics Chart Data
    Router::get('/api/analytics/data', [AnalyticsController::class, 'apiData']);

    // Contact Import Preview
    Router::post('/api/contacts/import-preview', [ContactController::class, 'previewImport'], [CsrfMiddleware::class]);

    // SMTP Bulk Import Preview
    Router::post('/api/smtp/import-preview', [SmtpController::class, 'previewImport'], [CsrfMiddleware::class]);
});
