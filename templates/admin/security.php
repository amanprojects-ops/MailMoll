<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-shield-check text-warning me-2"></i>Security Posture & 2FA Enforcement</h4>
        <p class="text-secondary small mb-0">Monitor cryptographic safeguards, session integrity, TOTP authenticator activations, and password token resets.</p>
    </div>
</div>

<!-- Security Architecture Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="admin-card p-3">
            <div class="text-success small fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>Credential Encryption</div>
            <h6 class="text-light fw-bold mb-0">AES-256-GCM</h6>
            <small class="text-secondary">Authenticated Ciphers</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3">
            <div class="text-success small fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>Password Hashes</div>
            <h6 class="text-light fw-bold mb-0">BCRYPT Cost 12</h6>
            <small class="text-secondary">Salted Hashes</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3">
            <div class="text-success small fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>Session Defense</div>
            <h6 class="text-light fw-bold mb-0">Regeneration On Login</h6>
            <small class="text-secondary">Fixation Immune</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card p-3">
            <div class="text-success small fw-semibold mb-1"><i class="bi bi-check-circle me-1"></i>CSRF Protection</div>
            <h6 class="text-light fw-bold mb-0">Strict Token Match</h6>
            <small class="text-secondary">All Mutating Routes</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Two-Factor Authenticator Enrollments -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold text-light mb-3"><i class="bi bi-phone text-primary me-2"></i>TOTP 2FA Enrollments</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 small">
                    <thead>
                        <tr class="text-secondary border-bottom border-secondary-subtle">
                            <th>User</th>
                            <th>Status</th>
                            <th class="text-end">Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($twoFaUsers)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary">
                                    No accounts currently enrolled in TOTP 2FA.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($twoFaUsers as $tf): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-light"><?= e($tf['name']) ?></div>
                                        <div class="text-secondary small"><?= e($tf['email']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $tf['is_enabled'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?>">
                                            <?= $tf['is_enabled'] ? 'Enforced' : 'Pending' ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-secondary">
                                        <?= e(date('M j, Y', strtotime($tf['created_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Password Reset Requests -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h5 class="fw-bold text-light mb-3"><i class="bi bi-key text-warning me-2"></i>Recent Password Reset Tokens</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0 small">
                    <thead>
                        <tr class="text-secondary border-bottom border-secondary-subtle">
                            <th>Requested For</th>
                            <th>Token Prefix</th>
                            <th class="text-end">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($passwordResets)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-secondary">
                                    No active password reset tokens found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($passwordResets as $pr): ?>
                                <tr>
                                    <td class="text-light"><?= e($pr['email']) ?></td>
                                    <td><code class="text-muted"><?= substr($pr['token'], 0, 10) ?>...</code></td>
                                    <td class="text-end text-secondary">
                                        <?= e(date('H:i:s (M j)', strtotime($pr['expires_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
