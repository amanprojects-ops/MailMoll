<?php

use App\Helpers\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

use App\Controllers\SmtpController;
use App\Controllers\CampaignController;
use App\Controllers\QueueController;
use App\Controllers\AnalyticsController;
use App\Controllers\ContactController;

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
