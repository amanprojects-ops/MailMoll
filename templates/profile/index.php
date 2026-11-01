<div class="mb-4">
    <h4 class="fw-bold mb-1">User Profile</h4>
    <p class="text-muted small mb-0">Update your account credentials and personal information.</p>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header fw-bold small">
                <i class="bi bi-person-fill text-primary me-1"></i> Account Details
            </div>
            <div class="card-body">
                <form action="<?= url('/profile/update') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Display Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">New Password (leave empty to keep current)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
