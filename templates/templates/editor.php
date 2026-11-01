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
            <p>This is a customizable email template. You can edit this text directly in the Visual Rich-Text editor or switch to HTML Source Code.</p>
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
    <p class="text-muted small">Design responsive email templates using Visual Rich-Text or raw HTML with a separate real-time HTML preview bar.</p>
</div>

<form action="<?= $template ? url('/templates/update/' . $template['id']) : url('/templates/store') ?>" method="POST" id="templateForm">
    <?= csrf_field() ?>
    <input type="hidden" name="body_html" id="hidden_body_html" value="">

    <div class="row g-4">
        <!-- Editor Controls & Settings -->
        <div class="col-lg-7">
            <div class="card p-3 mb-3 shadow-sm">
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

                <!-- Editor Mode Tabs Bar -->
                <ul class="nav nav-tabs mb-0" id="editorModeTabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active fw-semibold small" id="visual-tab" data-bs-toggle="tab" data-bs-target="#visual-pane" role="tab">
                            <i class="bi bi-body-text me-1 text-primary"></i> Visual Rich-Text
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link fw-semibold small" id="codemirror-tab" data-bs-toggle="tab" data-bs-target="#codemirror-pane" role="tab">
                            <i class="bi bi-code-slash me-1 text-primary"></i> HTML Source Code
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link fw-semibold small" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-pane" role="tab">
                            <i class="bi bi-eye-fill me-1 text-primary"></i> HTML Preview
                        </button>
                    </li>
                </ul>

                <div class="tab-content border-start border-end border-bottom rounded-bottom" id="editorPanes">
                    <!-- Visual Rich-Text Mode -->
                    <div class="tab-pane fade show active p-0" id="visual-pane" role="tabpanel">
                        <!-- Rich Visual Toolbar -->
                        <div class="p-2 bg-light border-bottom d-flex flex-wrap align-items-center gap-1">
                            <select class="form-select form-select-sm d-inline-block w-auto" onchange="execVisualFormatBlock(this.value)">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('strikeThrough')" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>

                            <div class="vr mx-1"></div>

                            <!-- Text Color -->
                            <div class="d-inline-flex align-items-center position-relative">
                                <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Text Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                    <i class="bi bi-palette text-primary"></i>
                                    <input type="color" value="#334155" oninput="execVisualColor(this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                                </label>
                            </div>

                            <!-- Highlight Color -->
                            <div class="d-inline-flex align-items-center position-relative">
                                <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Highlight Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                    <i class="bi bi-paint-bucket text-warning"></i>
                                    <input type="color" value="#fef08a" oninput="execVisualHilite(this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                                </label>
                            </div>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>

                            <div class="vr mx-1"></div>

                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="insertVisualLink()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('unlink')" title="Remove Link"><i class="bi bi-link-45deg text-danger"></i></button>
                            <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execVisualCmd('removeFormat')" title="Clear Formatting"><i class="bi bi-eraser"></i></button>
                        </div>

                        <!-- Visual Editor Frame (WYSIWYG inside iframe to preserve 100% of inline styles and CSS) -->
                        <div style="background: #f8fafc; padding: 12px; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;">
                            <iframe id="visualEditorFrame" class="w-100 bg-white border rounded shadow-sm" style="height: 460px;"></iframe>
                        </div>
                    </div>

                    <!-- HTML Source Mode (CodeMirror) -->
                    <div class="tab-pane fade p-0" id="codemirror-pane" role="tabpanel">
                        <textarea id="codeMirrorTextarea"><?= htmlspecialchars($currentHtml) ?></textarea>
                    </div>

                    <!-- Separate In-Tab HTML Preview -->
                    <div class="tab-pane fade p-2 bg-light" id="preview-pane" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="small fw-semibold text-muted"><i class="bi bi-display me-1 text-primary"></i> Full In-Tab Preview</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="updateLivePreview()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                            </button>
                        </div>
                        <iframe id="tabPreviewIframe" class="w-100 bg-white border rounded shadow-sm" style="min-height: 480px;"></iframe>
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

        <!-- Separate HTML Preview Bar (Right Column) -->
        <div class="col-lg-5" id="separatePreviewColumn">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <span class="fw-bold small text-dark">
                        <i class="bi bi-display me-1 text-primary"></i> Separate HTML Preview
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
                <div class="card-body p-2 d-flex justify-content-center align-items-stretch bg-light" style="overflow-x: auto; min-height: 520px;">
                    <div id="previewContainer" style="width: 100%; transition: width 0.3s ease;">
                        <iframe id="previewIframe" class="w-100 h-100 border rounded shadow-sm bg-white" style="min-height: 500px;"></iframe>
                    </div>
                </div>
                <!-- Quick Test Email Sender -->
                <div class="card-footer bg-white border-top p-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-send-check"></i></span>
                        <input type="email" id="quickTestEmail" class="form-control" placeholder="Send test to: recipient@domain.com">
                        <button type="button" class="btn btn-outline-primary" onclick="sendQuickTestEmail()">
                            Send Test
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.CodeMirror {
    height: 480px;
    font-family: 'Fira Code', 'Consolas', monospace;
    font-size: 13px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}
