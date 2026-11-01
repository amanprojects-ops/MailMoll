<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Suppression & Bounce Management</h4>
        <p class="text-muted small mb-0">Automatic suppression list preventing dispatch to unsubscribed users and hard bounces.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newSuppressionModal">
        <i class="bi bi-shield-plus me-1"></i> Add Suppressed Email
    </button>
</div>

<div class="alert alert-secondary py-2 small mb-4">
    <i class="bi bi-info-circle me-1"></i> Addresses on this list are blocked globally across all campaigns and transactional dispatches to protect your domain reputation.
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th>Suppressed Email</th>
                        <th>Reason Category</th>
                        <th>Source Campaign</th>
                        <th>Added On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppressions)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                                Suppression list is currently empty.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppressions as $s): ?>
                            <tr>
                                <td class="small fw-semibold"><?= e($s['email']) ?></td>
                                <td>
                                    <?php if ($s['reason'] === 'hard_bounce'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-octagon me-1"></i> Hard Bounce</span>
                                    <?php elseif ($s['reason'] === 'unsubscribe'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-dark"><i class="bi bi-person-x me-1"></i> Unsubscribed</span>
                                    <?php elseif ($s['reason'] === 'complaint'): ?>
                                        <span class="badge bg-danger text-white"><i class="bi bi-flag me-1"></i> Spam Complaint</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= e($s['reason']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= $s['source_campaign_id'] ? 'Campaign #' . $s['source_campaign_id'] : 'Manual Entry' ?>
                                </td>
                                <td class="small text-muted">
                                    <?= date('M d, Y H:i', strtotime($s['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <form action="<?= url('/suppressions/delete/' . $s['id']) ?>" method="POST" class="d-inline m-0">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-confirm-delete" data-message="Remove <?= e($s['email']) ?> from suppression list?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Suppression -->
<div class="modal fade" id="newSuppressionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/suppressions/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Manually Suppress Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="user@domain.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Suppression Reason *</label>
                        <select name="reason" class="form-select">
                            <option value="manual">Manual Suppression</option>
                            <option value="unsubscribe">User Unsubscribe</option>
                            <option value="hard_bounce">Hard Bounce (Invalid Address)</option>
                            <option value="complaint">Spam Complaint</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add to Suppression List</button>
                </div>
            </form>
        </div>
    </div>
</div>
