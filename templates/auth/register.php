<div class="text-center mb-4">
    <h3 class="fw-bold text-light mb-1">Create your account</h3>
    <p class="text-secondary small">Deploy your self-hosted MailFlow workspace</p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/register') ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="mb-3">
        <label for="name" class="form-label small fw-semibold text-light">Full Name</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control bg-dark border-secondary-subtle text-light" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required autofocus placeholder="Alex Miller">
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-light">Work Email</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-dark border-secondary-subtle text-light" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required placeholder="alex@company.com">
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold text-light">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-dark border-secondary-subtle text-light" id="password" name="password" minlength="8" required placeholder="At least 8 characters">
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label small fw-semibold text-light">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-shield-check"></i></span>
            <input type="password" class="form-control bg-dark border-secondary-subtle text-light" id="password_confirmation" name="password_confirmation" minlength="8" required placeholder="Re-enter password">
        </div>
    </div>

    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input bg-dark border-secondary-subtle" id="terms" name="terms" required checked>
        <label class="form-check-label text-secondary small" for="terms">
            I agree to the <a href="<?= \App\Helpers\UrlHelper::to('/terms') ?>" target="_blank" class="text-primary text-decoration-none">Terms of Service</a> & <a href="<?= \App\Helpers\UrlHelper::to('/privacy-policy') ?>" target="_blank" class="text-primary text-decoration-none">Privacy Policy</a>
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-arrow-right-circle me-1"></i> Create MailFlow Account
    </button>
</form>

<div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
    Already have an account? 
    <a href="<?= \App\Helpers\UrlHelper::to('/login') ?>" class="text-primary fw-semibold text-decoration-none">Sign in here</a>
</div>
