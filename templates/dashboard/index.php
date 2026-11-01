<!-- Header with Quick Actions -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Operational Overview</h4>
        <p class="text-muted small mb-0">Real-time health, quota status, and delivery metrics across all SMTP providers.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="runWorkerBtn">
            <i class="bi bi-play-circle-fill"></i> Run Worker Batch
        </button>
        <a href="<?= url('/campaigns/new') ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-circle-fill"></i> New Campaign
        </a>
    </div>
</div>

<!-- Worker Health Alert Banner -->
<div class="card p-3 mb-4 border-0 bg-light">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill <?= $workerActive ? 'bg-success' : 'bg-warning text-dark' ?> p-2">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                Worker Status: <?= $workerActive ? 'Active (Cron/Daemon Running)' : 'Idle / Standby' ?>
            </span>
            <span class="text-muted small">
                Last Heartbeat: <strong><?= e($workerHeartbeat ?: 'Never') ?></strong>
            </span>
        </div>
        <div class="small text-muted">
            <i class="bi bi-info-circle me-1"></i> Trigger manual batch above or configure cron <code>* * * * * php cron/email-worker.php</code>
        </div>
    </div>
</div>

<!-- Key Stat Cards (Section 3) -->
<div class="row g-3 mb-4">
    <!-- Active SMTP Accounts -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold">SMTP Accounts</span>
                    <h3 class="fw-bold my-1"><?= $activeSmtp ?> <span class="text-muted fs-6 fw-normal">/ <?= $totalSmtp ?></span></h3>
                    <span class="badge bg-primary bg-opacity-10 text-primary small">Active Nodes</span>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-server"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Emails Queued -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold">Emails Queued</span>
                    <h3 class="fw-bold my-1"><?= number_format($queued) ?></h3>
                    <span class="badge bg-warning bg-opacity-10 text-warning small">Pending Dispatch</span>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Emails Sent & Accepted -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold">SMTP Accepted</span>
                    <h3 class="fw-bold my-1 text-success"><?= number_format($sent) ?></h3>
                    <span class="badge bg-success bg-opacity-10 text-success small">Delivered to MTA</span>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-all"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Acceptance Rate -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold">Delivery Rate</span>
                    <h3 class="fw-bold my-1"><?= $deliveryRate ?>%</h3>
                    <span class="badge bg-info bg-opacity-10 text-info small">Failures: <?= number_format($failed) ?></span>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-pie-chart-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- SMTP Quota Utilization (Section 4 & 5) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-speedometer2 text-primary me-2"></i> SMTP Provider Quota Utilization</span>
                <a href="<?= url('/smtp') ?>" class="btn btn-sm btn-link text-decoration-none">Manage Accounts</a>
            </div>
            <div class="card-body">
                <?php if (empty($smtpAccounts)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-hdd-network fs-2 d-block mb-2"></i>
                        No SMTP accounts added yet. <a href="<?= url('/smtp') ?>">Add your first SMTP</a> to start sending.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($smtpAccounts as $acc): ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold small"><?= e($acc['name']) ?></span>
                                        <span class="badge badge-status badge-<?= $acc['status'] ?>"><?= e($acc['status']) ?></span>
                                    </div>
                                    <span class="text-muted small">
                                        Today: <strong><?= $acc['daily_used'] ?></strong> / <?= $acc['daily_limit'] ?: '∞' ?>
                                    </span>
                                </div>
                                <div class="quota-bar">
                                    <div class="quota-progress <?= $acc['daily_percent'] > 90 ? 'bg-danger' : ($acc['daily_percent'] > 70 ? 'bg-warning' : 'bg-primary') ?>" 
                                         style="width: <?= $acc['daily_percent'] ?>%;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted" style="font-size: 11px;">
                                    <span>Host: <?= e($acc['host']) ?>:<?= e($acc['port']) ?> (<?= strtoupper($acc['encryption']) ?>)</span>
                                    <span>Hourly: <?= $acc['hourly_used'] ?> / <?= $acc['hourly_limit'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Active Campaigns Status (Section 6) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-send text-primary me-2"></i> Recent Campaigns</span>
                <a href="<?= url('/campaigns') ?>" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($activeCampaigns)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-send-x fs-2 d-block mb-2"></i>
                        No campaigns created yet. <a href="<?= url('/campaigns/new') ?>">Create your first campaign</a>.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeCampaigns as $camp): 
                                    $pct = $camp['total_recipients'] > 0 ? round(($camp['sent_count'] / $camp['total_recipients']) * 100) : 0;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small"><?= e($camp['title']) ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= e($camp['subject']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-<?= $camp['status'] ?>"><?= e($camp['status']) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: <?= $pct ?>%;"></div>
                                                </div>
                                                <span class="small text-muted"><?= $pct ?>%</span>
                                            </div>
                                            <div class="text-muted" style="font-size: 10px;"><?= $camp['sent_count'] ?> / <?= $camp['total_recipients'] ?></div>
                                        </td>
                                        <td class="small text-muted"><?= date('M d, H:i', strtotime($camp['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Delivery Logs (Section 9) -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-clock-history text-primary me-2"></i> Live Delivery Feed</span>
        <a href="<?= url('/logs/delivery') ?>" class="btn btn-sm btn-link text-decoration-none">Full Diagnostic Logs</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentEvents)): ?>
            <div class="text-center py-4 text-muted">
                No delivery logs recorded yet. Once queue worker runs, dispatch logs will appear in real time.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Recipient</th>
                            <th>Campaign</th>
                            <th>SMTP Node</th>
                            <th>Status Event</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $ev): ?>
                            <tr>
                                <td class="fw-semibold small"><?= e($ev['recipient_email']) ?></td>
                                <td class="small text-muted"><?= e($ev['campaign_title'] ?? 'Standalone/Transactional') ?></td>
                                <td class="small"><?= e($ev['smtp_name'] ?? 'Direct') ?></td>
                                <td>
                                    <?php if ($ev['event_type'] === 'accepted'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success small"><i class="bi bi-check2 me-1"></i> Accepted</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger small"><i class="bi bi-x me-1"></i> <?= e($ev['event_type']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= date('H:i:s M d', strtotime($ev['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
