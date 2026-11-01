<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">System Audit Logs</h4>
        <p class="text-muted small mb-0">Immutable audit trail of administrator and system actions with client IP traces.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Resource</th>
                        <th>IP Address</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No audit events logged.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $a): ?>
                            <tr>
                                <td class="font-monospace small">#<?= $a['id'] ?></td>
                                <td>
                                    <div class="small fw-semibold"><?= e($a['user_name'] ?? 'System/Cron') ?></div>
                                    <div class="text-muted" style="font-size: 11px;"><?= e($a['user_email'] ?? '') ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small"><?= e($a['action']) ?></span>
                                </td>
                                <td class="small text-muted">
                                    <?= e($a['resource_type']) ?> <?= $a['resource_id'] ? '#' . $a['resource_id'] : '' ?>
                                </td>
                                <td class="small font-monospace text-muted">
                                    <?= e($a['ip_address'] ?? '127.0.0.1') ?>
                                </td>
                                <td class="small font-monospace" style="font-size: 11px; max-width: 250px;">
                                    <?= e($a['details_json'] ?: '-') ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('M d, Y H:i:s', strtotime($a['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