</style>

<script>
let cmEditor;
let activeMode = 'visual';

function getVisualHtml() {
    const frame = document.getElementById('visualEditorFrame');
    if (!frame || !frame.contentDocument || !frame.contentDocument.documentElement) {
        return '';
    }
    const doc = frame.contentDocument;
    const doctype = doc.doctype 
        ? `<!DOCTYPE ${doc.doctype.name}${doc.doctype.publicId ? ` PUBLIC "${doc.doctype.publicId}"` : ''}${doc.doctype.systemId ? ` "${doc.doctype.systemId}"` : ''}>\n` 
        : '<!DOCTYPE html>\n';
    return doctype + doc.documentElement.outerHTML;
}

function initVisualFrame(frame, html) {
    if (!frame) return;
    const doc = frame.contentDocument || frame.contentWindow.document;
    doc.open();
    doc.write(html);
    doc.close();

    try {
        doc.designMode = 'on';
        if (doc.body) {
            doc.body.contentEditable = 'true';
            doc.body.style.cursor = 'text';
            doc.body.style.outline = 'none';
        }
    } catch (e) {
        console.warn('designMode init error', e);
    }

    attachVisualFrameListeners(frame);
}

function attachVisualFrameListeners(frame) {
    try {
        const doc = frame.contentDocument || frame.contentWindow.document;
        if (!doc) return;

        // Prevent links inside visual editor from navigating
        doc.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                e.preventDefault();
            }
        });

        // Real-time sync on typing
        doc.addEventListener('input', function() {
            if (activeMode === 'visual') {
                syncFromVisual();
            }
        });

        doc.addEventListener('keyup', function() {
            if (activeMode === 'visual') {
                syncFromVisual();
            }
        });
    } catch (e) {
        console.warn('Listener error', e);
    }
}

function syncFromVisual() {
    const html = getVisualHtml();
    if (cmEditor && cmEditor.getValue() !== html) {
        cmEditor.setValue(html);
    }
    updateLivePreview(html);
}

document.addEventListener('DOMContentLoaded', function() {
    const rawInitial = document.getElementById('codeMirrorTextarea').value;

    // 1. Initialize CodeMirror (HTML Source Code)
    const textarea = document.getElementById('codeMirrorTextarea');
    cmEditor = CodeMirror.fromTextArea(textarea, {
        mode: 'htmlmixed',
        theme: 'dracula',
        lineNumbers: true,
        lineWrapping: true
    });
    cmEditor.setValue(rawInitial);

    // 2. Initialize Visual Editor Frame
    const frame = document.getElementById('visualEditorFrame');
    if (frame) {
        initVisualFrame(frame, rawInitial);
    }

    // 3. Tab Switching Handlers
    const visualTab = document.getElementById('visual-tab');
    const codemirrorTab = document.getElementById('codemirror-tab');
    const previewTab = document.getElementById('preview-tab');

    visualTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'visual';
        if (cmEditor) {
            const html = cmEditor.getValue();
            const f = document.getElementById('visualEditorFrame');
            if (f) {
                initVisualFrame(f, html);
            }
            updateLivePreview(html);
        }
    });

    codemirrorTab.addEventListener('shown.bs.tab', function() {
        activeMode = 'code';
        const html = getVisualHtml();
        if (html && cmEditor) {
            cmEditor.setValue(html);
            cmEditor.refresh();
        }
        updateLivePreview(html);
    });

    previewTab.addEventListener('shown.bs.tab', function() {
        updateLivePreview();
    });

    // 4. CodeMirror Real-time Sync
    cmEditor.on('change', function() {
        if (activeMode === 'code') {
            const html = cmEditor.getValue();
            updateLivePreview(html);
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
                finalHtml = getVisualHtml() || (cmEditor ? cmEditor.getValue() : '');
            }
            document.getElementById('hidden_body_html').value = finalHtml;
        });
    }

    // 6. Responsive Preview Controls (Desktop / Mobile)
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
    updateLivePreview(rawInitial);
});

