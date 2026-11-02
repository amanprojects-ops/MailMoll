<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Contacts & Audiences</h4>
        <p class="text-muted small mb-0">Manage permission-based subscriber lists and multi-format bulk contact imports (.csv, .xlsx, .json, .txt).</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newListModal">
            <i class="bi bi-folder-plus me-1"></i> New List
        </button>
        <div class="btn-group">
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Contacts
            </button>
            <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Download Sample Templates">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm small">
                <li><h6 class="dropdown-header small text-uppercase fw-bold text-muted"><i class="bi bi-download me-1"></i> Sample Templates</h6></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/contacts/sample/csv') ?>"><i class="bi bi-filetype-csv text-success fs-6"></i> CSV Template (.csv)</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/contacts/sample/xlsx') ?>"><i class="bi bi-filetype-xlsx text-primary fs-6"></i> Excel Template (.xlsx)</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/contacts/sample/json') ?>"><i class="bi bi-filetype-json text-warning fs-6"></i> JSON Template (.json)</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="<?= url('/contacts/sample/txt') ?>"><i class="bi bi-filetype-txt text-secondary fs-6"></i> Plain Text List (.txt)</a></li>
            </ul>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newContactModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Contact
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Contact Lists Column -->
    <div class="col-lg-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold small text-muted text-uppercase d-flex justify-content-between align-items-center">
                <span>Subscriber Lists (<?= count($lists) ?>)</span>
                <button class="btn btn-sm btn-link p-0 text-primary" data-bs-toggle="modal" data-bs-target="#newListModal" title="New List">
                    <i class="bi bi-plus-circle-fill"></i>
                </button>
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
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Subscriber</th>
                                <th>Status</th>
                                <th>Opt-in Date</th>
                                <th>Opt-in IP / Source</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                        No contacts found in this list. Use <strong>"Add Contact"</strong> or <strong>"Import Contacts"</strong>.
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
        <div class="modal-content shadow">
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
                            <option value="">-- No Specific List (General Pool) --</option>
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
        <div class="modal-content shadow">
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

