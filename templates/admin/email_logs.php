<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-journal-code text-warning me-2"></i>Global SMTP Handshake Logs</h4>
        <p class="text-secondary small mb-0">Raw socket transmission transcripts, RFC status codes, and latency diagnostics across all active nodes.</p>
    </div>
    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"><?= count($attempts) ?> Latest Dispatches</span>
</div>

<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead>
                <tr class="text-secondary text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">Recipient</th>
                    <th>Subject</th>
                    <th>SMTP Node</th>
                    <th>Status Code</th>
                    <th>Duration</th>
                    <th>Response Transcript</th>
                    <th class="pe-4 text-end">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attempts)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 text-muted"></i>
                            No SMTP handshake logs recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attempts as $att): ?>
                        <tr>
                            <td class="ps-4 font-monospace text-light">
                                <?= e($att['recipient_email'] ?? 'Unknown') ?>
                            </td>
                            <td class="text-secondary" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($att['subject'] ?? 'Notification') ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?= e($att['smtp_name'] ?? 'Direct Relay') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (str_starts_with($att['status_code'] ?? '', '2')): ?>
                                    <span class="badge bg-success-subtle text-success"><?= e($att['status_code']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger"><?= e($att['status_code'] ?? 'ERR') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary font-monospace"><?= $att['duration_ms'] ?? 0 ?> ms</td>
                            <td class="text-secondary font-monospace" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($att['response_text'] ?? 'OK') ?>
                            </td>
                            <td class="pe-4 text-end text-secondary">
                                <?= e(date('H:i:s', strtotime($att['attempted_at']))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
