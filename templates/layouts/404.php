<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/dashboard.css') ?>" rel="stylesheet">
    <style>
        body {
            background-color: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-center">
    <div class="card p-5 shadow-sm" style="max-width: 500px;">
        <i class="bi bi-compass text-primary" style="font-size: 4rem;"></i>
        <h1 class="h2 fw-bold mt-3">404 - Page Not Found</h1>
        <p class="text-muted">The requested path <code><?= e($path ?? '') ?></code> was not found on this server.</p>
        <div class="mt-4">
            <a href="<?= url('/') ?>" class="btn btn-primary px-4">
                <i class="bi bi-house-door-fill me-1"></i> Return to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
