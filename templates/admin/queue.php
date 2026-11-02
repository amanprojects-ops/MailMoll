<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-hourglass-split text-warning me-2"></i>Global Cross-Instance Queue</h4>
        <p class="text-secondary small mb-0">Inspect atomic job dispatch across all tenant campaigns and microservice transactional pipelines.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?= url('/queue/clear-failed') ?>" method="POST" onsubmit="return confirm('Purge failed jobs?');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Flush Failed
            </button>
        </form>
    </div>
</div>

<!-- Status Counters -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-3">
        <div class="admin-card p-3 text-center">
            <span class="text-secondary small d-block">Pending Dispatch</span>
            <strong class="fs-4 text-warning"><?= number_format($counts['pending'] ?? 0) ?></strong>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="admin-card p-3 text-center">
            <span class="text-secondary small d-block">Processing (Locked)</span>
            <strong class="fs-4 text-info"><?= number_format($counts['processing'] ?? 0) ?></strong>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="admin-card p-3 text-center">
            <span class="text-secondary small d-block">Delivered (Sent)</span>
            <strong class="fs-4 text-success"><?= number_format($counts['sent'] ?? 0) ?></strong>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="admin-card p-3 text-center">
            <span class="text-secondary small d-block">Failed Attempts</span>
            <strong class="fs-4 text-danger"><?= number_format($counts['failed'] ?? 0) ?></strong>
        </div>
    </div>
</div>

<!-- Queue Items Table -->
<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead>
                <tr class="text-secondary text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">Job ID</th>
                    <th>Recipient</th>
                    <th>Subject</th>
                    <th>SMTP Assigned</th>
                    <th>Status</th>
                    <th>Attempts</th>
                    <th>Scheduled</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($queueItems)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-secondary">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                            Queue is completely empty. No pending or failed jobs.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($queueItems as $q): ?>
                        <tr>
                            <td class="ps-4 font-monospace text-muted">#<?= $q['id'] ?></td>
                            <td>
                                <strong class="text-light"><?= e($q['recipient_email']) ?></strong>
                            </td>
                            <td class="text-secondary" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($q['subject']) ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?= e($q['smtp_name'] ?? 'Auto-Assigned') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($q['status'] === 'sent'): ?>
                                    <span class="badge bg-success-subtle text-success">Sent</span>
                                <?php elseif ($q['status'] === 'pending'): ?>
                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                <?php elseif ($q['status'] === 'processing'): ?>
                                    <span class="badge bg-info-subtle text-info">Processing</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger"><?= e($q['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $q['attempts'] ?> / <?= $q['max_attempts'] ?></td>
                            <td class="text-secondary"><?= e(date('M j, H:i', strtotime($q['scheduled_at']))) ?></td>
                            <td class="pe-4 text-end">
                                <?php if (in_array($q['status'], ['failed', 'retry'])): ?>
                                    <form action="<?= url('/queue/retry/' . $q['id']) ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-warning btn-sm py-0 px-2" title="Retry">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
