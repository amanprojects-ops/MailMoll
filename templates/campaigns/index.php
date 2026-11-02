<?php
$totalCampaigns = count($campaigns);
$draftCount = 0;
$activeCount = 0;
$completedCount = 0;
$pausedCount = 0;
$totalRecipients = 0;
$totalDelivered = 0;

foreach ($campaigns as $c) {
    $st = strtolower($c['status'] ?? 'draft');
    if ($st === 'draft') {
        $draftCount++;
    } elseif (in_array($st, ['queued', 'processing'])) {
        $activeCount++;
    } elseif (in_array($st, ['sent', 'completed'])) {
        $completedCount++;
    } elseif ($st === 'paused') {
        $pausedCount++;
    }

    $totalRecipients += (int)($c['total_recipients'] ?? 0);
    $totalDelivered += (int)($c['sent_count'] ?? 0);
}
?>

<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Campaign Management</span>
            <span class="badge bg-primary bg-opacity-10 text-primary fs-6 fw-semibold rounded-pill px-3"><?= $totalCampaigns ?> Total</span>
        </h4>
        <p class="text-muted small mb-0">Create, schedule, track, and manage multi-provider email broadcasts.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/campaigns/new') ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 shadow-sm">
            <i class="bi bi-magic fs-6"></i>
            <span class="fw-semibold">New Campaign Wizard</span>
        </a>
    </div>
</div>

