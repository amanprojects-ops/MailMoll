<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Email Queue Monitor</h4>
        <p class="text-muted small mb-0">Inspect atomic queue states, worker leases, delivery retries, and errors.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?= url('/queue/clear-failed') ?>" method="POST" class="d-inline m-0">
            <?= csrf_field() ?>
            <button type="button" class="btn btn-outline-danger btn-sm btn-confirm-delete" data-message="Purge all failed and cancelled jobs from the queue table?">
                <i class="bi bi-trash3 me-1"></i> Clear Failed Jobs
            </button>
        </form>
        <button class="btn btn-primary btn-sm" id="runWorkerBtn">
            <i class="bi bi-play-circle-fill me-1"></i> Trigger Worker Now
        </button>
    </div>
</div>

<!-- Status Filter Pills -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= url('/queue?status=all') ?>" class="btn btn-sm <?= $statusFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">
        All (<?= $counts['total'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=pending') ?>" class="btn btn-sm <?= $statusFilter === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' ?>">
        Pending (<?= $counts['pending'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=processing') ?>" class="btn btn-sm <?= $statusFilter === 'processing' ? 'btn-primary' : 'btn-outline-primary' ?>">
        Processing (<?= $counts['processing'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=retry') ?>" class="btn btn-sm <?= $statusFilter === 'retry' ? 'btn-info text-dark' : 'btn-outline-info text-dark' ?>">
        Retry Backoff (<?= $counts['retry'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=sent') ?>" class="btn btn-sm <?= $statusFilter === 'sent' ? 'btn-success' : 'btn-outline-success' ?>">
        Sent (<?= $counts['sent'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=failed') ?>" class="btn btn-sm <?= $statusFilter === 'failed' ? 'btn-danger' : 'btn-outline-danger' ?>">
        Failed (<?= $counts['failed'] ?? 0 ?>)
    </a>
    <a href="<?= url('/queue?status=cancelled') ?>" class="btn btn-sm <?= $statusFilter === 'cancelled' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
        Cancelled (<?= $counts['cancelled'] ?? 0 ?>)
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Campaign</th>
                        <th>Recipient</th>
                        <th>Selected SMTP</th>
                        <th>Status</th>
                        <th>Attempts</th>
                        <th>Scheduled</th>
                        <th>Last Error</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jobs)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No queue jobs matching filter <strong><?= e($statusFilter) ?></strong>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td class="font-monospace small">#<?= $job['id'] ?></td>
                                <td class="small">
                                    <?= e($job['campaign_title'] ?? 'Transactional') ?>
                                    <div class="text-muted" style="font-size: 11px;"><?= e($job['subject']) ?></div>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?= e($job['recipient_name'] ?: 'Recipient') ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= e($job['recipient_email']) ?></div>
                                </td>
                                <td class="small">
                                    <?= e($job['smtp_name'] ?? 'Auto-Assigned') ?>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-<?= $job['status'] ?>"><?= e($job['status']) ?></span>
                                </td>
                                <td class="small">
                                    <?= $job['attempts'] ?> / <?= $job['max_attempts'] ?>
                                </td>
                                <td class="small text-muted" style="font-size: 11px;">
                                    <?= date('M d, H:i:s', strtotime($job['scheduled_at'])) ?>
                                </td>
                                <td class="small text-danger" style="max-width: 220px; font-size: 11px;">
                                    <?php if ($job['last_error']): ?>
                                        <span title="<?= e($job['last_error']) ?>" class="d-inline-block text-truncate" style="max-width: 200px;">
                                            <?= e($job['last_error']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <?php if (in_array($job['status'], ['failed', 'retry', 'cancelled'])): ?>
                                            <form action="<?= url('/queue/retry/' . $job['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-primary" title="Force Retry">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (in_array($job['status'], ['pending', 'retry'])): ?>
                                            <form action="<?= url('/queue/cancel/' . $job['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-secondary" title="Cancel Dispatch">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
