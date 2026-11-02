<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-light"><i class="bi bi-credit-card-2-front-fill text-warning me-2"></i>Subscriptions & Licensing</h4>
        <p class="text-secondary small mb-0">Overview of tenant license tiers, feature grants, and self-hosted activation keys.</p>
    </div>
</div>

<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr class="text-secondary small text-uppercase border-bottom border-secondary-subtle">
                    <th class="ps-4 py-3">Account</th>
                    <th>Plan Tier</th>
                    <th>Status</th>
                    <th>Activated At</th>
                    <th class="pe-4 text-end">License Scope</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-secondary">
                            No subscription records found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscriptions as $s): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-light"><?= e($s['user_name']) ?></div>
                                <div class="text-secondary small"><?= e($s['user_email']) ?></div>
                            </td>
                            <td>
                                <?php if ($s['plan'] === 'enterprise'): ?>
                                    <span class="badge bg-warning text-dark fw-bold">ENTERPRISE FLEET</span>
                                <?php elseif ($s['plan'] === 'pro'): ?>
                                    <span class="badge bg-primary fw-bold">PRO LICENSE</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-light">COMMUNITY (FREE)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success text-uppercase">
                                    <i class="bi bi-check-circle me-1"></i><?= e($s['status']) ?>
                                </span>
                            </td>
                            <td class="text-secondary small">
                                <?= e(date('M j, Y', strtotime($s['starts_at']))) ?>
                            </td>
                            <td class="pe-4 text-end">
                                <span class="text-secondary small">Unlimited Relays & Campaigns</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
