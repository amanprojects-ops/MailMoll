
<div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-4 bg-primary bg-opacity-10 text-primary mb-3">
        <i class="bi bi-shield-check fs-2"></i>
    </div>
    <h3 class="fw-bold text-light mb-1">Two-Factor Verification</h3>
    <p class="text-secondary small">Enter the 6-digit authentication code from your authenticator app</p>
</div>

<form action="<?= \App\Helpers\UrlHelper::to('/two-factor') ?>" method="POST">
    <?= csrf_field() ?>

    <div class="mb-4">
        <label for="code" class="form-label small fw-semibold text-light text-center d-block">Authentication Code</label>
        <input type="text" 
               class="form-control form-control-lg bg-dark border-secondary-subtle text-light text-center font-monospace fs-3 letter-spacing-2" 
               id="code" 
               name="code" 
               inputmode="numeric" 
               pattern="[0-9]*" 
               maxlength="6" 
               required 
               autofocus 
               placeholder="000 000" 
               style="letter-spacing: 0.3em;">
        <div class="form-text text-secondary text-center small mt-2">
            Open Google Authenticator, 1Password, or Authy to view your active code.
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-1"></i> Verify & Proceed
    </button>
</form>

<div class="mt-4 pt-3 border-top border-secondary-subtle text-center text-secondary small">
    Having trouble? <a href="<?= \App\Helpers\UrlHelper::to('/login') ?>" class="text-primary fw-semibold text-decoration-none">Sign in with backup credentials</a>
</div>