// Formatting commands for Visual Editor
function execVisualCmd(command, value = null) {
    const frame = document.getElementById('visualEditorFrame');
    if (!frame || !frame.contentDocument) return;
    frame.contentWindow.focus();
    frame.contentDocument.execCommand(command, false, value);
    syncFromVisual();
}

function execVisualFormatBlock(tag) {
    execVisualCmd('formatBlock', '<' + tag + '>');
}

function execVisualColor(color) {
    execVisualCmd('foreColor', color);
}

function execVisualHilite(color) {
    execVisualCmd('hiliteColor', color);
}

function insertVisualLink() {
    const url = prompt('Enter destination URL:', 'https://');
    if (url) {
        execVisualCmd('createLink', url);
    }
}

function insertPlaceholder(tag) {
    if (activeMode === 'code' && cmEditor) {
        const doc = cmEditor.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(tag, cursor);
        const html = cmEditor.getValue();
        updateLivePreview(html);
    } else {
        const frame = document.getElementById('visualEditorFrame');
        if (frame && frame.contentDocument) {
            frame.contentWindow.focus();
            frame.contentDocument.execCommand('insertText', false, tag);
            syncFromVisual();
        }
    }
}

// Renders the REAL DESIGN into the separate preview bar
function updateLivePreview(rawHtml = null) {
    let fullHtml = rawHtml;
    if (!fullHtml) {
        if (activeMode === 'code' && cmEditor) {
            fullHtml = cmEditor.getValue();
        } else {
            fullHtml = getVisualHtml() || (cmEditor ? cmEditor.getValue() : '') || document.getElementById('codeMirrorTextarea')?.value || '';
        }
    }

    // Replace merge tags with realistic preview values
    let rendered = fullHtml
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsub');

    // Ensure preview has base target blank so links don't navigate the preview pane
    if (rendered.includes('<head>')) {
        rendered = rendered.replace('<head>', '<head><base target="_blank">');
    } else {
        rendered = '<base target="_blank">' + rendered;
    }

    const iframe = document.getElementById('previewIframe');
    if (iframe) {
        iframe.srcdoc = rendered;
    }

    const tabIframe = document.getElementById('tabPreviewIframe');
    if (tabIframe) {
        tabIframe.srcdoc = rendered;
    }
}

function sendQuickTestEmail() {
    const email = document.getElementById('quickTestEmail').value.trim();
    if (!email) {
        Swal.fire('Input Required', 'Please enter a recipient email address.', 'warning');
        return;
    }

    let fullHtml = activeMode === 'code' && cmEditor 
        ? cmEditor.getValue() 
        : (getVisualHtml() || (cmEditor ? cmEditor.getValue() : ''));

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const subject = document.querySelector('input[name="subject_default"]')?.value || 'Test Template';

    Swal.fire({
        title: 'Sending Test Email...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(window.MAILMOLL_URL + '/api/campaigns/test-send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-Token': csrfToken
        },
        body: new URLSearchParams({
            '_csrf_token': csrfToken,
            'test_email': email,
            'subject': subject,
            'body_html': fullHtml
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Sent!', data.message, 'success');
        } else {
            Swal.fire('Failed', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', err.message, 'error');
    });
}

function saveTemplate() {
    let finalHtml = '';
    if (activeMode === 'code' && cmEditor) {
        finalHtml = cmEditor.getValue();
    } else {
        finalHtml = getVisualHtml() || (cmEditor ? cmEditor.getValue() : '');
    }

    document.getElementById('hidden_body_html').value = finalHtml;
    document.getElementById('templateForm').submit();
}
</script>
