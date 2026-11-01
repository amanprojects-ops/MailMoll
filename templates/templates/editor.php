<div class="mb-4">
    <a href="<?= url('/templates') ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Templates
    </a>
    <h4 class="fw-bold mt-2 mb-1"><?= $template ? 'Edit Template: ' . e($template['name']) : 'Create Email Template' ?></h4>
    <p class="text-muted small">Dual HTML/WYSIWYG email template editor with placeholder tags.</p>
</div>

<form action="<?= $template ? url('/templates/update/' . $template['id']) : url('/templates/store') ?>" method="POST" id="templateForm">
    <?= csrf_field() ?>
    <input type="hidden" name="body_html" id="hidden_body_html" value="">

    <div class="row g-4">
        <!-- Editor Controls & Settings -->
        <div class="col-lg-7">
            <div class="card p-3 mb-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Template Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= e($template['name'] ?? old('name')) ?>" placeholder="e.g. Monthly Newsletter" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Default Subject</label>
                        <input type="text" name="subject_default" class="form-control" value="<?= e($template['subject_default'] ?? old('subject_default')) ?>" placeholder="e.g. Your Monthly Digest">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Tags (comma-separated)</label>
                        <input type="text" name="tags" class="form-control" value="<?= e($template['tags'] ?? old('tags')) ?>" placeholder="e.g. newsletter, marketing, updates">
                    </div>
                </div>

                <!-- Merge Tags Bar -->
                <div class="d-flex flex-wrap align-items-center gap-1 mb-3 p-2 bg-light rounded">
                    <span class="small fw-semibold text-muted me-1">Merge Tags:</span>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{first_name}}')">{{first_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{last_name}}')">{{last_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{email}}')">{{email}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{unsubscribe_url}}')">{{unsubscribe_url}}</button>
                </div>

                <!-- Editor Mode Tabs -->
                <ul class="nav nav-tabs mb-3" id="editorModeTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active fw-semibold small" id="codemirror-tab" data-bs-toggle="tab" data-bs-target="#codemirror-pane">
                            <i class="bi bi-code-slash me-1"></i> HTML Source Code
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link fw-semibold small" id="quill-tab" data-bs-toggle="tab" data-bs-target="#quill-pane">
                            <i class="bi bi-body-text me-1"></i> Visual Rich-Text
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="editorPanes">
                    <!-- HTML Mode -->
                    <div class="tab-pane fade show active" id="codemirror-pane">
                        <textarea id="codeMirrorTextarea"><?= htmlspecialchars($template['body_html'] ?? '<div style="font-family: Arial, sans-serif; padding: 20px;"><h2>Hello, {{first_name}}!</h2><p>This is your email template.</p><p><a href="{{unsubscribe_url}}">Unsubscribe</a></p></div>') ?></textarea>
                    </div>

                    <!-- Visual Mode -->
                    <div class="tab-pane fade" id="quill-pane">
                        <div id="quillTemplateEditor" style="height: 380px; background: #fff;"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="<?= url('/templates') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    <button type="button" class="btn btn-primary btn-sm px-4" onclick="saveTemplate()">
                        <i class="bi bi-save me-1"></i> Save Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Preview Pane -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold small"><i class="bi bi-display me-1 text-primary"></i> Live Responsive Preview</span>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="updateLivePreview()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body p-0">
                    <iframe id="previewIframe" class="w-100 h-100 border-0" style="min-height: 520px; background: #fff;"></iframe>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let cmEditor;
let quillEditor;
let activeMode = 'code';

document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize CodeMirror
    const textarea = document.getElementById('codeMirrorTextarea');
    cmEditor = CodeMirror.fromTextArea(textarea, {
        mode: 'htmlmixed',
        theme: 'dracula',
        lineNumbers: true,
        lineWrapping: true
    });

    // 2. Initialize Quill
    quillEditor = new Quill('#quillTemplateEditor', {
        theme: 'snow'
    });
    quillEditor.root.innerHTML = cmEditor.getValue();

    // 3. Tab Sync
    document.getElementById('quill-tab').addEventListener('shown.bs.tab', function() {
        activeMode = 'visual';
        quillEditor.root.innerHTML = cmEditor.getValue();
        updateLivePreview();
    });

    document.getElementById('codemirror-tab').addEventListener('shown.bs.tab', function() {
        activeMode = 'code';
        cmEditor.setValue(quillEditor.root.innerHTML);
        cmEditor.refresh();
        updateLivePreview();
    });

    cmEditor.on('change', function() {
        updateLivePreview();
    });

    // Initial preview
    updateLivePreview();
});

function insertPlaceholder(tag) {
    if (activeMode === 'code') {
        const doc = cmEditor.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(tag, cursor);
    } else {
        const range = quillEditor.getSelection(true);
        quillEditor.insertText(range.index, tag);
    }
    updateLivePreview();
}

function updateLivePreview() {
    const rawHtml = activeMode === 'code' ? cmEditor.getValue() : quillEditor.root.innerHTML;
    // Replace dummy variables for live preview
    const rendered = rawHtml
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#');

    const iframe = document.getElementById('previewIframe');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(rendered);
        iframe.contentWindow.document.close();
    }
}

function saveTemplate() {
    const finalHtml = activeMode === 'code' ? cmEditor.getValue() : quillEditor.root.innerHTML;
    document.getElementById('hidden_body_html').value = finalHtml;
    document.getElementById('templateForm').submit();
}
</script>
