
<div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-4 bg-info bg-opacity-10 text-info mb-3">
        <i class="bi bi-envelope-check fs-2"></i>
    </div>
    <h3 class="fw-bold text-light mb-1">Verify Your Email</h3>
    <p class="text-secondary small">We've sent an activation link to your email address</p>
</div>

<div class="p-3 rounded-3 bg-dark border border-secondary-subtle text-secondary small mb-4">
    <p class="mb-2">
        Please check your inbox and click the verification link to activate your MailFlow account.
    </p>
    <p class="mb-0 text-muted">
        If you did not receive the email within a couple of minutes, check your spam folder or request another link below.
    </p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/verify-email/resend') ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['verification_email'] ?? '') ?>">

    <button type="submit" class="btn btn-outline-light w-100 py-2 fw-semibold mb-3">
        <i class="bi bi-arrow-clockwise me-1"></i> Resend Verification Email
    </button>
</form>

<div class="pt-3 border-top border-secondary-subtle text-center text-secondary small">
    <a href="<?= \App\Helpers\UrlHelper::to('/login') ?>" class="text-primary fw-semibold text-decoration-none">Back to Login</a>
</div>
