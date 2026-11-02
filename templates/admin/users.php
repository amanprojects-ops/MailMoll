<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-people-fill text-warning me-2"></i>User & Role Management</h4>
        <p class="text-secondary small mb-0">Control team member permissions, roles (Admin, Operator, Viewer), and account status.</p>
    </div>
    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"><?= count($users) ?> Accounts Registered</span>
</div>

<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-secondary small text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">User</th>
                    <th>Role</th>
                    <th>Plan Tier</th>
                    <th>Status</th>
                    <th>Last Active</th>
                    <th class="pe-4 text-end">Update Privileges</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar bg-primary bg-opacity-25 text-primary rounded-circle d-inline-flex justify-content-center align-items-center fw-bold" style="width: 38px; height: 38px;">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </span>
                                <div>
                                    <div class="fw-bold text-light"><?= e($u['name']) ?></div>
                                    <div class="text-secondary small"><?= e($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-warning text-dark fw-bold"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>
                            <?php elseif ($u['role'] === 'operator'): ?>
                                <span class="badge bg-primary-subtle text-primary fw-bold"><i class="bi bi-gear-fill me-1"></i>Operator</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary fw-bold"><i class="bi bi-eye-fill me-1"></i>Viewer</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success text-uppercase">
                                <?= e($u['subscription_plan'] ?? 'community') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $u['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                <?= e($u['status']) ?>
                            </span>
                        </td>
                        <td class="text-secondary small">
                            <?= $u['last_login_at'] ? e(date('M j, Y H:i', strtotime($u['last_login_at']))) : '<span class="text-muted">Never</span>' ?>
                        </td>
                        <td class="pe-4 text-end">
                            <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $u['id'] ?>">
                                <i class="bi bi-pencil-square me-1"></i> Edit Role
                            </button>

                            <!-- Edit User Modal -->
                            <div class="modal fade text-start" id="editUserModal<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark border-secondary-subtle">
                                        <div class="modal-header border-secondary-subtle">
                                            <h5 class="modal-title text-light fw-bold">Manage Privileges: <?= e($u['name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?= url('/admin/users/update/' . $u['id']) ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label text-light small fw-semibold">User Role</label>
                                                    <select class="form-select bg-body-tertiary border-secondary-subtle text-light" name="role">
                                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin (Full Platform & Server Access)</option>
                                                        <option value="operator" <?= $u['role'] === 'operator' ? 'selected' : '' ?>>Operator (Campaigns, Templates, Contacts)</option>
                                                        <option value="viewer" <?= $u['role'] === 'viewer' ? 'selected' : '' ?>>Viewer (Read-Only Analytics)</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-light small fw-semibold">Account Status</label>
                                                    <select class="form-select bg-body-tertiary border-secondary-subtle text-light" name="status">
                                                        <option value="active" <?= $u['status'] === 'active' ? 'selected' : '' ?>>Active (Can Sign In)</option>
                                                        <option value="inactive" <?= $u['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Suspended)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary-subtle">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning fw-semibold text-dark">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
