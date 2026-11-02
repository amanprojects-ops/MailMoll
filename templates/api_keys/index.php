<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-key-fill text-primary me-2"></i>REST API Keys</h4>
        <p class="text-secondary small mb-0">Manage bearer tokens to enqueue transactional emails programmatically from your backends.</p>
    </div>
    <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createKeyModal">
        <i class="bi bi-plus-lg"></i> Create New API Key
    </button>
</div>

<?php if ($newKey = \App\Helpers\Session::getFlash('new_api_key')): ?>
    <div class="alert alert-warning border-warning p-4 rounded-3 mb-4 shadow-sm">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-shield-exclamation fs-3 text-warning"></i>
            <div class="flex-grow-1">
                <h5 class="fw-bold text-dark mb-1">Save your Secret Key now</h5>
                <p class="text-secondary small mb-3">
                    Please copy this secret key immediately. For security reasons, <strong>it will never be displayed again</strong>.
                </p>
                <div class="input-group">
                    <input type="text" class="form-control font-monospace bg-white text-dark fw-bold" id="newSecretKeyInput" value="<?= e($newKey) ?>" readonly>
                    <button class="btn btn-dark" type="button" onclick="navigator.clipboard.writeText('<?= e($newKey) ?>'); alert('Key copied to clipboard!');">
                        <i class="bi bi-clipboard me-1"></i> Copy Secret
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- API Keys Table Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0 text-light">Active API Keys</h6>
        <span class="badge bg-secondary-subtle text-secondary"><?= count($apiKeys) ?> Total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-secondary small text-uppercase">
                    <th class="ps-4 py-3">Key Label</th>
                    <th>Token Prefix</th>
                    <th>Scopes</th>
                    <th>Created</th>
                    <th>Last Used</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($apiKeys)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <i class="bi bi-key fs-1 d-block mb-2 text-muted"></i>
                            No API keys found. Generate a key above to start integrating with MailFlow API.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($apiKeys as $key): ?>
                        <tr>
                            <td class="ps-4 fw-semibold text-light">
                                <i class="bi bi-shield-lock me-2 text-primary"></i><?= e($key['name']) ?>
                            </td>
                            <td>
                                <code class="bg-body-secondary px-2 py-1 rounded small"><?= e($key['key_prefix']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success me-1">send</span>
                                <span class="badge bg-info-subtle text-info">read</span>
                            </td>
                            <td class="text-secondary small"><?= e(date('M j, Y H:i', strtotime($key['created_at']))) ?></td>
                            <td class="text-secondary small">
                                <?= $key['last_used_at'] ? e(date('M j, Y H:i', strtotime($key['last_used_at']))) : '<span class="text-muted">Never</span>' ?>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="<?= url('/api-keys/delete/' . $key['id']) ?>" method="POST" onsubmit="return confirm('Revoke this API key? Any applications using this key will immediately lose access.');" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Revoke Key">
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

<!-- API Quickstart Card -->
<div class="card border-0 shadow-sm p-4">
    <h5 class="fw-bold text-light mb-2"><i class="bi bi-terminal text-primary me-2"></i>Quickstart Integration</h5>
    <p class="text-secondary small mb-3">Authenticate requests by passing your API token in the <code>Authorization</code> header:</p>
    <div class="bg-black p-3 rounded-3 font-monospace small">
        <span class="text-secondary"># Enqueue transactional email</span><br>
        <span class="text-primary">curl</span> -X POST <?= e(url('/api/v1/emails/send')) ?> \<br>
        &nbsp;&nbsp;-H <span class="text-warning">"Authorization: Bearer YOUR_SECRET_KEY"</span> \<br>
        &nbsp;&nbsp;-H <span class="text-warning">"Content-Type: application/json"</span> \<br>
        &nbsp;&nbsp;-d <span class="text-success">'{"to": "user@domain.com", "subject": "Hello", "body_html": "&lt;p&gt;Hi!&lt;/p&gt;"}'</span>
    </div>
</div>

<!-- Create Key Modal -->
<div class="modal fade" id="createKeyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary-subtle">
            <div class="modal-header border-secondary-subtle">
                <h5 class="modal-title text-light fw-bold">Create New REST API Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/api-keys/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="key_name" class="form-label text-light small fw-semibold">Key Identifier / Name</label>
                        <input type="text" class="form-control bg-body-tertiary border-secondary-subtle text-light" id="key_name" name="name" placeholder="e.g. Production Webhook Server, Mobile Backend" required autofocus>
                        <div class="form-text text-secondary small">Give this key a descriptive name so you remember where it is deployed.</div>
                    </div>
                </div>
                <div class="modal-footer border-secondary-subtle">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-key me-1"></i> Generate Secret Key</button>
                </div>
            </form>
        </div>
    </div>
</div>