<!-- Quick Statistics Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Campaigns</div>
                    <h3 class="fw-bold mb-0 mt-1"><?= $totalCampaigns ?></h3>
                    <div class="text-muted" style="font-size: 11px;"><?= number_format($totalRecipients) ?> total recipients</div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-envelope-paper fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Draft Campaigns</div>
                    <h3 class="fw-bold mb-0 mt-1 text-secondary"><?= $draftCount ?></h3>
                    <div class="text-muted" style="font-size: 11px;">Ready for review & edit</div>
                </div>
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary rounded-3 p-3">
                    <i class="bi bi-pencil-square fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Active / In-Flight</div>
                    <h3 class="fw-bold mb-0 mt-1 text-info"><?= $activeCount ?></h3>
                    <div class="text-muted" style="font-size: 11px;">Queued or dispatching</div>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="bi bi-send fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Delivered / Sent</div>
                    <h3 class="fw-bold mb-0 mt-1 text-success"><?= $completedCount ?></h3>
                    <div class="text-muted" style="font-size: 11px;"><?= number_format($totalDelivered) ?> messages dispatched</div>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Campaigns Data Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <!-- Filter Tabs -->
        <ul class="nav nav-pills small gap-1" id="campaignFilterTabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active py-1 px-3 fw-semibold camp-filter-btn" data-status="all">
                    All <span class="badge bg-white text-dark ms-1"><?= $totalCampaigns ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link py-1 px-3 fw-semibold text-secondary camp-filter-btn" data-status="draft">
                    Drafts <span class="badge bg-light text-secondary border ms-1"><?= $draftCount ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link py-1 px-3 fw-semibold text-info camp-filter-btn" data-status="active">
                    Active & Queued <span class="badge bg-info bg-opacity-10 text-info ms-1"><?= $activeCount ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link py-1 px-3 fw-semibold text-success camp-filter-btn" data-status="sent">
                    Completed <span class="badge bg-success bg-opacity-10 text-success ms-1"><?= $completedCount ?></span>
                </button>
            </li>
            <?php if ($pausedCount > 0): ?>
                <li class="nav-item">
                    <button type="button" class="nav-link py-1 px-3 fw-semibold text-warning camp-filter-btn" data-status="paused">
                        Paused <span class="badge bg-warning bg-opacity-10 text-warning ms-1"><?= $pausedCount ?></span>
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Search Input -->
        <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="campSearchInput" class="form-control bg-light border-start-0" placeholder="Search campaigns...">
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="campaignsTable">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 220px;">Campaign Details</th>
                        <th style="min-width: 180px;">Subject Line</th>
                        <th>Status</th>
                        <th style="min-width: 160px;">Recipients & Progress</th>
                        <th>Timing</th>
                        <th class="text-end" style="min-width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-send-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <div class="fw-semibold">No campaigns found</div>
                                <div class="small text-muted mt-1">Click <strong>"New Campaign Wizard"</strong> to build your first email campaign.</div>
                                <a href="<?= url('/campaigns/new') ?>" class="btn btn-primary btn-sm mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Create First Campaign
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($campaigns as $camp): 
                            $status = strtolower($camp['status'] ?? 'draft');
                            $totalRec = (int)($camp['total_recipients'] ?? 0);
                            $sentRec = (int)($camp['sent_count'] ?? 0);
                            $pct = $totalRec > 0 ? round(($sentRec / $totalRec) * 100) : 0;
                        ?>
                            <tr class="camp-row" data-status="<?= e($status) ?>">
                                <td>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                        <?php if ($status === 'draft'): ?>
                                            <a href="<?= url('/campaigns/edit/' . $camp['id']) ?>" class="text-decoration-none text-dark fw-bold hover-primary" title="Click to edit draft">
                                                <?= e($camp['title']) ?>
                                            </a>
                                            <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">Draft</span>
                                        <?php else: ?>
                                            <span><?= e($camp['title']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size: 11px;">
                                        <i class="bi bi-person-fill text-secondary"></i>
                                        <span><?= e($camp['sender_name']) ?></span>
                                        <span class="text-secondary">&bull;</span>
                                        <span class="font-monospace">&lt;<?= e($camp['sender_email']) ?>&gt;</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark text-truncate" style="max-width: 240px;" title="<?= e($camp['subject']) ?>">
                                        <?= e($camp['subject']) ?>
                                    </div>
                                    <div class="text-muted font-monospace" style="font-size: 11px;">
                                        <?= e(strtoupper($camp['routing_type'] ?? 'GROUP')) ?> Routing
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-<?= e($status) ?>">
                                        <?php if ($status === 'draft'): ?>
                                            <i class="bi bi-pencil-fill" style="font-size: 9px;"></i>
                                        <?php elseif ($status === 'processing'): ?>
                                            <i class="bi bi-arrow-repeat spin" style="font-size: 9px;"></i>
                                        <?php elseif ($status === 'queued'): ?>
                                            <i class="bi bi-hourglass-split" style="font-size: 9px;"></i>
                                        <?php elseif ($status === 'sent' || $status === 'completed'): ?>
                                            <i class="bi bi-check-circle-fill" style="font-size: 9px;"></i>
                                        <?php elseif ($status === 'paused'): ?>
                                            <i class="bi bi-pause-fill" style="font-size: 9px;"></i>
                                        <?php endif; ?>
                                        <?= ucfirst(e($camp['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 11px;">
                                        <span><strong><?= number_format($sentRec) ?></strong> / <?= number_format($totalRec) ?></span>
                                        <span class="fw-semibold text-dark"><?= $pct ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 4px; background-color: #E2E8F0;">
                                        <div class="progress-bar <?= $pct == 100 ? 'bg-success' : 'bg-primary' ?>" style="width: <?= $pct ?>%; border-radius: 4px;"></div>
                                    </div>
                                    <?php if (!empty($camp['failed_count']) && $camp['failed_count'] > 0): ?>
                                        <div class="text-danger mt-1 fw-semibold" style="font-size: 10px;">
                                            <i class="bi bi-exclamation-circle me-1"></i> <?= $camp['failed_count'] ?> failed
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?php if (!empty($camp['scheduled_at'])): ?>
                                        <div class="text-primary fw-semibold" style="font-size: 11px;">
                                            <i class="bi bi-calendar-event me-1"></i> <?= date('M d, Y', strtotime($camp['scheduled_at'])) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($camp['scheduled_at'])) ?> UTC
                                        </div>
                                    <?php else: ?>
                                        <div style="font-size: 11px;">
                                            <?= date('M d, Y', strtotime($camp['created_at'])) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <?= date('H:i', strtotime($camp['created_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- Quick Email Preview Modal Button -->
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-preview-camp" 
                                            data-title="<?= e($camp['title']) ?>" 
                                            data-subject="<?= e($camp['subject']) ?>" 
                                            data-sender="<?= e($camp['sender_name'] . ' <' . $camp['sender_email'] . '>') ?>"
                                            data-body="<?= htmlspecialchars($camp['body_html']) ?>"
                                            title="Preview Rendered Email">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <!-- If Draft: Edit Button -->
                                        <?php if ($status === 'draft'): ?>
                                            <a href="<?= url('/campaigns/edit/' . $camp['id']) ?>" class="btn btn-outline-primary btn-sm py-1 px-2 fw-semibold" title="Edit Draft Campaign">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                            <form action="<?= url('/campaigns/launch/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Dispatch and launch to queue">
                                                    <i class="bi bi-play-fill me-1"></i> Launch
                                                </button>
                                            </form>
                                        <?php elseif ($status === 'paused'): ?>
                                            <form action="<?= url('/campaigns/resume/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-success btn-sm py-1 px-2" title="Resume Campaign">
                                                    <i class="bi bi-play-fill me-1"></i> Resume
                                                </button>
                                            </form>
                                        <?php elseif (in_array($status, ['queued', 'processing'])): ?>
                                            <form action="<?= url('/campaigns/pause/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-warning btn-sm py-1 px-2" title="Pause Campaign Dispatch">
                                                    <i class="bi bi-pause-fill me-1"></i> Pause
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Delete Campaign Button -->
                                        <form action="<?= url('/campaigns/delete/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                            <?= csrf_field() ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-confirm-delete" data-message="Delete campaign '<?= e($camp['title']) ?>'? Any unreleased jobs will be purged.">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Quick Campaign Email Preview -->
<div class="modal fade" id="campaignPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light py-2">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="previewCampModalTitle">Campaign Email Preview</h6>
                    <div class="text-muted small" style="font-size: 11px;" id="previewCampModalMeta"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 bg-light">
                <iframe id="campaignPreviewIframe" class="w-100 bg-white rounded border" style="height: 480px; border: 1px solid #E2E8F0;"></iframe>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i> Rendered in isolated sandbox</span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Quick Filter Buttons
    const filterBtns = document.querySelectorAll('.camp-filter-btn');
    const rows = document.querySelectorAll('.camp-row');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('active', 'btn-primary');
            });
            this.classList.add('active');

            const status = this.getAttribute('data-status');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'all') {
                    row.style.display = '';
                } else if (status === 'active') {
                    row.style.display = (rowStatus === 'queued' || rowStatus === 'processing') ? '' : 'none';
                } else if (status === 'sent') {
                    row.style.display = (rowStatus === 'sent' || rowStatus === 'completed') ? '' : 'none';
                } else {
                    row.style.display = (rowStatus === status) ? '' : 'none';
                }
            });
        });
    });

    // 2. Search Filter
    const searchInput = document.getElementById('campSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // 3. Quick Email Preview Modal
    const previewModalEl = document.getElementById('campaignPreviewModal');
    const previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;
    const previewIframe = document.getElementById('campaignPreviewIframe');
    const previewTitle = document.getElementById('previewCampModalTitle');
    const previewMeta = document.getElementById('previewCampModalMeta');

    document.querySelectorAll('.btn-preview-camp').forEach(btn => {
        btn.addEventListener('click', function() {
            const title = this.getAttribute('data-title') || 'Email Preview';
            const subject = this.getAttribute('data-subject') || '';
            const sender = this.getAttribute('data-sender') || '';
            let rawBody = this.getAttribute('data-body') || '';

            // Replace personalized tags for preview
            let previewHtml = rawBody
                .replace(/\{\{first_name\}\}/g, 'Sarah')
                .replace(/\{\{last_name\}\}/g, 'Connor')
                .replace(/\{\{email\}\}/g, 'sarah.connor@example.com')
                .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsubscribe');

            if (!previewHtml.toLowerCase().includes('<html')) {
                previewHtml = `<!DOCTYPE html><html><head><meta charset="utf-8"><base target="_blank"><style>body{margin:0;padding:20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background-color:#f8fafc;color:#1e293b;}</style></head><body>${previewHtml}</body></html>`;
            } else if (previewHtml.includes('<head>')) {
                previewHtml = previewHtml.replace('<head>', '<head><base target="_blank">');
            }

            if (previewTitle) previewTitle.textContent = title;
            if (previewMeta) previewMeta.textContent = `Subject: "${subject}" | From: ${sender}`;
            if (previewIframe) previewIframe.srcdoc = previewHtml;

            if (previewModal) previewModal.show();
        });
    });
});
</script>
