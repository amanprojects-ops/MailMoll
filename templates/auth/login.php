<div class="text-center mb-4">
    <h3 class="fw-bold text-light mb-1">Welcome back</h3>
    <p class="text-secondary small">Sign in to your MailFlow control panel</p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/login') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold text-light">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control bg-dark border-secondary-subtle text-light" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? 'admin@mailmoll.local') ?>" required autofocus placeholder="name@company.com">
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label small fw-semibold text-light mb-0">Password</label>
            <a href="<?= \App\Helpers\UrlHelper::to('/forgot-password') ?>" class="text-primary small text-decoration-none">Forgot password?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-dark border-secondary-subtle text-secondary"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-dark border-secondary-subtle text-light" id="password" name="password" value="Admin@12345" required placeholder="••••••••">
        </div>
    </div>

    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input bg-dark border-secondary-subtle" id="remember" name="remember" checked>
        <label class="form-check-label text-secondary small" for="remember">Keep me signed in for 30 days</label>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to MailFlow
    </button>
</form>

<div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
    Don't have an account yet? 
    <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="text-primary fw-semibold text-decoration-none">Create an account</a>
</div>
