<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Contacts & Audiences</h4>
        <p class="text-muted small mb-0">Manage permission-based subscriber lists and bulk CSV contact imports.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newListModal">
            <i class="bi bi-folder-plus me-1"></i> New List
        </button>
        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importCsvModal">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import CSV
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newContactModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Contact
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Contact Lists Column -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header fw-bold small text-muted text-uppercase">
                Subscriber Lists (<?= count($lists) ?>)
            </div>
            <div class="list-group list-group-flush small">
                <a href="<?= url('/contacts') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= !$selectedList ? 'active' : '' ?>">
                    <span><i class="bi bi-people me-2"></i> All Contacts</span>
                    <span class="badge bg-secondary rounded-pill"><?= count($contacts) ?></span>
                </a>
                <?php foreach ($lists as $l): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center <?= $selectedList == $l['id'] ? 'active' : '' ?>">
                        <a href="<?= url('/contacts?list_id=' . $l['id']) ?>" class="text-decoration-none <?= $selectedList == $l['id'] ? 'text-white' : 'text-dark' ?> flex-grow-1 text-truncate">
                            <i class="bi bi-folder me-2"></i> <?= e($l['name']) ?>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge <?= $selectedList == $l['id'] ? 'bg-light text-dark' : 'bg-primary bg-opacity-10 text-primary' ?> rounded-pill">
                                <?= $l['total_contacts'] ?>
                            </span>
                            <form action="<?= url('/contacts/lists/delete/' . $l['id']) ?>" method="POST" class="d-inline m-0">
                                <?= csrf_field() ?>
                                <button type="button" class="btn btn-link btn-sm p-0 <?= $selectedList == $l['id'] ? 'text-white' : 'text-danger' ?> btn-confirm-delete" data-message="Delete list '<?= e($l['name']) ?>'?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Contacts Table Column -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable">
                        <thead>
                            <tr>
                                <th>Subscriber</th>
                                <th>Status</th>
                                <th>Opt-in Date</th>
                                <th>Opt-in IP</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        No contacts found in this list. Use <strong>"Add Contact"</strong> or <strong>"Import CSV"</strong>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contacts as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold small"><?= e($c['first_name'] ? "{$c['first_name']} {$c['last_name']}" : 'Subscriber') ?></div>
                                            <div class="text-muted" style="font-size: 11px;"><?= e($c['email']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-<?= $c['status'] ?>"><?= e($c['status']) ?></span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= date('M d, Y H:i', strtotime($c['opt_in_at'])) ?>
                                        </td>
                                        <td class="small text-muted font-monospace" style="font-size: 11px;">
                                            <?= e($c['opt_in_ip'] ?: 'Import') ?>
                                        </td>
                                        <td class="text-end">
                                            <form action="<?= url('/contacts/delete/' . $c['id']) ?>" method="POST" class="d-inline m-0">
                                                <?= csrf_field() ?>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-confirm-delete" data-message="Delete contact '<?= e($c['email']) ?>'?">
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
    </div>
</div>

<!-- Modal: New Contact -->
<div class="modal fade" id="newContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/contacts/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Subscriber Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="user@domain.com" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control" placeholder="John">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Doe">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Assign to List</label>
                        <select name="list_id" class="form-select">
                            <option value="">-- No Specific List --</option>
                            <?php foreach ($lists as $l): ?>
                                <option value="<?= $l['id'] ?>" <?= $selectedList == $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: New List -->
<div class="modal fade" id="newListModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/contacts/lists/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Contact List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">List Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. VIP Customers, Newsletter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="List description or source details"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create List</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Import CSV -->
<div class="modal fade" id="importCsvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/contacts/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Import Contacts via CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Destination List *</label>
                        <select name="list_id" class="form-select" required>
                            <?php foreach ($lists as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">CSV File *</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <div class="form-text small">
                            CSV header should include: <code>email, first_name, last_name</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
