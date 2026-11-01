<div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
        <i class="bi bi-envelope-paper-heart-fill fs-2"></i>
    </div>
    <h3 class="fw-bold text-dark"><?= e(config('app.name', 'MailMoll')) ?></h3>
    <p class="text-muted small">Multi-SMTP Email Management Dashboard</p>
</div>

<?php if ($flashError = flash('error')): ?>
    <div class="alert alert-danger py-2 small mb-3">
        <i class="bi bi-exclamation-circle-fill me-1"></i> <?= e($flashError) ?>
    </div>
<?php endif; ?>

<?php if ($flashSuccess = flash('success')): ?>
    <div class="alert alert-success py-2 small mb-3">
        <i class="bi bi-check-circle-fill me-1"></i> <?= e($flashSuccess) ?>
    </div>
<?php endif; ?>

<form action="<?= url('/login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" value="<?= e(old('email', 'admin@mailmoll.local')) ?>" required autofocus placeholder="name@company.com">
        </div>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label small fw-semibold">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" value="Admin@12345" required placeholder="••••••••">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
    </button>
</form>

<div class="mt-4 pt-3 border-top text-center text-muted small">
    <div>Standard Credentials:</div>
    <code>admin@mailmoll.local</code> / <code>Admin@12345</code>
</div>
