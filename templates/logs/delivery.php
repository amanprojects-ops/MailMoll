<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Delivery Logs</h4>
        <p class="text-muted small mb-0">Trace SMTP acceptance codes, handshakes, bounce events, and MTA statuses.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Recipient</th>
                        <th>Campaign</th>
                        <th>SMTP Node</th>
                        <th>Event Type</th>
                        <th>Metadata / Response</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                No delivery events logged yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr>
                                <td class="font-monospace small">#<?= $l['id'] ?></td>
                                <td class="small fw-semibold"><?= e($l['recipient_email']) ?></td>
                                <td class="small text-muted"><?= e($l['campaign_title'] ?? 'Standalone/Transactional') ?></td>
                                <td class="small"><?= e($l['smtp_name'] ?? 'Direct') ?></td>
                                <td>
                                    <?php if ($l['event_type'] === 'accepted'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i> Accepted</span>
                                    <?php elseif ($l['event_type'] === 'rejected'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                                    <?php elseif ($l['event_type'] === 'bounced'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> Bounced</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($l['event_type']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small font-monospace" style="font-size: 11px; max-width: 250px;">
                                    <?= e($l['meta_json'] ?: '-') ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('M d, Y H:i:s', strtotime($l['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
