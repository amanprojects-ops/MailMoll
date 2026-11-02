<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Super Admin Panel') ?> - <?= e(config('app.name', 'MailFlow')) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Toastify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <!-- Admin Theme CSS -->
    <link href="<?= asset('css/marketing.css') ?>" rel="stylesheet">

    <style>
        :root {
            --admin-sidebar-width: 260px;
            --admin-accent: #f59e0b;
        }
        body {
            background-color: #0c0f17;
            color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100vh;
        }
        #admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        #admin-sidebar {
            width: var(--admin-sidebar-width);
            background: #111522;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        #admin-sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            margin: 0.15rem 0.75rem;
            transition: all 0.15s ease;
        }
        .admin-nav-item:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.05);
        }
        .admin-nav-item.active {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.15));
            border-left: 3px solid #f59e0b;
            font-weight: 600;
        }
        .admin-nav-item.active i {
            color: #f59e0b;
        }
        .admin-section-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            color: #64748b;
            font-weight: 700;
            padding: 1.25rem 1.25rem 0.35rem;
        }
        #admin-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .admin-topbar {
            background: rgba(17, 21, 34, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.75rem 1.5rem;
        }
        .admin-card {
            background: #111522;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<div id="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside id="admin-sidebar">
        <div class="sidebar-brand">
            <a href="<?= url('/admin/dashboard') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark p-2 rounded-3"><i class="bi bi-shield-lock-fill fs-6"></i></span>
                <div>
                    <div class="fw-bold text-light lh-1">MailFlow</div>
                    <small class="text-warning text-uppercase letter-spacing-1 fw-bold" style="font-size: 9px;">Super Admin</small>
                </div>
            </a>
            <a href="<?= url('/dashboard') ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Switch to Customer Dashboard" style="font-size: 11px;">
                <i class="bi bi-box-arrow-up-right me-1"></i>App
            </a>
        </div>

        <div class="overflow-y-auto flex-grow-1 py-2">
            <div class="admin-section-label">Fleet Overview</div>
            <a href="<?= url('/admin/dashboard') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Executive Dashboard
            </a>
            <a href="<?= url('/admin/system-health') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'health' ? 'active' : '' ?>">
                <i class="bi bi-heart-pulse-fill"></i> System Telemetry
            </a>
            <a href="<?= url('/admin/backups') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'backups' ? 'active' : '' ?>">
                <i class="bi bi-database-down"></i> Backups & Snapshots
            </a>

            <div class="admin-section-label">Identity & Access</div>
            <a href="<?= url('/admin/users') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'users' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> User Directory
            </a>
            <a href="<?= url('/admin/subscriptions') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'subscriptions' ? 'active' : '' ?>">
                <i class="bi bi-credit-card-2-front-fill"></i> Subscriptions & Tiers
            </a>
            <a href="<?= url('/admin/security') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'security' ? 'active' : '' ?>">
                <i class="bi bi-shield-check"></i> Security & 2FA
            </a>

            <div class="admin-section-label">Global Telemetry</div>
            <a href="<?= url('/admin/queue') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'queue' ? 'active' : '' ?>">
                <i class="bi bi-hourglass-split"></i> Global Queue
            </a>
            <a href="<?= url('/admin/email-logs') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'email_logs' ? 'active' : '' ?>">
                <i class="bi bi-journal-code"></i> SMTP Delivery Logs
            </a>
            <a href="<?= url('/admin/audit-logs') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'audit_logs' ? 'active' : '' ?>">
                <i class="bi bi-list-check"></i> Audit Trails
            </a>

            <div class="admin-section-label">Infrastructure</div>
            <a href="<?= url('/admin/settings') ?>" class="admin-nav-item <?= ($adminNav ?? '') === 'settings' ? 'active' : '' ?>">
                <i class="bi bi-sliders"></i> Global Settings
            </a>
        </div>

        <div class="p-3 border-top border-secondary-subtle small text-secondary">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span>Database:</span>
                <span class="badge bg-success-subtle text-success">SQLite 3 WAL</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span>PHP Version:</span>
                <span class="text-light"><?= phpversion() ?></span>
            </div>
        </div>
    </aside>

    <!-- Admin Content Area -->
    <div id="admin-content">
        <!-- Topbar -->
        <header class="admin-topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-bold text-light"><?= e($title ?? 'Super Admin Panel') ?></span>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill small">
                    <i class="bi bi-lightning-charge me-1"></i>Master Cluster
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="<?= url('/dashboard') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Customer App
                </a>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-light" data-bs-toggle="dropdown">
                        <span class="avatar bg-warning text-dark fw-bold rounded-circle p-1 d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; font-size: 13px;">
                            <?= strtoupper(substr(auth_user()['name'] ?? 'A', 0, 1)) ?>
                        </span>
                        <span class="small fw-semibold"><?= e(auth_user()['name'] ?? 'Super Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="<?= url('/settings') ?>"><i class="bi bi-gear me-2"></i> User Settings</a></li>
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
        </header>

        <!-- Flash Messages -->
        <div class="px-4 pt-3">
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

        <!-- Main Admin View -->
        <main class="flex-grow-1 p-4">
            <?= $content ?>
        </main>

        <!-- Admin Footer -->
        <footer class="p-3 px-4 border-top border-secondary-subtle small text-secondary d-flex justify-content-between align-items-center bg-dark">
            <div>MailFlow Super Admin Control Center &bull; Protected Environment</div>
            <div>Build <?= e(config('app.version', '2.0.0')) ?> &bull; Memory: <?= round(memory_get_peak_usage(true) / 1024 / 1024, 2) ?> MB</div>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
</body>
</html>
