<?php

use App\Helpers\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SmtpController;
use App\Controllers\CampaignController;
use App\Controllers\TemplateController;
use App\Controllers\ContactController;
use App\Controllers\QueueController;
use App\Controllers\LogController;
use App\Controllers\SuppressionController;
use App\Controllers\AnalyticsController;
use App\Controllers\SettingsController;
use App\Controllers\PublicController;

// Public Marketing Pages
Router::get('/', [PublicController::class, 'home']);
Router::get('/features', [PublicController::class, 'features']);
Router::get('/solutions', [PublicController::class, 'solutions']);
Router::get('/pricing', [PublicController::class, 'pricing']);
Router::get('/about', [PublicController::class, 'about']);
Router::get('/contact', [PublicController::class, 'contact']);
Router::post('/contact', [PublicController::class, 'submitContact'], [CsrfMiddleware::class]);
Router::post('/newsletter/subscribe', [PublicController::class, 'subscribeNewsletter'], [CsrfMiddleware::class]);
Router::get('/docs', [PublicController::class, 'docs']);
Router::get('/faq', [PublicController::class, 'faq']);
Router::get('/privacy-policy', [PublicController::class, 'privacy']);
Router::get('/terms', [PublicController::class, 'terms']);
Router::get('/sitemap.xml', [PublicController::class, 'sitemap']);
Router::get('/robots.txt', [PublicController::class, 'robots']);

