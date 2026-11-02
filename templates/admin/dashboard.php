<!-- Super Admin Dashboard -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-speedometer2 text-warning me-2"></i>Executive Fleet Control</h4>
        <p class="text-secondary small mb-0">High-level telemetry across instances, subscriber databases, and queue dispatches.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/backups') ?>" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-database-down me-1"></i> Snapshot DB
        </a>
        <a href="<?= url('/admin/system-health') ?>" class="btn btn-warning btn-sm fw-semibold text-dark">
            <i class="bi bi-heart-pulse-fill me-1"></i> Diagnostics
        </a>
    </div>
</div>

<!-- Worker Alert Banner -->
<div class="admin-card p-3 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill <?= $workerActive ? 'bg-success' : 'bg-warning text-dark' ?> p-2">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                Worker Engine: <?= $workerActive ? 'Operational (Daemon Active)' : 'Idle / Cron Mode' ?>
            </span>
            <span class="text-secondary small">
                Last Heartbeat: <strong class="text-light"><?= e($workerHeartbeat) ?></strong>
            </span>
        </div>
        <div class="small text-secondary">
            Disk Storage: <strong class="text-light"><?= e($diskFree) ?> GB Free</strong> of <?= e($diskTotal) ?> GB &bull; SQLite WAL Mode Active
        </div>
    </div>
</div>

<!-- 8 Key Executive Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold">Total Users</span>
                    <h3 class="fw-bold my-1 text-light"><?= $totalUsers ?></h3>
                    <span class="badge bg-primary-subtle text-primary small">+<?= $newUsersWeek ?> this week</span>
                </div>
                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold">Active Users</span>
                    <h3 class="fw-bold my-1 text-success"><?= $activeUsers ?></h3>
                    <span class="badge bg-success-subtle text-success small">100% Verified</span>
                </div>
                <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success fs-4">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold">Pending Queue</span>
                    <h3 class="fw-bold my-1 text-warning"><?= number_format($totalQueued) ?></h3>
                    <span class="badge bg-warning-subtle text-warning small"><?= number_format($totalProcessing) ?> In Processing</span>
                </div>
                <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning fs-4">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-secondary small fw-semibold">Database Size</span>
                    <h3 class="fw-bold my-1 text-info"><?= $dbSize ?> <span class="fs-6 fw-normal text-secondary">MB</span></h3>
                    <span class="badge bg-info-subtle text-info small">SQLite 3 Journal</span>
                </div>
                <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info fs-4">
                    <i class="bi bi-database"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Users Table -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-light mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Recent Registrations</h5>
                <a href="<?= url('/admin/users') ?>" class="small text-warning text-decoration-none">View All Users <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 small">
                    <thead>
                        <tr class="text-secondary border-bottom border-secondary-subtle">
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $ru): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-light"><?= e($ru['name']) ?></div>
                                    <div class="text-secondary small"><?= e($ru['email']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= e($ru['role']) ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $ru['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                        <?= e($ru['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-secondary">
                                    <?= e(date('M j, Y', strtotime($ru['created_at']))) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Security Events -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-light mb-0"><i class="bi bi-shield-lock text-warning me-2"></i>Security Audit Activity</h5>
                <a href="<?= url('/admin/audit-logs') ?>" class="small text-warning text-decoration-none">Audit Log <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 small">
                    <thead>
                        <tr class="text-secondary border-bottom border-secondary-subtle">
                            <th>Event Action</th>
                            <th>Actor</th>
                            <th>IP Address</th>
                            <th class="text-end">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSecurity)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">No audit logs recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentSecurity as $sec): ?>
                                <tr>
                                    <td>
                                        <code class="text-info"><?= e($sec['action']) ?></code>
                                    </td>
                                    <td class="text-light">
                                        <?= e($sec['user_email'] ?? 'System') ?>
                                    </td>
                                    <td class="text-secondary font-monospace small">
                                        <?= e($sec['ip_address'] ?? '127.0.0.1') ?>
                                    </td>
                                    <td class="text-end text-secondary small">
                                        <?= e(date('H:i:s', strtotime($sec['created_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
