<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Dashboard') ?> - <?= e(config('app.name', 'MailFlow')) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

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

    <!-- MailFlow Design Tokens & Dashboard CSS -->
    <link href="<?= asset('css/marketing.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/dashboard.css') ?>" rel="stylesheet">

    <script>
        window.MAILFLOW_URL = '<?= e(url()) ?>';
        window.MAILMOLL_URL = '<?= e(url()) ?>';
    </script>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar Navigation -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading d-flex align-items-center justify-content-between">
            <a href="<?= url('/dashboard') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                <span class="brand-badge" style="background: linear-gradient(135deg, #6366F1, #4F46E5); width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff;">
                    <i class="bi bi-send-fill fs-6"></i>
                </span>
                <span class="fw-bold fs-5 text-light letter-spacing-tight"><?= e(config('app.name', 'MailFlow')) ?></span>
            </a>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small" style="font-size: 10px;">PRO</span>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">Core Platform</div>
            <a href="<?= url('/dashboard') ?>" class="nav-link <?= in_array($activeNav ?? '', ['overview', 'dashboard']) ? 'active' : '' ?>">
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
                <i class="bi bi-hdd-network-fill"></i> SMTP Accounts & Pools
            </a>

            <div class="nav-section">Audience & Content</div>
            <a href="<?= url('/contacts') ?>" class="nav-link <?= ($activeNav ?? '') === 'contacts' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Contacts & Lists
            </a>
            <a href="<?= url('/templates') ?>" class="nav-link <?= ($activeNav ?? '') === 'templates' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-code-fill"></i> Email Templates
            </a>
            <a href="<?= url('/suppressions') ?>" class="nav-link <?= ($activeNav ?? '') === 'suppressions' ? 'active' : '' ?>">
                <i class="bi bi-shield-slash-fill"></i> Suppression List
            </a>

            <div class="nav-section">Telemetry & Dev</div>
            <a href="<?= url('/analytics') ?>" class="nav-link <?= ($activeNav ?? '') === 'analytics' ? 'active' : '' ?>">
                <i class="bi bi-graph-up-arrow"></i> Deliverability Analytics
            </a>
            <a href="<?= url('/logs/delivery') ?>" class="nav-link <?= ($activeNav ?? '') === 'delivery_logs' ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Delivery Logs
            </a>
            <a href="<?= url('/api-keys') ?>" class="nav-link <?= ($activeNav ?? '') === 'api_keys' ? 'active' : '' ?>">
                <i class="bi bi-key-fill"></i> REST API Keys
            </a>

            <?php if (($user = auth_user()) && ($user['role'] ?? '') === 'admin'): ?>
                <div class="nav-section text-warning"><i class="bi bi-shield-lock-fill me-1"></i> Super Admin</div>
                <a href="<?= url('/admin/dashboard') ?>" class="nav-link text-warning-emphasis <?= str_starts_with($activeNav ?? '', 'admin') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Admin Control Center
                </a>
                <a href="<?= url('/admin/users') ?>" class="nav-link <?= ($activeNav ?? '') === 'admin_users' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill"></i> Manage Users
                </a>
                <a href="<?= url('/admin/system-health') ?>" class="nav-link <?= ($activeNav ?? '') === 'admin_health' ? 'active' : '' ?>">
                    <i class="bi bi-heart-pulse-fill"></i> System Telemetry
                </a>
            <?php endif; ?>

            <div class="nav-section">Configuration</div>
            <a href="<?= url('/settings') ?>" class="nav-link <?= ($activeNav ?? '') === 'system_settings' ? 'active' : '' ?>">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
            <a href="<?= url('/profile') ?>" class="nav-link <?= ($activeNav ?? '') === 'profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i> User Profile
            </a>
        </div>
    </div>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="w-100 d-flex flex-column">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-2 border-bottom">
            <div class="container-fluid p-0">
                <button class="btn btn-outline-secondary btn-sm me-3" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <!-- Search box -->
                <div class="d-none d-md-flex align-items-center position-relative" style="width: 300px;">
                    <i class="bi bi-search position-absolute text-secondary ps-3"></i>
                    <input type="text" class="form-control form-control-sm ps-5 bg-body-tertiary border-secondary-subtle" placeholder="Search campaigns, contacts... (Ctrl+K)">
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <!-- Documentation Link -->
                    <a href="<?= url('/docs') ?>" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1" target="_blank">
                        <i class="bi bi-book"></i> Docs
                    </a>

                    <!-- Theme Toggle -->
                    <button class="btn btn-sm btn-outline-secondary" id="themeToggleBtn" title="Toggle Light/Dark Theme">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg p-0" style="width: 320px;">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold small">System Notifications</h6>
                                <span class="badge bg-primary-subtle text-primary">All Systems Green</span>
                            </div>
                            <div class="p-2 d-flex flex-column gap-1" style="max-height: 240px; overflow-y: auto;">
                                <div class="p-2 rounded bg-body-tertiary small">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong class="text-success"><i class="bi bi-check-circle me-1"></i>Worker Active</strong>
                                        <small class="text-muted">Just now</small>
                                    </div>
                                    <div class="text-secondary mt-1">Queue worker processed successfully. Zero errors.</div>
                                </div>
                                <div class="p-2 rounded bg-body-tertiary small">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong class="text-primary"><i class="bi bi-shield-check me-1"></i>Security Audit</strong>
                                        <small class="text-muted">1 hr ago</small>
                                    </div>
                                    <div class="text-secondary mt-1">Database WAL journaling verified.</div>
                                </div>
                            </div>
                            <div class="p-2 border-top text-center">
                                <a href="<?= url('/logs/delivery') ?>" class="text-decoration-none small text-primary fw-semibold">View Delivery Logs</a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center me-2" style="width: 34px; height: 34px; font-weight: 600; font-size: 14px;">
                                <?= strtoupper(substr(auth_user()['name'] ?? 'A', 0, 1)) ?>
                            </span>
                            <span class="fw-semibold small d-none d-sm-inline"><?= e(auth_user()['name'] ?? 'Admin') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small"><?= e(auth_user()['name'] ?? 'Admin') ?></div>
                                <div class="text-muted small"><?= e(auth_user()['email'] ?? 'admin@mailflow.local') ?></div>
                                <span class="badge bg-secondary-subtle text-secondary text-uppercase mt-1" style="font-size: 10px;"><?= e(auth_user()['role'] ?? 'Admin') ?></span>
                            </li>
                            <?php if (($user = auth_user()) && ($user['role'] ?? '') === 'admin'): ?>
                                <li><a class="dropdown-item text-warning" href="<?= url('/admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i> Super Admin Panel</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="bi bi-person me-2"></i> User Profile</a></li>
                            <li><a class="dropdown-item" href="<?= url('/api-keys') ?>"><i class="bi bi-key me-2"></i> API Keys</a></li>
                            <li><a class="dropdown-item" href="<?= url('/settings') ?>"><i class="bi bi-gear me-2"></i> System Settings</a></li>
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
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= e($flashSuccess) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($flashError = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
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
        <footer class="py-3 px-4 border-top text-muted small d-flex justify-content-between align-items-center bg-body-tertiary mt-auto">
            <span>&copy; <?= date('Y') ?> <strong><?= e(config('app.name', 'MailFlow')) ?></strong> &bull; Enterprise Multi-SMTP Architecture.</span>
            <span>Release <?= e(config('app.version', '2.0.0')) ?> &bull; PHP <?= phpversion() ?></span>
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

<!-- MailFlow App JS -->
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