// Public & Guest Routes
Router::get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
Router::post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
Router::get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
Router::post('/register', [AuthController::class, 'register'], [GuestMiddleware::class, CsrfMiddleware::class]);
Router::get('/forgot-password', [AuthController::class, 'showForgotPassword'], [GuestMiddleware::class]);
Router::post('/forgot-password', [AuthController::class, 'forgotPassword'], [GuestMiddleware::class, CsrfMiddleware::class]);
Router::get('/reset-password', [AuthController::class, 'showResetPassword'], [GuestMiddleware::class]);
Router::post('/reset-password', [AuthController::class, 'resetPassword'], [GuestMiddleware::class, CsrfMiddleware::class]);
Router::get('/verify-email', [AuthController::class, 'showVerifyEmail']);
Router::get('/verify-email/confirm', [AuthController::class, 'verifyEmail']);
Router::post('/verify-email/resend', [AuthController::class, 'resendVerification'], [CsrfMiddleware::class]);
Router::get('/two-factor', [AuthController::class, 'showTwoFactor']);
Router::post('/two-factor', [AuthController::class, 'twoFactor'], [CsrfMiddleware::class]);
Router::get('/logout', [AuthController::class, 'logout']);
Router::post('/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);

// Public Unsubscribe Handlers (RFC-8058)
Router::get('/unsubscribe', [SuppressionController::class, 'handleUnsubscribe']);
Router::post('/unsubscribe', [SuppressionController::class, 'handleUnsubscribe']);

// Protected Authenticated Routes
Router::group(['middleware' => [AuthMiddleware::class]], function () {

    // Dashboard Overview
    Router::get('/dashboard', [DashboardController::class, 'index']);

    // SMTP Management
    Router::get('/smtp', [SmtpController::class, 'index']);
    Router::get('/smtp/sample', [SmtpController::class, 'downloadSample']);
    Router::get('/smtp/sample/{format}', [SmtpController::class, 'downloadSample']);
    Router::post('/smtp/store', [SmtpController::class, 'store'], [CsrfMiddleware::class]);
    Router::post('/smtp/import', [SmtpController::class, 'import'], [CsrfMiddleware::class]);
    Router::post('/smtp/update/{id}', [SmtpController::class, 'update'], [CsrfMiddleware::class]);
    Router::post('/smtp/toggle/{id}', [SmtpController::class, 'toggle'], [CsrfMiddleware::class]);
    Router::post('/smtp/delete/{id}', [SmtpController::class, 'delete'], [CsrfMiddleware::class]);

    // SMTP Groups
    Router::post('/smtp/groups/store', [SmtpController::class, 'storeGroup'], [CsrfMiddleware::class]);
    Router::post('/smtp/groups/update/{id}', [SmtpController::class, 'updateGroup'], [CsrfMiddleware::class]);
    Router::post('/smtp/groups/delete/{id}', [SmtpController::class, 'deleteGroup'], [CsrfMiddleware::class]);

    // Campaigns & Wizard
    Router::get('/campaigns', [CampaignController::class, 'index']);
    Router::get('/campaigns/new', [CampaignController::class, 'create']);
    Router::post('/campaigns/store', [CampaignController::class, 'store'], [CsrfMiddleware::class]);
    Router::get('/campaigns/edit/{id}', [CampaignController::class, 'edit']);
    Router::post('/campaigns/update/{id}', [CampaignController::class, 'update'], [CsrfMiddleware::class]);
    Router::post('/campaigns/launch/{id}', [CampaignController::class, 'launch'], [CsrfMiddleware::class]);
    Router::post('/campaigns/pause/{id}', [CampaignController::class, 'pause'], [CsrfMiddleware::class]);
    Router::post('/campaigns/resume/{id}', [CampaignController::class, 'resume'], [CsrfMiddleware::class]);
    Router::post('/campaigns/delete/{id}', [CampaignController::class, 'delete'], [CsrfMiddleware::class]);

    // Email Templates
    Router::get('/templates', [TemplateController::class, 'index']);
    Router::get('/templates/new', [TemplateController::class, 'create']);
    Router::post('/templates/store', [TemplateController::class, 'store'], [CsrfMiddleware::class]);
    Router::get('/templates/edit/{id}', [TemplateController::class, 'edit']);
    Router::post('/templates/update/{id}', [TemplateController::class, 'update'], [CsrfMiddleware::class]);
    Router::post('/templates/delete/{id}', [TemplateController::class, 'delete'], [CsrfMiddleware::class]);
    Router::get('/templates/preview/{id}', [TemplateController::class, 'preview']);

    // Contacts & Lists
    Router::get('/contacts', [ContactController::class, 'index']);
    Router::get('/contacts/sample', [ContactController::class, 'downloadSample']);
    Router::get('/contacts/sample/{format}', [ContactController::class, 'downloadSample']);
    Router::post('/contacts/store', [ContactController::class, 'store'], [CsrfMiddleware::class]);
    Router::post('/contacts/lists/store', [ContactController::class, 'storeList'], [CsrfMiddleware::class]);
    Router::post('/contacts/lists/update/{id}', [ContactController::class, 'updateList'], [CsrfMiddleware::class]);
    Router::post('/contacts/import', [ContactController::class, 'import'], [CsrfMiddleware::class]);
    Router::post('/contacts/delete/{id}', [ContactController::class, 'delete'], [CsrfMiddleware::class]);
    Router::post('/contacts/lists/delete/{id}', [ContactController::class, 'deleteList'], [CsrfMiddleware::class]);

    // Queue Monitor
    Router::get('/queue', [QueueController::class, 'index']);
    Router::post('/queue/retry/{id}', [QueueController::class, 'retry'], [CsrfMiddleware::class]);
    Router::post('/queue/cancel/{id}', [QueueController::class, 'cancel'], [CsrfMiddleware::class]);
    Router::post('/queue/clear-failed', [QueueController::class, 'clearFailed'], [CsrfMiddleware::class]);

    // Logs & Deliveries
    Router::get('/logs/delivery', [LogController::class, 'deliveryLogs']);
    Router::get('/logs/audit', [LogController::class, 'auditLogs']);

    // Suppressions
    Router::get('/suppressions', [SuppressionController::class, 'index']);
    Router::post('/suppressions/store', [SuppressionController::class, 'store'], [CsrfMiddleware::class]);
    Router::post('/suppressions/delete/{id}', [SuppressionController::class, 'delete'], [CsrfMiddleware::class]);

    // Analytics
    Router::get('/analytics', [AnalyticsController::class, 'index']);

    // Settings & Profile
    Router::get('/settings', [SettingsController::class, 'index']);
    Router::post('/settings/update', [SettingsController::class, 'update'], [CsrfMiddleware::class]);
    Router::get('/settings/backup', [SettingsController::class, 'backup']);
    Router::get('/profile', [SettingsController::class, 'profile']);
    Router::post('/profile/update', [SettingsController::class, 'updateProfile'], [CsrfMiddleware::class]);
});
