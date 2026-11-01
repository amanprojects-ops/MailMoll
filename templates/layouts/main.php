<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Dashboard') ?> - <?= e(config('app.name', 'MailMoll')) ?></title>

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Quill Editor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

    <!-- CodeMirror CSS -->
    <link href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/theme/dracula.css" rel="stylesheet">

    <!-- Toastify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <!-- MailMoll Dashboard CSS -->
    <link href="<?= asset('css/dashboard.css') ?>" rel="stylesheet">

    <script>
        window.MAILMOLL_URL = '<?= e(url()) ?>';
    </script>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar Navigation -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">
            <span class="brand-badge"><i class="bi bi-envelope-paper-heart-fill"></i></span>
            <span><?= e(config('app.name', 'MailMoll')) ?></span>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">Core</div>
            <a href="<?= url('/') ?>" class="nav-link <?= ($activeNav ?? '') === 'overview' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> Overview
            </a>
            <a href="<?= url('/campaigns') ?>" class="nav-link <?= ($activeNav ?? '') === 'campaigns' ? 'active' : '' ?>">
                <i class="bi bi-send-fill"></i> Campaigns
            </a>
            <a href="<?= url('/queue') ?>" class="nav-link <?= ($activeNav ?? '') === 'email_queue' ? 'active' : '' ?>">
                <i class="bi bi-hourglass-split"></i> Email Queue
            </a>

            <div class="nav-section">SMTP Infrastructure</div>
            <a href="<?= url('/smtp') ?>" class="nav-link <?= ($activeNav ?? '') === 'smtp_accounts' ? 'active' : '' ?>">
                <i class="bi bi-server"></i> SMTP Accounts & Groups
            </a>

            <div class="nav-section">Audience & Content</div>
            <a href="<?= url('/contacts') ?>" class="nav-link <?= ($activeNav ?? '') === 'contacts' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Contacts & Lists
            </a>
            <a href="<?= url('/templates') ?>" class="nav-link <?= ($activeNav ?? '') === 'templates' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-code-fill"></i> Email Templates
            </a>
            <a href="<?= url('/suppressions') ?>" class="nav-link <?= ($activeNav ?? '') === 'suppressions' ? 'active' : '' ?>">
                <i class="bi bi-shield-slash-fill"></i> Suppressions & Unsub
            </a>

            <div class="nav-section">Intelligence & Logs</div>
            <a href="<?= url('/analytics') ?>" class="nav-link <?= ($activeNav ?? '') === 'analytics' ? 'active' : '' ?>">
                <i class="bi bi-graph-up-arrow"></i> Analytics
            </a>
            <a href="<?= url('/logs/delivery') ?>" class="nav-link <?= ($activeNav ?? '') === 'delivery_logs' ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Delivery Logs
            </a>

            <div class="nav-section">Administration</div>
            <a href="<?= url('/settings') ?>" class="nav-link <?= ($activeNav ?? '') === 'system_settings' ? 'active' : '' ?>">
                <i class="bi bi-gear-fill"></i> System Settings
            </a>
            <a href="<?= url('/profile') ?>" class="nav-link <?= ($activeNav ?? '') === 'profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i> User Profile
            </a>
        </div>
    </div>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="w-100">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary btn-sm me-3" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <span class="fs-6 fw-semibold text-secondary d-none d-md-inline">
                    <?= e($title ?? 'Overview') ?>
                </span>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary border-0" id="themeToggleBtn" title="Toggle Light/Dark Theme">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown">
                            <span class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                <?= strtoupper(substr(auth_user()['name'] ?? 'A', 0, 1)) ?>
                            </span>
                            <span class="fw-semibold small d-none d-sm-inline"><?= e(auth_user()['name'] ?? 'Admin') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?= url('/settings') ?>"><i class="bi bi-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= url('/logout') ?>" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <div class="container-fluid px-4 pt-3">
            <?php if ($flashSuccess = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= e($flashSuccess) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($flashError = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= e($flashError) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Workspace -->
        <main class="container-fluid px-4 py-3 flex-grow-1">
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer class="py-3 px-4 border-top text-muted small d-flex justify-content-between align-items-center bg-white mt-auto">
            <span>&copy; <?= date('Y') ?> <strong><?= e(config('app.name', 'MailMoll')) ?></strong>. Multi-SMTP Engine.</span>
            <span>Version <?= e(config('app.version', '1.0.0')) ?> (PHP <?= phpversion() ?>)</span>
        </footer>
    </div>
</div>

<!-- Core JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/xml/xml.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/htmlmixed/htmlmixed.js"></script>

<!-- MailMoll App JS -->
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
