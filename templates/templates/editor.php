<?php
$defaultHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Notification</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #6366f1; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Welcome to MailMoll!</h1>
        </div>
        <div style="padding: 30px; color: #334155; line-height: 1.6; font-size: 15px;">
            <p style="margin-top: 0;">Hello <strong>{{first_name}} {{last_name}}</strong>,</p>
            <p>Thank you for subscribing to our updates. We're thrilled to have you onboard.</p>
            <p>This is a customizable email template. You can edit this text directly in the Visual Design Editor or switch to HTML Source Code.</p>
            <div style="margin: 30px 0; text-align: center;">
                <a href="#" style="background-color: #6366f1; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; display: inline-block;">Explore Your Account</a>
            </div>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 35px; border-top: 1px solid #f1f5f9; padding-top: 20px; line-height: 1.5;">
                You received this email because you opted in with <strong>{{email}}</strong>.<br>
                If you no longer wish to receive these emails, you can <a href="{{unsubscribe_url}}" style="color: #6366f1;">unsubscribe here</a>.
            </p>
        </div>
    </div>
</body>
</html>
HTML;

$currentHtml = !empty($template['body_html']) ? $template['body_html'] : old('body_html', $defaultHtml);
?>

<div class="mb-4">
    <a href="<?= url('/templates') ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Templates
    </a>
    <h4 class="fw-bold mt-2 mb-1"><?= $template ? 'Edit Template: ' . e($template['name']) : 'Create Email Template' ?></h4>
    <p class="text-muted small">Design responsive email templates with live real-design preview and visual in-place editing.</p>
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
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertPlaceholder('{{first_name}}')">{{first_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertPlaceholder('{{last_name}}')">{{last_name}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertPlaceholder('{{email}}')">{{email}}</button>
                    <button type="button" class="btn btn-sm btn-white border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertPlaceholder('{{unsubscribe_url}}')">{{unsubscribe_url}}</button>
                </div>

                <!-- Editor Mode Tabs -->
                <ul class="nav nav-tabs mb-0" id="editorModeTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active fw-semibold small" id="visual-tab" data-bs-toggle="tab" data-bs-target="#visual-pane" role="tab">
                            <i class="bi bi-palette-fill me-1 text-primary"></i> Visual Design Editor
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link fw-semibold small" id="codemirror-tab" data-bs-toggle="tab" data-bs-target="#codemirror-pane" role="tab">
                            <i class="bi bi-code-slash me-1 text-primary"></i> HTML Source Code
                        </button>
                    </li>
                </ul>

                <div class="tab-content border-start border-end border-bottom rounded-bottom" id="editorPanes">
                    <!-- Visual Design Editor Mode -->
                    <div class="tab-pane fade show active p-0" id="visual-pane" role="tabpanel">
                        <!-- Rich Visual Toolbar -->
                        <div class="p-2 bg-light border-bottom d-flex flex-wrap align-items-center gap-1">
                            <select class="form-select form-select-sm d-inline-block w-auto" id="formatBlockSelect" onchange="execFormatBlock(this.value)">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('strikeThrough')" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>

                            <div class="vr mx-1"></div>

                            <!-- Text Color -->
                            <div class="d-inline-flex align-items-center position-relative">
                                <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Text Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                    <i class="bi bi-palette text-primary"></i>
                                    <input type="color" value="#334155" oninput="restoreSelection(); execCmd('foreColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                                </label>
                            </div>

                            <!-- Highlight Color -->
                            <div class="d-inline-flex align-items-center position-relative">
                                <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Highlight Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                    <i class="bi bi-paint-bucket text-warning"></i>
                                    <input type="color" value="#fef08a" oninput="restoreSelection(); execCmd('hiliteColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                                </label>
                            </div>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="insertLinkPrompt()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('unlink')" title="Remove Link"><i class="bi bi-link-45deg text-danger"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCmd('removeFormat')" title="Clear Formatting"><i class="bi bi-eraser"></i></button>
                        </div>

                        <!-- Visual Design Canvas (Direct in-place visual editing with Real Design preserved) -->
                        <div id="visualEditorCanvas" contenteditable="true" style="min-height: 420px; max-height: 580px; overflow-y: auto; background: #f8fafc; padding: 20px; outline: none; border: 1px solid #e2e8f0;"></div>
                    </div>
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

        <!-- Live Responsive Preview Pane -->
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold small text-muted">
                        <i class="bi bi-display me-1 text-primary"></i> Real Design Preview
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" id="btnPreviewDesktop" title="Desktop View (100%)">
                                <i class="bi bi-laptop"></i> Desktop
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnPreviewMobile" title="Mobile View (375px)">
                                <i class="bi bi-phone"></i> Mobile
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateLivePreview()" title="Refresh Preview">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <?php if (!empty($template['id'])): ?>
                            <a href="<?= url('/templates/preview/' . $template['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Open Preview in New Tab">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-2 d-flex justify-content-center align-items-stretch bg-light" style="overflow-x: auto; min-height: 560px;">
                    <div id="previewContainer" style="width: 100%; transition: width 0.3s ease;">
                        <iframe id="previewIframe" class="w-100 h-100 border rounded shadow-sm bg-white" style="min-height: 540px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.CodeMirror {
    height: 460px;
    font-family: 'Fira Code', 'Consolas', monospace;
    font-size: 13px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
#visualEditorCanvas:focus {
    box-shadow: inset 0 0 0 2px #6366f1;
}
</style>

