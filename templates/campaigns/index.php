<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Campaign Management</h4>
        <p class="text-muted small mb-0">Create, schedule, track, and manage multi-provider email broadcasts.</p>
    </div>
    <a href="<?= url('/campaigns/new') ?>" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-magic"></i> New Campaign Wizard
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 datatable">
                <thead>
                    <tr>
                        <th>Campaign Title</th>
                        <th>Subject Line</th>
                        <th>Status</th>
                        <th>Recipients & Progress</th>
                        <th>Scheduled / Started</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-send-x fs-1 d-block mb-2"></i>
                                No campaigns found. Click <strong>"New Campaign Wizard"</strong> to build your first email campaign.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($campaigns as $camp): 
                            $pct = $camp['total_recipients'] > 0 ? round(($camp['sent_count'] / $camp['total_recipients']) * 100) : 0;
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-bold small"><?= e($camp['title']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;">
                                        Sender: <?= e($camp['sender_name']) ?> &lt;<?= e($camp['sender_email']) ?>&gt;
                                    </div>
                                </td>
                                <td class="small">
                                    <?= e($camp['subject']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-<?= $camp['status'] ?>"><?= e($camp['status']) ?></span>
                                </td>
                                <td style="min-width: 140px;">
                                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 11px;">
                                        <span>Sent: <strong><?= $camp['sent_count'] ?></strong> / <?= $camp['total_recipients'] ?></span>
                                        <span><?= $pct ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?= $pct ?>%;"></div>
                                    </div>
                                    <?php if ($camp['failed_count'] > 0): ?>
                                        <div class="text-danger" style="font-size: 10px;">Failed: <?= $camp['failed_count'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?php if ($camp['scheduled_at']): ?>
                                        <i class="bi bi-clock me-1"></i> <?= date('M d, H:i', strtotime($camp['scheduled_at'])) ?>
                                    <?php else: ?>
                                        <?= date('M d, H:i', strtotime($camp['created_at'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <?php if (in_array($camp['status'], ['draft', 'paused'])): ?>
                                            <form action="<?= url('/campaigns/launch/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-success" title="Dispatch to Queue">
                                                    <i class="bi bi-play-fill"></i> Launch
                                                </button>
                                            </form>
                                        <?php elseif ($camp['status'] === 'queued' || $camp['status'] === 'processing'): ?>
                                            <form action="<?= url('/campaigns/pause/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-warning" title="Pause Dispatch">
                                                    <i class="bi bi-pause-fill"></i> Pause
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form action="<?= url('/campaigns/delete/' . $camp['id']) ?>" method="POST" class="d-inline m-0">
                                            <?= csrf_field() ?>
                                            <button type="button" class="btn btn-outline-danger btn-confirm-delete" data-message="Delete campaign '<?= e($camp['title']) ?>' and purge pending queue jobs?">
                                                <i class="bi bi-trash-fill"></i>
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
