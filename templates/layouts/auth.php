<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Authentication') ?> - <?= e(config('app.name', 'MailFlow')) ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/marketing.css') ?>" rel="stylesheet">
    <style>
        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .auth-container {
            width: 100%;
            max-width: 480px;
        }
        .auth-card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 2.5rem;
        }
        .auth-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #f8fafc;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            letter-spacing: -0.025em;
        }
        .auth-brand .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="text-center mb-4">
            <a href="<?= \App\Helpers\UrlHelper::to('/') ?>" class="auth-brand">
                <span class="brand-icon"><i class="bi bi-send-fill fs-5"></i></span>
                <span>MailFlow</span>
            </a>
        </div>

        <div class="auth-card">
            <?php if ($flash = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= e($flash) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($flash = \App\Helpers\Session::getFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <div><?= e($flash) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>

        <div class="text-center mt-4 text-secondary small">
            &copy; <?= date('Y') ?> MailFlow Technologies Inc. All rights reserved.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
