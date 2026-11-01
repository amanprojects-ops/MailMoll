<div class="text-center">
    <?php if ($success): ?>
        <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-3">
            <i class="bi bi-check2-circle fs-1"></i>
        </div>
        <h4 class="fw-bold text-dark">Unsubscribed Successfully</h4>
        <p class="text-muted small my-3"><?= e($message) ?></p>
        <?php if (!empty($email)): ?>
            <div class="p-2 bg-light rounded text-secondary small mb-3">
                <strong>Recipient:</strong> <?= e($email) ?>
            </div>
        <?php endif; ?>
        <p class="text-muted small">You will no longer receive marketing communications from our platform.</p>
    <?php else: ?>
        <div class="d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger mb-3">
            <i class="bi bi-x-circle fs-1"></i>
        </div>
        <h4 class="fw-bold text-dark">Unsubscribe Notice</h4>
        <p class="text-muted small my-3"><?= e($message) ?></p>
    <?php endif; ?>
</div>
