<?php
$defaultHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Notification</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 30px 15px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #6366f1; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Hello, {{first_name}}!</h1>
        </div>
        <div style="padding: 30px; color: #334155; line-height: 1.6; font-size: 15px;">
            <p>Welcome to our service, {{first_name}} {{last_name}}!</p>
            <p>This is a customizable email template. You can edit this text directly in the Visual Rich-Text editor or switch to HTML Source Code.</p>
            <div style="margin: 30px 0; text-align: center;">
                <a href="#" style="background-color: #6366f1; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; display: inline-block;">Get Started</a>
            </div>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                You are receiving this at {{email}}. To unsubscribe, click <a href="{{unsubscribe_url}}" style="color: #6366f1;">here</a>.
            </p>
        </div>
    </div>
</body>
</html>
HTML;

$currentHtml = $template['body_html'] ?? old('body_html', $defaultHtml);
?>

<div class="mb-4">
    <a href="<?= url('/templates') ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Templates
    </a>
    <h4 class="fw-bold mt-2 mb-1"><?= $template ? 'Edit Template: ' . e($template['name']) : 'Create Email Template' ?></h4>
    <p class="text-muted small">Design responsive email templates using Visual Rich-Text or raw HTML with real-time preview.</p>
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
                    <span class="small fw-semibold text-muted me-1"><i class="bi bi-tags-fill me-1 text-primary"></i> Merge Tags:</span>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{first_name}}')">{{first_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{last_name}}')">{{last_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{email}}')">{{email}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onclick="insertPlaceholder('{{unsubscribe_url}}')">{{unsubscribe_url}}</button>
                </div>

                <!-- Editor Mode Tabs -->
                <ul class="nav nav-tabs mb-0" id="editorModeTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active fw-semibold small" id="quill-tab" data-bs-toggle="tab" data-bs-target="#quill-pane" role="tab">
                            <i class="bi bi-body-text me-1 text-primary"></i> Visual Rich-Text
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link fw-semibold small" id="codemirror-tab" data-bs-toggle="tab" data-bs-target="#codemirror-pane" role="tab">
                            <i class="bi bi-code-slash me-1"></i> HTML Source Code
                        </button>
                    </li>
                </ul>

                <div class="tab-content border-start border-end border-bottom rounded-bottom" id="editorPanes">
                    <!-- Visual Mode (Active by default for visual editing) -->
                    <div class="tab-pane fade show active p-0" id="quill-pane" role="tabpanel">
                        <div id="quillToolbar">
                            <span class="ql-formats">
                                <select class="ql-header">
                                    <option value="1">Heading 1</option>
                                    <option value="2">Heading 2</option>
                                    <option value="3">Heading 3</option>
                                    <option selected>Normal</option>
                                </select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <select class="ql-color"></select>
                                <select class="ql-background"></select>
                            </span>
                            <span class="ql-formats">
                                <select class="ql-align"></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-list" value="ordered"></button>
                                <button class="ql-list" value="bullet"></button>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                                <button class="ql-clean"></button>
                            </span>
                        </div>
                        <div id="quillTemplateEditor" style="height: 380px; font-size: 15px; background: #ffffff;"></div>
                    </div>

                    <!-- HTML Source Mode -->
                    <div class="tab-pane fade p-0" id="codemirror-pane" role="tabpanel">
                        <textarea id="codeMirrorTextarea"><?= htmlspecialchars($currentHtml) ?></textarea>
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
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold small text-muted">
                        <i class="bi bi-display me-1 text-primary"></i> Live Responsive Preview
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" id="btnPreviewDesktop" title="Desktop View (100%)">
                                <i class="bi bi-laptop"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPreviewMobile" title="Mobile View (375px)">
                                <i class="bi bi-phone"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateLivePreview()" title="Refresh Preview">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-2 d-flex justify-content-center align-items-stretch bg-light" style="overflow-x: auto; min-height: 540px;">
                    <div id="previewContainer" style="width: 100%; transition: width 0.3s ease;">
                        <iframe id="previewIframe" class="w-100 h-100 border rounded shadow-sm bg-white" style="min-height: 520px;" sandbox="allow-same-origin"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.CodeMirror {
    height: 420px;
    font-family: 'Fira Code', 'Consolas', monospace;
    font-size: 13px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
.ql-toolbar.ql-snow {
    border-color: #dee2e6;
    background: #f8fafc;
}
.ql-container.ql-snow {
    border-color: #dee2e6;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
.ql-editor {
    min-height: 380px;
}
</style>

<script>
let cmEditor;
let quillEditor;
let activeMode = 'visual';

// Document wrapper state for full HTML emails
let docWrapper = {
    hasWrapper: false,
    prefix: '',
    suffix: ''
};

function parseHtmlForVisual(raw) {
    if (raw && raw.includes('<body') && raw.includes('</body>')) {
        const bodyMatch = raw.match(/<body[^>]*>/i);
        if (bodyMatch) {
            const startIdx = raw.indexOf(bodyMatch[0]) + bodyMatch[0].length;
            const endIdx = raw.lastIndexOf('</body>');
            docWrapper.hasWrapper = true;
            docWrapper.prefix = raw.substring(0, startIdx);
            docWrapper.suffix = raw.substring(endIdx);
            return raw.substring(startIdx, endIdx).trim();
        }
    }
    docWrapper.hasWrapper = false;
    docWrapper.prefix = '';
    docWrapper.suffix = '';
    return raw || '';
}

function assembleFullHtml(content) {
    if (docWrapper.hasWrapper && docWrapper.prefix) {
        return docWrapper.prefix + "\n" + content + "\n" + docWrapper.suffix;
    }
    return content;
}

document.addEventListener('DOMContentLoaded', function() {
    const rawInitial = document.getElementById('codeMirrorTextarea').value;
    const visualInitial = parseHtmlForVisual(rawInitial);

    // 1. Initialize Quill (Visual Rich-Text)
    quillEditor = new Quill('#quillTemplateEditor', {
        modules: {
            toolbar: '#quillToolbar'
        },
        theme: 'snow'
    });

    if (visualInitial) {
        if (quillEditor.clipboard && typeof quillEditor.clipboard.dangerouslyPasteHTML === 'function') {
            quillEditor.clipboard.dangerouslyPasteHTML(visualInitial);
        } else {
            quillEditor.root.innerHTML = visualInitial;
        }
    }

    // 2. Initialize CodeMirror (HTML Source Code)
    const textarea = document.getElementById('codeMirrorTextarea');
    cmEditor = CodeMirror.fromTextArea(textarea, {
        mode: 'htmlmixed',
        theme: 'dracula',
        lineNumbers: true,
        lineWrapping: true
    });
    cmEditor.setValue(rawInitial);

    // 3. Tab Switching Handlers
    const quillTab = document.getElementById('quill-tab');
    const codemirrorTab = document.getElementById('codemirror-tab');

    quillTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'visual';
        const rawCode = cmEditor.getValue();
        const extracted = parseHtmlForVisual(rawCode);
        if (quillEditor.clipboard && typeof quillEditor.clipboard.dangerouslyPasteHTML === 'function') {
            quillEditor.clipboard.dangerouslyPasteHTML(extracted);
        } else {
            quillEditor.root.innerHTML = extracted;
        }
        updateLivePreview();
    });

    codemirrorTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'code';
        const fullHtml = assembleFullHtml(quillEditor.root.innerHTML);
        cmEditor.setValue(fullHtml);
        cmEditor.refresh();
        updateLivePreview();
    });

    // 4. Real-time change events for Live Preview
    cmEditor.on('change', function() {
        if (activeMode === 'code') {
            updateLivePreview();
        }
    });

    quillEditor.on('text-change', function() {
        if (activeMode === 'visual') {
            updateLivePreview();
        }
    });

    // 5. Responsive Preview Controls (Desktop / Mobile)
    const btnDesktop = document.getElementById('btnPreviewDesktop');
    const btnMobile = document.getElementById('btnPreviewMobile');
    const previewContainer = document.getElementById('previewContainer');

    btnDesktop.addEventListener('click', function() {
        btnDesktop.classList.add('active');
        btnMobile.classList.remove('active');
        previewContainer.style.width = '100%';
    });

    btnMobile.addEventListener('click', function() {
        btnMobile.classList.add('active');
        btnDesktop.classList.remove('active');
        previewContainer.style.width = '375px';
    });

    // Initial Live Preview
    updateLivePreview();
});

function insertPlaceholder(tag) {
    if (activeMode === 'code') {
        const doc = cmEditor.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(tag, cursor);
    } else {
        const range = quillEditor.getSelection(true) || { index: quillEditor.getLength(), length: 0 };
        quillEditor.insertText(range.index, tag);
    }
    updateLivePreview();
}

function updateLivePreview() {
    let rawHtml = '';
    if (activeMode === 'code' && cmEditor) {
        rawHtml = cmEditor.getValue();
    } else if (quillEditor) {
        rawHtml = assembleFullHtml(quillEditor.root.innerHTML);
    }

    // Replace merge tags with realistic preview values
    const rendered = rawHtml
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsub');

    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        iframe.srcdoc = rendered;
    }
}

function saveTemplate() {
    let finalHtml = '';
    if (activeMode === 'code' && cmEditor) {
        finalHtml = cmEditor.getValue();
    } else if (quillEditor) {
        finalHtml = assembleFullHtml(quillEditor.root.innerHTML);
    }

    document.getElementById('hidden_body_html').value = finalHtml;
    document.getElementById('templateForm').submit();
}
</script>
