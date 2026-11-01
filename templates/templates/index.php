<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Email Templates</h4>
        <p class="text-muted small mb-0">Design, customize, and manage reusable responsive HTML email templates.</p>
    </div>
    <a href="<?= url('/templates/new') ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-circle-fill"></i> New Template
    </a>
</div>

<div class="row g-4">
    <?php if (empty($templates)): ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="bi bi-file-earmark-code fs-1 d-block mb-2"></i>
            No templates created yet. Click <strong>"New Template"</strong> to start designing.
        </div>
    <?php else: ?>
        <?php foreach ($templates as $tpl): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold small"><?= e($tpl['name']) ?></span>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= url('/templates/preview/' . $tpl['id']) ?>" target="_blank" class="btn btn-outline-secondary" title="Preview in new tab">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= url('/templates/edit/' . $tpl['id']) ?>" class="btn btn-outline-secondary" title="Edit Template">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= url('/templates/delete/' . $tpl['id']) ?>" method="POST" class="d-inline m-0">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-outline-danger btn-confirm-delete" data-message="Delete template '<?= e($tpl['name']) ?>'?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            <strong>Default Subject:</strong> <?= e($tpl['subject_default'] ?: 'None specified') ?>
                        </div>
                        <div class="border rounded p-2 bg-light mb-3" style="height: 120px; overflow: hidden; font-size: 11px; opacity: 0.85;">
                            <?= strip_tags($tpl['body_html']) ?>
                        </div>
                        <?php if (!empty($tpl['tags'])): ?>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach (explode(',', $tpl['tags']) as $tag): ?>
                                    <span class="badge bg-light text-secondary border small"><?= e(trim($tag)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-transparent border-top text-muted" style="font-size: 11px;">
                        Updated: <?= date('M d, Y H:i', strtotime($tpl['updated_at'])) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
