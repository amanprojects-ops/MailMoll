<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">System Settings & Infrastructure</h4>
        <p class="text-muted small mb-0">Manage global application variables, cron scheduler settings, and database backups.</p>
    </div>
    <a href="<?= url('/settings/backup') ?>" class="btn btn-outline-success btn-sm">
        <i class="bi bi-download me-1"></i> Download SQLite Backup
    </a>
</div>

<div class="row g-4">
    <!-- Application Configuration -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-bold small">
                <i class="bi bi-sliders text-primary me-1"></i> General Application Parameters
            </div>
            <div class="card-body">
                <form action="<?= url('/settings/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Application Title</label>
                            <input type="text" name="app_title" class="form-control" value="<?= e($settings['app_title']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Company / Organization</label>
                            <input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default Sender Email</label>
                            <input type="email" name="default_from_email" class="form-control" value="<?= e($settings['default_from_email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Default Sender Name</label>
                            <input type="text" name="default_from_name" class="form-control" value="<?= e($settings['default_from_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Max Retry Attempts (Transient 4xx)</label>
                            <input type="number" name="max_retry_attempts" class="form-control" value="<?= e($settings['max_retry_attempts']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Worker Batch Size</label>
                            <input type="number" name="batch_size" class="form-control" value="<?= e($settings['batch_size']) ?>" required>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cron & Worker Information -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header fw-bold small">
                <i class="bi bi-clock-history text-primary me-1"></i> Automated Cron Worker
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    For production or shared cPanel hosting, configure standard crontab to execute every minute. The worker claims jobs atomically via SQLite transactions.
                </p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Standard Crontab Entry (Every Minute):</label>
                    <pre class="bg-dark text-light p-2 rounded small" style="font-size: 11px;">* * * * * php <?= base_path('cron/email-worker.php') ?> --max-jobs=50 --max-time=55 >> /dev/null 2>&1</pre>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nightly Maintenance / Cleanup Cron:</label>
                    <pre class="bg-dark text-light p-2 rounded small" style="font-size: 11px;">0 2 * * * php <?= base_path('cron/cleanup.php') ?> >> /dev/null 2>&1</pre>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Daemon Mode (VPS / Supervisor):</label>
                    <pre class="bg-dark text-light p-2 rounded small" style="font-size: 11px;">php <?= base_path('cron/email-worker.php') ?> --daemon --sleep=3</pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-bold small">
                <i class="bi bi-shield-lock-fill text-primary me-1"></i> Security & Cryptography
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted">
                    <li><i class="bi bi-check-circle text-success me-1"></i> <strong>Encryption:</strong> AES-256-GCM for SMTP credentials at rest.</li>
                    <li><i class="bi bi-check-circle text-success me-1"></i> <strong>Session Security:</strong> HTTP-Only cookies with CSRF tokens.</li>
                    <li><i class="bi bi-check-circle text-success me-1"></i> <strong>Storage Protection:</strong> Private files blocked by Apache <code>.htaccess</code>.</li>
                    <li><i class="bi bi-check-circle text-success me-1"></i> <strong>Database Engine:</strong> SQLite 3 in WAL Mode with Foreign Keys enabled.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