<script>
let cmEditor;
let activeMode = 'visual';
let savedSelectionRange = null;

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

function saveCurrentSelection() {
    const sel = window.getSelection();
    if (sel && sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        const canvas = document.getElementById('visualEditorCanvas');
        if (canvas && canvas.contains(range.commonAncestorContainer)) {
            savedSelectionRange = range;
        }
    }
}

function restoreSelection() {
    if (savedSelectionRange) {
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedSelectionRange);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const rawInitial = document.getElementById('codeMirrorTextarea').value;
    const visualCanvas = document.getElementById('visualEditorCanvas');

    // 1. Initialize Visual Design Canvas with the exact real design
    const visualContent = parseHtmlForVisual(rawInitial);
    visualCanvas.innerHTML = visualContent;

    // Prevent clicking links inside the visual canvas from navigating away
    visualCanvas.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            e.preventDefault();
        }
        saveCurrentSelection();
    });

    visualCanvas.addEventListener('keyup', saveCurrentSelection);
    visualCanvas.addEventListener('mouseup', saveCurrentSelection);

    // 2. Initialize CodeMirror for HTML Source Code
    const textarea = document.getElementById('codeMirrorTextarea');
    cmEditor = CodeMirror.fromTextArea(textarea, {
        mode: 'htmlmixed',
        theme: 'dracula',
        lineNumbers: true,
        lineWrapping: true
    });
    cmEditor.setValue(rawInitial);

    // 3. Tab Switching Handlers
    const visualTab = document.getElementById('visual-tab');
    const codemirrorTab = document.getElementById('codemirror-tab');

    visualTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'visual';
        const rawCode = cmEditor.getValue();
        visualCanvas.innerHTML = parseHtmlForVisual(rawCode);
        updateLivePreview();
    });

    codemirrorTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'code';
        const fullHtml = assembleFullHtml(visualCanvas.innerHTML);
        cmEditor.setValue(fullHtml);
        cmEditor.refresh();
        updateLivePreview();
    });

    // 4. Real-time updates
    visualCanvas.addEventListener('input', function() {
        if (activeMode === 'visual') {
            updateLivePreview();
        }
    });

    cmEditor.on('change', function() {
        if (activeMode === 'code') {
            updateLivePreview();
        }
    });

    // 5. Form submission safeguard
    const form = document.getElementById('templateForm');
    if (form) {
        form.addEventListener('submit', function() {
            let finalHtml = '';
            if (activeMode === 'code' && cmEditor) {
                finalHtml = cmEditor.getValue();
            } else {
                finalHtml = assembleFullHtml(visualCanvas ? visualCanvas.innerHTML : '');
            }
            document.getElementById('hidden_body_html').value = finalHtml;
        });
    }

    // 6. Desktop / Mobile Responsive Toggle
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

    // Initial Live Preview Render
    updateLivePreview();
});

// Formatting command helper for Visual Editor
function execCmd(command, value = null) {
    const canvas = document.getElementById('visualEditorCanvas');
    canvas.focus();
    restoreSelection();
    document.execCommand(command, false, value);
    saveCurrentSelection();
    updateLivePreview();
}

function execFormatBlock(tag) {
    execCmd('formatBlock', '<' + tag + '>');
}

function insertLinkPrompt() {
    const url = prompt('Enter destination URL:', 'https://');
    if (url) {
        execCmd('createLink', url);
    }
}

function insertPlaceholder(tag) {
    if (activeMode === 'code') {
        const doc = cmEditor.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(tag, cursor);
    } else {
        const canvas = document.getElementById('visualEditorCanvas');
        canvas.focus();
        restoreSelection();
        document.execCommand('insertText', false, tag);
        saveCurrentSelection();
    }
    updateLivePreview();
}

// Renders the REAL DESIGN into the preview iframe
function updateLivePreview() {
    let fullHtml = '';
    if (activeMode === 'code' && cmEditor) {
        fullHtml = cmEditor.getValue();
    } else {
        const canvas = document.getElementById('visualEditorCanvas');
        fullHtml = assembleFullHtml(canvas ? canvas.innerHTML : '');
    }

    // Replace merge tags with realistic preview values
    let rendered = fullHtml
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsub');

    // Ensure preview has standard email typography fallback if fragment
    if (!rendered.toLowerCase().includes('<html')) {
        rendered = `<!DOCTYPE html><html><head><meta charset="utf-8"><base target="_blank"><style>body{margin:0;padding:20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background-color:#f8fafc;}</style></head><body>${rendered}</body></html>`;
    } else if (rendered.includes('<head>')) {
        rendered = rendered.replace('<head>', '<head><base target="_blank">');
    } else {
        rendered = '<base target="_blank">' + rendered;
    }

    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        iframe.srcdoc = rendered;
    }
}

function saveTemplate() {
    let finalHtml = '';
    if (activeMode === 'code' && cmEditor) {
        finalHtml = cmEditor.getValue();
    } else {
        const canvas = document.getElementById('visualEditorCanvas');
        finalHtml = assembleFullHtml(canvas ? canvas.innerHTML : '');
    }

    document.getElementById('hidden_body_html').value = finalHtml;
    document.getElementById('templateForm').submit();
}
</script>
