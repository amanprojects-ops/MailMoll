<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-list-check text-warning me-2"></i>Global Platform Audit Trail</h4>
        <p class="text-secondary small mb-0">Immutable record of mutating actions, user logins, campaign dispatches, and configuration changes.</p>
    </div>
    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"><?= count($auditLogs) ?> Total Events Recorded</span>
</div>

<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead>
                <tr class="text-secondary text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">Event Action</th>
                    <th>Resource</th>
                    <th>Actor</th>
                    <th>IP Address</th>
                    <th>Metadata</th>
                    <th class="pe-4 text-end">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($auditLogs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            No audit events logged yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td class="ps-4">
                                <code class="text-warning"><?= e($log['action']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?= e($log['resource_type']) ?><?= $log['resource_id'] ? ' #' . $log['resource_id'] : '' ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-light"><?= e($log['user_email'] ?? 'System/Guest') ?></strong>
                            </td>
                            <td class="font-monospace text-secondary">
                                <?= e($log['ip_address'] ?? '127.0.0.1') ?>
                            </td>
                            <td class="font-monospace text-muted small" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($log['details_json'] ?? '-') ?>
                            </td>
                            <td class="pe-4 text-end text-secondary">
                                <?= e(date('M j, Y H:i:s', strtotime($log['created_at']))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