<!-- Modal: Multi-Format Contact Importer with Dedicated Email & Name Filter -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <form id="contactImportForm" action="<?= url('/contacts/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-file-earmark-arrow-up text-success me-1"></i> Multi-Format Contact Importer
                        </h5>
                        <div class="text-muted small">Import and filter contacts from CSV, Excel (.xlsx, .xls), JSON, or Plain Text.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- File Selection Area -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold small d-flex justify-content-between align-items-center">
                            <span>Select File *</span>
                            <span class="badge bg-light text-secondary border">
                                .csv, .xlsx, .xls, .json, .txt
                            </span>
                        </label>
                        <div class="input-group">
                            <input type="file" name="import_file" id="importFileInput" class="form-control" accept=".csv, .xlsx, .xls, .json, .txt, .tsv" required>
                            <button type="button" id="btnInspectFile" class="btn btn-outline-primary" style="display:none;">
                                <i class="bi bi-search me-1"></i> Inspect
                            </button>
                        </div>
                        <div class="form-text small d-flex align-items-center gap-2 mt-1">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span>Supports Excel workbooks, standard CSVs (comma/semicolon/tab), JSON arrays, and raw email lists.</span>
                        </div>
                        <!-- Download Sample Templates Section -->
                        <div class="p-2 px-3 bg-white border rounded d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2 shadow-xs">
                            <div class="small fw-semibold text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-download text-primary"></i> Download Sample Templates:
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('/contacts/sample/csv') ?>" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size:11px;" title="Download Sample CSV">
                                    <i class="bi bi-filetype-csv text-success me-1"></i> CSV
                                </a>
                                <a href="<?= url('/contacts/sample/xlsx') ?>" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size:11px;" title="Download Sample Excel (.xlsx)">
                                    <i class="bi bi-filetype-xlsx text-primary me-1"></i> Excel (.xlsx)
                                </a>
                                <a href="<?= url('/contacts/sample/json') ?>" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size:11px;" title="Download Sample JSON">
                                    <i class="bi bi-filetype-json text-warning me-1"></i> JSON
                                </a>
                                <a href="<?= url('/contacts/sample/txt') ?>" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size:11px;" title="Download Sample Text List">
                                    <i class="bi bi-filetype-txt text-secondary me-1"></i> TXT
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Destination List Options -->
                    <div class="card mb-4 bg-light border-0">
                        <div class="card-body py-3">
                            <label class="form-label fw-bold small text-dark mb-2">
                                <i class="bi bi-folder-check me-1 text-primary"></i> Destination Audience List
                            </label>
                            
                            <div class="row g-2 align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Select Existing List</label>
                                    <select name="list_id" id="destListSelect" class="form-select form-select-sm">
                                        <option value="">-- No Specific List (General Contacts) --</option>
                                        <?php foreach ($lists as $l): ?>
                                            <option value="<?= $l['id'] ?>" <?= $selectedList == $l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?> (<?= $l['total_contacts'] ?> contacts)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-1 text-center text-muted small fw-bold">OR</div>
                                <div class="col-md-5">
                                    <label class="form-label small text-muted mb-1">Create New List On-The-Fly</label>
                                    <input type="text" name="new_list_name" id="newListNameInput" class="form-control form-control-sm" placeholder="e.g. Imported Leads <?= date('M Y') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Inspection Loading Spinner -->
                    <div id="importPreviewLoading" class="text-center py-4 text-primary" style="display:none;">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <span class="small fw-semibold">Analyzing file structure and auto-detecting email & name columns...</span>
                    </div>

                    <!-- Dedicated Email & Name Column Filter / Mapping Section -->
                    <div id="columnMappingSection" class="card mb-4 border" style="display:none;">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-dark">
                                <i class="bi bi-funnel-fill text-primary me-1"></i> Dedicated Field Mapping & Filtering
                            </span>
                            <span id="fileBadgeFormat" class="badge bg-primary bg-opacity-10 text-primary font-monospace">CSV</span>
                        </div>
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <!-- Email Dedicated Filter -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-danger">
                                        <i class="bi bi-envelope-at me-1"></i> Email Column *
                                    </label>
                                    <select name="email_column" id="emailColSelect" class="form-select form-select-sm border-danger">
                                        <option value="auto">Auto-Detect Email</option>
                                    </select>
                                    <div class="form-text small text-muted" style="font-size:11px;">Primary email address field.</div>
                                </div>

                                <!-- First Name / Full Name Filter -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-primary">
                                        <i class="bi bi-person me-1"></i> First Name / Full Name
                                    </label>
                                    <select name="first_name_column" id="firstNameColSelect" class="form-select form-select-sm">
                                        <option value="auto">Auto-Detect Name</option>
                                    </select>
                                    <div class="form-text small text-muted" style="font-size:11px;">First name or full single name.</div>
                                </div>

                                <!-- Last Name Filter -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">
                                        <i class="bi bi-person-badge me-1"></i> Last Name (Optional)
                                    </label>
                                    <select name="last_name_column" id="lastNameColSelect" class="form-select form-select-sm">
                                        <option value="auto">Auto-Detect (or None)</option>
                                    </select>
                                    <div class="form-text small text-muted" style="font-size:11px;">Surname or family name.</div>
                                </div>
                            </div>

                            <!-- Live Sample Preview Table -->
                            <div class="mt-3">
                                <label class="form-label small fw-semibold text-muted d-flex justify-content-between">
                                    <span>Sample Data Preview (First 5 Rows)</span>
                                    <span id="sampleRowCountBadge" class="badge bg-secondary">0 rows</span>
                                </label>
                                <div class="table-responsive border rounded" style="max-height: 160px; overflow-y: auto;">
                                    <table id="previewDataTable" class="table table-sm table-striped table-bordered mb-0 small" style="font-size: 11px;">
                                        <thead class="table-light"></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dedicated Advanced Import & Filter Options -->
                    <div class="card border">
                        <div class="card-header bg-light py-2">
                            <span class="small fw-bold text-dark">
                                <i class="bi bi-sliders me-1 text-secondary"></i> Dedicated Filtering & Hygiene Rules
                            </span>
                        </div>
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="extract_regex" id="extractRegexOpt" value="1" checked>
                                        <label class="form-check-label small fw-semibold" for="extractRegexOpt">
                                            Regex Extraction Filter
                                        </label>
                                        <div class="form-text small text-muted" style="font-size:11px;">
                                            Extracts pure email even from mixed text (e.g. <code>John &lt;user@site.com&gt;</code>).
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="skip_suppressed" id="skipSuppressedOpt" value="1" checked>
                                        <label class="form-check-label small fw-semibold" for="skipSuppressedOpt">
                                            Suppression List Filter (Recommended)
                                        </label>
                                        <div class="form-text small text-muted" style="font-size:11px;">
                                            Automatically filters out unsubscribed, bounced, or suppressed emails.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold mb-1">Duplicate Handling</label>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="radio" name="duplicate_mode" id="dupUpdate" value="update" checked>
                                        <label class="form-check-label small" for="dupUpdate">
                                            <strong>Update Existing</strong> - Refresh contact name and attach to list
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="duplicate_mode" id="dupSkip" value="skip">
                                        <label class="form-check-label small" for="dupSkip">
                                            <strong>Skip Duplicates</strong> - Keep existing records untouched
                                        </label>
                                    </div>

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="has_header" id="hasHeaderOpt" value="1" checked>
                                        <label class="form-check-label small" for="hasHeaderOpt">
                                            First row is a header row
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitImport" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Fallback Alias for importCsvModal trigger button if any other views refer to it -->
<div class="modal fade" id="importCsvModal" tabindex="-1" style="display:none;" aria-hidden="true"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Redirect any click on old #importCsvModal to #importModal
    const oldModalEl = document.getElementById('importCsvModal');
    if (oldModalEl) {
        oldModalEl.addEventListener('show.bs.modal', function (e) {
            e.preventDefault();
            const newModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('importModal'));
            newModal.show();
        });
    }

    const fileInput = document.getElementById('importFileInput');
    const loadingEl = document.getElementById('importPreviewLoading');
    const mappingSection = document.getElementById('columnMappingSection');
    const emailSelect = document.getElementById('emailColSelect');
    const firstNameSelect = document.getElementById('firstNameColSelect');
    const lastNameSelect = document.getElementById('lastNameColSelect');
    const previewTable = document.getElementById('previewDataTable');
    const rowCountBadge = document.getElementById('sampleRowCountBadge');
    const formatBadge = document.getElementById('fileBadgeFormat');
    const hasHeaderCheckbox = document.getElementById('hasHeaderOpt');

    fileInput.addEventListener('change', function () {
        if (!this.files || !this.files[0]) {
            mappingSection.style.display = 'none';
            return;
        }

        const file = this.files[0];
        const formData = new FormData();
        formData.append('import_file', file);

        // Fetch CSRF Token
        const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
        formData.append('_token', csrfToken);

        loadingEl.style.display = 'block';
        mappingSection.style.display = 'none';

        fetch('<?= url("/api/contacts/import-preview") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            loadingEl.style.display = 'none';

            if (!res.success || !res.data) {
                // If preview fails, keep standard form functional without mapping preview
                console.warn('Preview note:', res.message || 'File inspection skipped');
                return;
            }

            const data = res.data;
            formatBadge.textContent = (data.format || 'file').toUpperCase();
            rowCountBadge.textContent = `${data.total_rows || 0} rows found`;

            if (typeof data.has_header !== 'undefined') {
                hasHeaderCheckbox.checked = !!data.has_header;
            }

            // Populate Column Selectors
            const headers = data.headers || [];
            
            // Helper to populate select
            function populateSelect(selectEl, defaultLabel, suggestedIdx) {
                selectEl.innerHTML = '';
                const autoOpt = document.createElement('option');
                autoOpt.value = 'auto';
                autoOpt.textContent = defaultLabel;
                selectEl.appendChild(autoOpt);

                headers.forEach((h, idx) => {
                    const opt = document.createElement('option');
                    opt.value = idx;
                    opt.textContent = `Column ${idx + 1}: ${h}`;
                    if (suggestedIdx !== null && suggestedIdx === idx) {
                        opt.selected = true;
                    }
                    selectEl.appendChild(opt);
                });
            }

            const suggested = data.suggested || {};
            populateSelect(emailSelect, 'Auto-Detect Email', suggested.email_idx !== undefined ? suggested.email_idx : null);
            populateSelect(firstNameSelect, 'Auto-Detect First / Full Name', suggested.first_name_idx !== undefined ? suggested.first_name_idx : (suggested.full_name_idx || null));
            populateSelect(lastNameSelect, 'Auto-Detect Last Name (or None)', suggested.last_name_idx !== undefined ? suggested.last_name_idx : null);

            // Populate Preview Table
            const thead = previewTable.querySelector('thead');
            const tbody = previewTable.querySelector('tbody');
            thead.innerHTML = '';
            tbody.innerHTML = '';

            const headerRow = document.createElement('tr');
            headers.forEach(h => {
                const th = document.createElement('th');
                th.textContent = h;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);

            const sampleRows = data.sample_rows || [];
            sampleRows.forEach(row => {
                const tr = document.createElement('tr');
                headers.forEach((h, colIdx) => {
                    const td = document.createElement('td');
                    td.textContent = row[colIdx] !== undefined ? row[colIdx] : '';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });

            mappingSection.style.display = 'block';
        })
        .catch(err => {
            loadingEl.style.display = 'none';
            console.error('Import preview error:', err);
        });
    });

    // Clear new list name if existing list selected, and vice versa
    const destSelect = document.getElementById('destListSelect');
    const newNameInput = document.getElementById('newListNameInput');

    newNameInput.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            destSelect.value = '';
        }
    });

    destSelect.addEventListener('change', function () {
        if (this.value !== '') {
            newNameInput.value = '';
        }
    });
});
</script>
