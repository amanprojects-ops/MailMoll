<div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-4 bg-primary bg-opacity-10 text-primary mb-3">
        <i class="bi bi-key fs-2"></i>
    </div>
    <h3 class="fw-bold text-light mb-1">Reset Password</h3>
    <p class="text-secondary small">Enter your email and we'll send you a password reset link</p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/forgot-password') ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="mb-4">
        <label for="email" class="form-label small fw-semibold text-light">Account Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-dark border-secondary-subtle text-light" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus placeholder="name@company.com">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-send me-1"></i> Send Password Reset Link
    </button>
</form>

<div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
    Remember your credentials? 
    <a href="<?= \App\Helpers\UrlHelper::to('/login') ?>" class="text-primary fw-semibold text-decoration-none">Return to sign in</a>
</div>
