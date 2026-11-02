
<!-- Page Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">SMTP Infrastructure</h4>
        <p class="text-muted small mb-0">Configure, monitor, and load-balance your outbound SMTP relay servers.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newGroupModal">
            <i class="bi bi-collection-fill me-1"></i> New SMTP Group
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newSmtpModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Add SMTP Account
        </button>
    </div>
</div>

<!-- Tabs for Accounts and Groups -->
<ul class="nav nav-pills mb-4" id="smtpTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="accounts-tab" data-bs-toggle="pill" data-bs-target="#accounts-content">
            <i class="bi bi-server me-1"></i> SMTP Accounts (<?= count($accounts) ?>)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="groups-tab" data-bs-toggle="pill" data-bs-target="#groups-content">
            <i class="bi bi-shuffle me-1"></i> Routing Groups (<?= count($groups) ?>)
        </button>
    </li>
</ul>

<div class="tab-content" id="smtpTabsContent">
    <!-- Accounts Tab -->
    <div class="tab-pane fade show active" id="accounts-content">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Account & Provider</th>
                                <th>Host / Port</th>
                                <th>From Address</th>
                                <th>Hourly Quota</th>
                                <th>Daily Quota</th>
                                <th>Status</th>
                                <th>Last Test</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($accounts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-hdd-network fs-1 d-block mb-2"></i>
                                        No SMTP accounts added yet. Click <strong>"Add SMTP Account"</strong> to register your first server.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($accounts as $acc): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold small"><?= e($acc['name']) ?></div>
                                            <span class="badge bg-light text-secondary border small text-uppercase" style="font-size: 10px;"><?= e($acc['provider']) ?></span>
                                            <span class="text-muted small" style="font-size: 11px;">Prio: <?= $acc['priority'] ?> | W: <?= $acc['weight'] ?></span>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= e($acc['host']) ?>:<?= e($acc['port']) ?></div>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase" style="font-size: 10px;"><?= e($acc['encryption']) ?></span>
                                        </td>
                                        <td>
                                            <div class="small"><?= e($acc['from_name']) ?></div>
                                            <div class="text-muted" style="font-size: 11px;">&lt;<?= e($acc['from_email']) ?>&gt;</div>
                                        </td>
                                        <td style="min-width: 130px;">
                                            <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 11px;">
                                                <span><?= $acc['hourly_used'] ?></span>
                                                <span>/ <?= $acc['hourly_limit'] ?: '∞' ?></span>
                                            </div>
                                            <div class="quota-bar">
                                                <div class="quota-progress bg-info" style="width: <?= $acc['hourly_percent'] ?>%;"></div>
                                            </div>
                                        </td>
                                        <td style="min-width: 130px;">
                                            <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 11px;">
                                                <span><?= $acc['daily_used'] ?></span>
                                                <span>/ <?= $acc['daily_limit'] ?: '∞' ?></span>
                                            </div>
                                            <div class="quota-bar">
                                                <div class="quota-progress <?= $acc['daily_percent'] > 90 ? 'bg-danger' : ($acc['daily_percent'] > 70 ? 'bg-warning' : 'bg-primary') ?>" 
                                                     style="width: <?= $acc['daily_percent'] ?>%;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-<?= $acc['status'] ?>"><?= e($acc['status']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($acc['last_test_result']): ?>
                                                <span class="badge <?= $acc['last_test_result'] === 'SUCCESS' ? 'bg-success' : 'bg-danger' ?> text-white" style="font-size: 10px;">
                                                    <?= e($acc['last_test_result']) ?>
                                                </span>
                                                <div class="text-muted" style="font-size: 10px;"><?= $acc['last_tested_at'] ? date('M d, H:i', strtotime($acc['last_tested_at'])) : '' ?></div>
                                            <?php else: ?>
                                                <span class="text-muted small" style="font-size: 11px;">Untested</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-secondary" onclick="testSmtpConnection(<?= $acc['id'] ?>)" title="Test SMTP Handshake">
                                                    <i class="bi bi-lightning-charge-fill text-warning"></i> Test
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSmtpModal<?= $acc['id'] ?>" title="Edit Settings">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <form action="<?= url('/smtp/toggle/' . $acc['id']) ?>" method="POST" class="d-inline m-0">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-secondary" title="<?= $acc['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                                        <i class="bi <?= $acc['status'] === 'active' ? 'bi-pause-fill' : 'bi-play-fill' ?>"></i>
                                                    </button>
                                                </form>
                                                <form action="<?= url('/smtp/delete/' . $acc['id']) ?>" method="POST" class="d-inline m-0">
                                                    <?= csrf_field() ?>
                                                    <button type="button" class="btn btn-outline-danger btn-confirm-delete" data-message="Delete SMTP account '<?= e($acc['name']) ?>'?" title="Delete">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Edit SMTP Modal -->
                                    <div class="modal fade" id="editSmtpModal<?= $acc['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form action="<?= url('/smtp/update/' . $acc['id']) ?>" method="POST">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit SMTP: <?= e($acc['name']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Account Label *</label>
                                                                <input type="text" name="name" class="form-control" value="<?= e($acc['name']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Provider Preset</label>
                                                                <select name="provider" class="form-select">
                                                                    <option value="generic" <?= $acc['provider'] === 'generic' ? 'selected' : '' ?>>Generic SMTP</option>
                                                                    <option value="sendgrid" <?= $acc['provider'] === 'sendgrid' ? 'selected' : '' ?>>SendGrid</option>
                                                                    <option value="ses" <?= $acc['provider'] === 'ses' ? 'selected' : '' ?>>Amazon SES</option>
                                                                    <option value="mailgun" <?= $acc['provider'] === 'mailgun' ? 'selected' : '' ?>>Mailgun</option>
                                                                    <option value="google" <?= $acc['provider'] === 'google' ? 'selected' : '' ?>>Google Workspace</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">SMTP Host *</label>
                                                                <input type="text" name="host" class="form-control" value="<?= e($acc['host']) ?>" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small fw-semibold">Port *</label>
                                                                <input type="number" name="port" class="form-control" value="<?= e($acc['port']) ?>" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small fw-semibold">Encryption *</label>
                                                                <select name="encryption" class="form-select">
                                                                    <option value="tls" <?= $acc['encryption'] === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
                                                                    <option value="ssl" <?= $acc['encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                                                    <option value="none" <?= $acc['encryption'] === 'none' ? 'selected' : '' ?>>None</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small fw-semibold">Auth Method</label>
                                                                <select name="auth_type" class="form-select">
                                                                    <option value="LOGIN" <?= ($acc['auth_type'] ?? 'LOGIN') === 'LOGIN' ? 'selected' : '' ?>>LOGIN</option>
                                                                    <option value="PLAIN" <?= ($acc['auth_type'] ?? '') === 'PLAIN' ? 'selected' : '' ?>>PLAIN</option>
                                                                    <option value="CRAM-MD5" <?= ($acc['auth_type'] ?? '') === 'CRAM-MD5' ? 'selected' : '' ?>>CRAM-MD5</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Username *</label>
                                                                <input type="text" name="username" class="form-control" value="<?= e($acc['username']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Password (Leave blank to keep unchanged)</label>
                                                                <input type="password" name="password" class="form-control" placeholder="••••••••">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">From Email *</label>
                                                                <input type="email" name="from_email" class="form-control" value="<?= e($acc['from_email']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">From Name *</label>
                                                                <input type="text" name="from_name" class="form-control" value="<?= e($acc['from_name']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Hourly Quota Limit *</label>
                                                                <input type="number" name="hourly_limit" class="form-control" value="<?= e($acc['hourly_limit']) ?>" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-semibold">Daily Quota Limit *</label>
                                                                <input type="number" name="daily_limit" class="form-control" value="<?= e($acc['daily_limit']) ?>" required>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-semibold">Priority (1-100)</label>
                                                                <input type="number" name="priority" class="form-control" value="<?= e($acc['priority']) ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-semibold">Weight</label>
                                                                <input type="number" name="weight" class="form-control" value="<?= e($acc['weight']) ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-semibold">Status</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="active" <?= $acc['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                    <option value="inactive" <?= $acc['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Update SMTP Account</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups Tab -->
    <div class="tab-pane fade" id="groups-content">
        <div class="row g-4">
            <?php if (empty($groups)): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-collection fs-1 d-block mb-2"></i>
                    No SMTP Groups created. Groups allow automatic load-balancing (Round-Robin, Weighted, Least Used, Priority).
                </div>
            <?php else: ?>
                <?php foreach ($groups as $grp): ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-6"><?= e($grp['name']) ?></span>
                                <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase" style="font-size: 11px;">
                                    <?= str_replace('_', ' ', e($grp['routing_strategy'])) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3"><?= e($grp['description'] ?: 'No description provided.') ?></p>
                                <div class="fw-semibold small mb-2">Pool Members (<?= count($grp['members']) ?>):</div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php if (empty($grp['members'])): ?>
                                        <span class="text-muted small fst-italic">No accounts assigned to this group.</span>
                                    <?php else: ?>
                                        <?php foreach ($grp['members'] as $m): ?>
                                            <span class="badge bg-light text-dark border p-2">
                                                <i class="bi bi-server text-primary me-1"></i> <?= e($m['name']) ?>
                                                <span class="text-muted small ms-1">(Prio: <?= $m['priority'] ?>)</span>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editGroupModal<?= $grp['id'] ?>">
                                    <i class="bi bi-pencil-fill me-1"></i> Edit Pool
                                </button>
                                <form action="<?= url('/smtp/groups/delete/' . $grp['id']) ?>" method="POST" class="d-inline m-0">
                                    <?= csrf_field() ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-confirm-delete" data-message="Delete group '<?= e($grp['name']) ?>'?">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Group Modal -->
                    <div class="modal fade" id="editGroupModal<?= $grp['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= url('/smtp/groups/update/' . $grp['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Group: <?= e($grp['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Group Name *</label>
                                            <input type="text" name="name" class="form-control" value="<?= e($grp['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Routing Strategy *</label>
                                            <select name="routing_strategy" class="form-select">
                                                <option value="round_robin" <?= $grp['routing_strategy'] === 'round_robin' ? 'selected' : '' ?>>Round Robin (Circular Rotation)</option>
                                                <option value="weighted" <?= $grp['routing_strategy'] === 'weighted' ? 'selected' : '' ?>>Weighted Distribution (By Account Weight)</option>
                                                <option value="least_used" <?= $grp['routing_strategy'] === 'least_used' ? 'selected' : '' ?>>Least Used (Balance Daily Dispatch)</option>
                                                <option value="priority" <?= $grp['routing_strategy'] === 'priority' ? 'selected' : '' ?>>Priority Based (Highest Priority First)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Description</label>
                                            <textarea name="description" class="form-control" rows="2"><?= e($grp['description']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Assigned SMTP Accounts</label>
                                            <?php 
                                                $assignedIds = array_column($grp['members'], 'id');
                                                foreach ($accounts as $a): 
                                            ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="account_ids[]" value="<?= $a['id'] ?>" id="edit_grp_<?= $grp['id'] ?>_acc_<?= $a['id'] ?>" <?= in_array($a['id'], $assignedIds) ? 'checked' : '' ?>>
                                                    <label class="form-check-label small" for="edit_grp_<?= $grp['id'] ?>_acc_<?= $a['id'] ?>">
                                                        <?= e($a['name']) ?> (<?= e($a['host']) ?>)
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Save Group</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: New SMTP Account -->
<div class="modal fade" id="newSmtpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('/smtp/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-server text-primary me-2"></i> Register New SMTP Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Account Label *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. SendGrid Primary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Provider Preset</label>
                            <select name="provider" class="form-select">
                                <option value="generic">Generic SMTP</option>
                                <option value="sendgrid">SendGrid</option>
                                <option value="ses">Amazon SES</option>
                                <option value="mailgun">Mailgun</option>
                                <option value="google">Google Workspace</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">SMTP Host *</label>
                            <input type="text" name="host" class="form-control" placeholder="smtp.provider.com" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Port *</label>
                            <input type="number" name="port" class="form-control" value="587" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Encryption *</label>
                            <select name="encryption" class="form-select">
                                <option value="tls" selected>TLS (STARTTLS)</option>
                                <option value="ssl">SSL</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Auth Method</label>
                            <select name="auth_type" class="form-select">
                                <option value="LOGIN" selected>LOGIN</option>
                                <option value="PLAIN">PLAIN</option>
                                <option value="CRAM-MD5">CRAM-MD5</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username *</label>
                            <input type="text" name="username" class="form-control" placeholder="apikey, email, or user" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Password / API Key *</label>
                            <input type="password" name="password" class="form-control" placeholder="Encrypted at rest with AES-256" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">From Email *</label>
                            <input type="email" name="from_email" class="form-control" placeholder="verified-sender@domain.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">From Name *</label>
                            <input type="text" name="from_name" class="form-control" placeholder="Company Alerts" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Hourly Quota Limit *</label>
                            <input type="number" name="hourly_limit" class="form-control" value="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Daily Quota Limit *</label>
                            <input type="number" name="daily_limit" class="form-control" value="2000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Priority (1-100)</label>
                            <input type="number" name="priority" class="form-control" value="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Weight</label>
                            <input type="number" name="weight" class="form-control" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save & Register Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: New SMTP Group -->
<div class="modal fade" id="newGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('/smtp/groups/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-collection text-primary me-2"></i> Create Routing Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Group Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Transactional Rotator Pool" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Routing Strategy *</label>
                        <select name="routing_strategy" class="form-select">
                            <option value="round_robin">Round Robin (Circular Rotation)</option>
                            <option value="weighted">Weighted Distribution (By Account Weight)</option>
                            <option value="least_used">Least Used (Balance Daily Dispatch)</option>
                            <option value="priority">Priority Based (Highest Priority First)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Purpose or SLA notes"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Member Accounts</label>
                        <?php foreach ($accounts as $a): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="account_ids[]" value="<?= $a['id'] ?>" id="new_grp_acc_<?= $a['id'] ?>">
                                <label class="form-check-label small" for="new_grp_acc_<?= $a['id'] ?>">
                                    <?= e($a['name']) ?> (<?= e($a['host']) ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>
