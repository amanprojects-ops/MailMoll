<div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-4 bg-success bg-opacity-10 text-success mb-3">
        <i class="bi bi-shield-lock fs-2"></i>
    </div>
    <h3 class="fw-bold text-light mb-1">Set New Password</h3>
    <p class="text-secondary small">Enter a new secure password for your account</p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/reset-password') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? ($_GET['token'] ?? '')) ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? ($_GET['email'] ?? '')) ?>">

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold text-light">New Password</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-dark border-secondary-subtle text-light" id="password" name="password" minlength="8" required autofocus placeholder="At least 8 characters">
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label small fw-semibold text-light">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-shield-check"></i></span>
            <input type="password" class="form-control bg-dark border-secondary-subtle text-light" id="password_confirmation" name="password_confirmation" minlength="8" required placeholder="Re-enter new password">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-check-lg me-1"></i> Update Password & Sign In
    </button>
</form>

<div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
    <a href="<?= \App\Helpers\UrlHelper::to('/login') ?>" class="text-primary fw-semibold text-decoration-none">Cancel and return to login</a>
</div>
