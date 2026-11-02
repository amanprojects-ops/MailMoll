<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-heart-pulse-fill text-danger me-2"></i>System Telemetry & Diagnostics</h4>
        <p class="text-secondary small mb-0">Hardware environment, PHP runtime parameters, SQLite database locks, and filesystem permissions.</p>
    </div>
    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i>All Core Systems Healthy</span>
</div>

<div class="row g-4 mb-4">
    <!-- SQLite Diagnostic Card -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold text-light mb-3"><i class="bi bi-database text-warning me-2"></i>Database Engine (SQLite 3)</h5>
            <ul class="list-unstyled d-flex flex-column gap-3 mb-0 small">
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary">Journaling Mode:</span>
                    <span class="badge bg-success text-dark fw-bold"><?= strtoupper($walMode) ?></span>
                </li>
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary">Estimated Database Size:</span>
                    <span class="fw-bold text-light"><?= round($dbSizeBytes / 1024 / 1024, 2) ?> MB</span>
                </li>
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary">Concurrency Safety:</span>
                    <span class="badge bg-primary-subtle text-primary">Atomic Row Locking</span>
                </li>
                <li class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary">Foreign Key Constraints:</span>
                    <span class="badge bg-success-subtle text-success">Enabled (PRAGMA foreign_keys = ON)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Storage Permissions Card -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold text-light mb-3"><i class="bi bi-folder-check text-info me-2"></i>Filesystem & Permissions</h5>
            <ul class="list-unstyled d-flex flex-column gap-3 mb-0 small">
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary"><code>/storage</code>:</span>
                    <span class="badge <?= $storageWritable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                        <?= $storageWritable ? 'Writable (0755)' : 'Permission Denied' ?>
                    </span>
                </li>
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary"><code>/storage/database</code>:</span>
                    <span class="badge <?= $dbWritable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                        <?= $dbWritable ? 'Writable (0755)' : 'Permission Denied' ?>
                    </span>
                </li>
                <li class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary-subtle">
                    <span class="text-secondary"><code>/storage/logs</code>:</span>
                    <span class="badge <?= $logsWritable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                        <?= $logsWritable ? 'Writable (0755)' : 'Permission Denied' ?>
                    </span>
                </li>
                <li class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary"><code>/storage/backups</code>:</span>
                    <span class="badge <?= $backupsWritable ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                        <?= $backupsWritable ? 'Writable (0755)' : 'Permission Denied' ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- PHP Extensions & Limits -->
<div class="admin-card p-4">
    <h5 class="fw-bold text-light mb-3"><i class="bi bi-cpu text-primary me-2"></i>PHP Runtime Parameters</h5>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="p-3 rounded bg-dark border border-secondary-subtle text-center">
                <small class="text-secondary d-block mb-1">PHP Version</small>
                <strong class="text-light fs-5"><?= phpversion() ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded bg-dark border border-secondary-subtle text-center">
                <small class="text-secondary d-block mb-1">Memory Limit</small>
                <strong class="text-light fs-5"><?= e($memoryLimit) ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded bg-dark border border-secondary-subtle text-center">
                <small class="text-secondary d-block mb-1">Max Execution Time</small>
                <strong class="text-light fs-5"><?= e($maxExecution) ?>s</strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 rounded bg-dark border border-secondary-subtle text-center">
                <small class="text-secondary d-block mb-1">Max Upload Filesize</small>
                <strong class="text-light fs-5"><?= e($uploadMax) ?></strong>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top border-secondary-subtle">
        <h6 class="text-light small fw-bold mb-3">Required PHP Extensions:</h6>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($extensions as $ext => $loaded): ?>
                <span class="badge <?= $loaded ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-3 py-2">
                    <i class="bi bi-<?= $loaded ? 'check-circle' : 'x-circle' ?> me-1"></i><?= e($ext) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
