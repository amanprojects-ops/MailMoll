<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'MailFlow - Professional Multi-SMTP Email Management SaaS') ?></title>
    <meta name="description" content="MailFlow is a self-hosted, enterprise-grade multi-SMTP email management SaaS. Connect authorized SMTP nodes, optimize deliverability, organize campaigns, and track real-time dispatch events.">

    <!-- Open Graph & Social Meta -->
    <meta property="og:title" content="<?= e($title ?? 'MailFlow - Smarter Email Infrastructure') ?>">
    <meta property="og:description" content="Manage your authorized SMTP connections, organize campaigns, and monitor email delivery with complete privacy.">
    <meta property="og:type" content="website">

    <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Toastify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <!-- MailFlow Marketing Design Tokens & CSS -->
    <link href="<?= asset('css/marketing.css') ?>" rel="stylesheet">

    <script>
        window.MAILFLOW_URL = '<?= e(url()) ?>';
    </script>
</head>
<body>

<!-- Navigation Header -->
<nav class="navbar navbar-expand-lg navbar-marketing sticky-top py-3">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark text-decoration-none" href="<?= url('/') ?>">
            <span class="brand-logo-mark"><i class="bi bi-send-check-fill"></i></span>
            <span class="fs-5 tracking-tight">MailFlow</span>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill small py-1 px-2" style="font-size: 10px;">Self-Hosted</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#marketingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="marketingNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'features' ? 'active' : '' ?>" href="<?= url('/features') ?>">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'solutions' ? 'active' : '' ?>" href="<?= url('/solutions') ?>">Solutions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'pricing' ? 'active' : '' ?>" href="<?= url('/pricing') ?>">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'docs' ? 'active' : '' ?>" href="<?= url('/docs') ?>">Documentation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'faq' ? 'active' : '' ?>" href="<?= url('/faq') ?>">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeNav ?? '') === 'contact' ? 'active' : '' ?>" href="<?= url('/contact') ?>">Contact</a>
                </li>
            </ul>

            <!-- Right CTAs & Theme Toggle -->
            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                <!-- Theme Switcher -->
                <button type="button" class="btn btn-sm btn-link text-muted p-2" id="themeToggleBtn" title="Toggle Light/Dark Theme">
                    <i class="bi bi-moon-stars-fill fs-6" id="themeIcon"></i>
                </button>

                <?php if (auth_check()): ?>
                    <a href="<?= url('/dashboard') ?>" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                        <i class="bi bi-grid-fill me-1"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="btn btn-link text-muted text-decoration-none btn-sm px-3 fw-semibold">
                        Log In
                    </a>
                    <a href="<?= url('/register') ?>" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                        Get Started <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Notifications -->
<?php if ($flashSuccess = flash('success')): ?>
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div><?= e($flashSuccess) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<?php if ($flashError = flash('error')): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div><?= e($flashError) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content Outlet -->
<main>
    <?= $content ?>
</main>

<!-- Marketing Footer -->
<footer class="footer-marketing">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="brand-logo-mark"><i class="bi bi-send-check-fill"></i></span>
                    <span class="fs-5 fw-bold text-dark tracking-tight">MailFlow</span>
                </div>
                <p class="text-muted small pe-lg-4">
                    Enterprise self-hosted multi-SMTP email management software. Connect authorized relay providers, protect domain reputation, and streamline email broadcasts with zero vendor lock-in.
                </p>
                <div class="d-flex gap-2 text-muted">
                    <span class="badge bg-light text-secondary border"><i class="bi bi-shield-check text-success me-1"></i> 100% Self-Hosted</span>
                    <span class="badge bg-light text-secondary border"><i class="bi bi-lock text-primary me-1"></i> AES-256 Vault</span>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6>Product</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/features') ?>">All Features</a></li>
                    <li><a href="<?= url('/solutions') ?>">Use Cases</a></li>
                    <li><a href="<?= url('/pricing') ?>">Plans & Pricing</a></li>
                    <li><a href="<?= url('/templates') ?>">Template Builder</a></li>
                    <li><a href="<?= url('/smtp') ?>">SMTP Engine</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6>Resources</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/docs') ?>">Documentation</a></li>
                    <li><a href="<?= url('/docs#api') ?>">REST API</a></li>
                    <li><a href="<?= url('/faq') ?>">FAQ</a></li>
                    <li><a href="<?= url('/sitemap.xml') ?>" target="_blank">Sitemap</a></li>
                    <li><a href="<?= url('/robots.txt') ?>" target="_blank">Robots.txt</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6>Company</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/about') ?>">About Us</a></li>
                    <li><a href="<?= url('/contact') ?>">Contact Sales</a></li>
                    <li><a href="<?= url('/privacy-policy') ?>">Privacy Policy</a></li>
                    <li><a href="<?= url('/terms') ?>">Terms of Service</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6>Security</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= url('/docs#security') ?>">Security Architecture</a></li>
                    <li><a href="<?= url('/privacy-policy#gdpr') ?>">GDPR & CAN-SPAM</a></li>
                    <li><a href="<?= url('/docs#self-hosted') ?>">cPanel & Apache</a></li>
                    <li><a href="<?= url('/login') ?>">Admin Sign-in</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top pt-4 d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
            <div>
                &copy; <?= date('Y') ?> MailFlow. All rights reserved. Self-Hosted Multi-SMTP Email Management SaaS.
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex align-items-center gap-1 text-success">
                    <span class="spinner-grow spinner-grow-sm" role="status" style="width: 8px; height: 8px;"></span>
                    <span style="font-size: 11px;">Systems Operational</span>
                </span>
                <a href="<?= url('/privacy-policy') ?>" class="text-muted text-decoration-none">Privacy</a>
                <a href="<?= url('/terms') ?>" class="text-muted text-decoration-none">Terms</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
// Theme Switcher Logic
document.addEventListener('DOMContentLoaded', function () {
    const htmlEl = document.documentElement;
    const themeBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');

    // Retrieve saved theme or prefer system
    const savedTheme = localStorage.getItem('mailflow_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    setTheme(savedTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            const current = htmlEl.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            setTheme(next);
        });
    }

    function setTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);
        localStorage.setItem('mailflow_theme', theme);
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill text-warning fs-6';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill text-secondary fs-6';
            }
        }
    }
});
</script>
</body>
</html>
