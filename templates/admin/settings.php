<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-sliders text-warning me-2"></i>Global Platform Settings</h4>
        <p class="text-secondary small mb-0">Configure master concurrency limits, queue batch parameters, and branding variables.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="admin-card p-4">
            <form action="<?= url('/admin/settings/update') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label for="app_name" class="form-label text-light small fw-semibold">Application Brand Name</label>
                    <input type="text" class="form-control bg-dark border-secondary-subtle text-light" id="app_name" name="app_name" value="<?= e($settings['app_name'] ?? 'MailFlow') ?>" required>
                    <div class="form-text text-secondary small">Appears across marketing layouts, header bars, and default email headers.</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="queue_batch_size" class="form-label text-light small fw-semibold">Worker Batch Size</label>
                        <input type="number" class="form-control bg-dark border-secondary-subtle text-light" id="queue_batch_size" name="queue_batch_size" value="<?= e($settings['queue_batch_size'] ?? '100') ?>" min="10" max="1000" required>
                        <div class="form-text text-secondary small">Emails locked and claimed per CLI worker execution pass.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="worker_sleep_seconds" class="form-label text-light small fw-semibold">Daemon Sleep Interval (Seconds)</label>
                        <input type="number" class="form-control bg-dark border-secondary-subtle text-light" id="worker_sleep_seconds" name="worker_sleep_seconds" value="<?= e($settings['worker_sleep_seconds'] ?? '5') ?>" min="1" max="60" required>
                        <div class="form-text text-secondary small">Seconds worker sleeps when queue is idle.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="debug_mode" class="form-label text-light small fw-semibold">Environment Debug Diagnostics</label>
                    <select class="form-select bg-dark border-secondary-subtle text-light" id="debug_mode" name="debug_mode">
                        <option value="0" <?= ($settings['debug_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Production (Errors logged silently)</option>
                        <option value="1" <?= ($settings['debug_mode'] ?? '0') === '1' ? 'selected' : '' ?>>Development (Detailed error stacktraces)</option>
                    </select>
                </div>

                <div class="pt-3 border-top border-secondary-subtle d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning fw-semibold text-dark px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Global Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
