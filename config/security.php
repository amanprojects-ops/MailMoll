<?php

return [
    'cipher' => 'aes-256-gcm',
    'session' => [
        'name' => 'mailmoll_session',
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'path' => '/',
        'domain' => '',
        'secure' => (bool) env('SESSION_SECURE_COOKIE', false),
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    'rate_limits' => [
        'login_attempts' => 5,
        'login_decay_minutes' => 15,
    ],
];
