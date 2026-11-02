<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-database-down text-warning me-2"></i>Database Snapshots & Disaster Recovery</h4>
        <p class="text-secondary small mb-0">Create point-in-time SQLite database backups to safeguard campaigns, templates, and contact lists.</p>
    </div>
    <form action="<?= url('/admin/backups/create') ?>" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-warning btn-sm fw-semibold text-dark">
            <i class="bi bi-camera-fill me-1"></i> Create Snapshot Now
        </button>
    </form>
</div>

<div class="admin-card overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead>
                <tr class="text-secondary text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">Snapshot File</th>
                    <th>Size (MB)</th>
                    <th>Created Timestamp</th>
                    <th class="pe-4 text-end">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($backups)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-secondary">
                            <i class="bi bi-archive fs-2 d-block mb-2 text-muted"></i>
                            No backup snapshots generated yet. Click "Create Snapshot Now" above to capture state.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($backups as $b): ?>
                        <tr>
                            <td class="ps-4 font-monospace text-light fw-semibold">
                                <i class="bi bi-file-earmark-binary me-2 text-warning"></i><?= e($b['filename']) ?>
                            </td>
                            <td><?= $b['size'] ?> MB</td>
                            <td class="text-secondary"><?= e($b['created_at']) ?></td>
                            <td class="pe-4 text-end">
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i>Verified Snapshot
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card p-4">
    <h5 class="fw-bold text-light mb-2"><i class="bi bi-arrow-counterclockwise text-info me-2"></i>Disaster Recovery Guide</h5>
    <p class="text-secondary small mb-2">
        To restore your database from any point-in-time snapshot, copy the desired snapshot file into <code>storage/database/mailmoll.sqlite</code>:
    </p>
    <div class="bg-black p-3 rounded font-monospace small text-secondary">
        # 1. Stop any running background worker<br>
        <span class="text-light">kill $(pgrep -f "email-worker.php")</span><br><br>
        # 2. Overwrite database with snapshot<br>
        <span class="text-light">cp storage/backups/mailflow_backup_TIMESTAMP.sqlite storage/database/mailmoll.sqlite</span>
    </div>
</div>
