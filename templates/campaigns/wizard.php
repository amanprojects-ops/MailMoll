<div class="mb-4">
    <a href="<?= url('/campaigns') ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Campaigns
    </a>
    <h4 class="fw-bold mt-2 mb-1">Create New Campaign</h4>
    <p class="text-muted small">Configure and launch a compliant email broadcast in 6 guided steps.</p>
</div>

<!-- Wizard Step Navigation -->
<div class="wizard-steps d-none d-md-flex">
    <div class="wizard-step-item active" id="step-nav-1">
        <div class="wizard-step-circle">1</div>
        <div class="wizard-step-title">Information</div>
    </div>
    <div class="wizard-step-item" id="step-nav-2">
        <div class="wizard-step-circle">2</div>
        <div class="wizard-step-title">Sender & Routing</div>
    </div>
    <div class="wizard-step-item" id="step-nav-3">
        <div class="wizard-step-circle">3</div>
        <div class="wizard-step-title">Audience</div>
    </div>
    <div class="wizard-step-item" id="step-nav-4">
        <div class="wizard-step-circle">4</div>
        <div class="wizard-step-title">Email Content</div>
    </div>
    <div class="wizard-step-item" id="step-nav-5">
        <div class="wizard-step-circle">5</div>
        <div class="wizard-step-title">Schedule</div>
    </div>
    <div class="wizard-step-item" id="step-nav-6">
        <div class="wizard-step-circle">6</div>
        <div class="wizard-step-title">Review & Confirm</div>
    </div>
</div>

<form action="<?= url('/campaigns/store') ?>" method="POST" id="campaignWizardForm">
    <?= csrf_field() ?>
    <input type="hidden" name="body_html" id="hidden_body_html" value="">
    <input type="hidden" name="launch_immediately" id="hidden_launch_immediately" value="0">

    <div class="card p-4">
        <!-- STEP 1: CAMPAIGN INFO -->
        <div class="wizard-step-content" id="step-1">
            <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i> Step 1: Campaign Information</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Internal Campaign Title *</label>
                    <input type="text" name="title" id="camp_title" class="form-control" placeholder="e.g. Q4 Product Feature Announcement" required>
                    <div class="form-text">Used internally for reporting and audit logs.</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">Email Subject Line *</label>
                    <input type="text" name="subject" id="camp_subject" class="form-control" placeholder="e.g. Introducing our new features, {{first_name}}!" required>
                    <div class="form-text">Supports placeholders like <code>{{first_name}}</code>.</div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="goToStep(2)">
                    Next: Sender Configuration <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 2: SENDER CONFIGURATION -->
        <div class="wizard-step-content d-none" id="step-2">
            <h5 class="fw-bold mb-3"><i class="bi bi-envelope-at text-primary me-2"></i> Step 2: Sender & Routing Engine</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Sender Display Name *</label>
                    <input type="text" name="sender_name" id="camp_sender_name" class="form-control" value="<?= e(auth_user()['name'] ?? 'MailMoll Team') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Sender Email Address *</label>
                    <input type="email" name="sender_email" id="camp_sender_email" class="form-control" value="updates@mailmoll.local" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Reply-To Address (Optional)</label>
                    <input type="email" name="reply_to" class="form-control" placeholder="support@domain.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Routing Type *</label>
                    <select name="routing_type" id="camp_routing_type" class="form-select" onchange="toggleRoutingSelection()">
                        <option value="group" selected>SMTP Group (Balanced Rotation)</option>
                        <option value="single_smtp">Dedicated SMTP Account</option>
                    </select>
                </div>
                <div class="col-md-12" id="group_selection_div">
                    <label class="form-label small fw-semibold">Select SMTP Pool / Group *</label>
                    <select name="smtp_group_id" class="form-select">
                        <?php foreach ($smtpGroups as $grp): ?>
                            <option value="<?= $grp['id'] ?>"><?= e($grp['name']) ?> (Strategy: <?= strtoupper($grp['routing_strategy']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 d-none" id="single_smtp_selection_div">
                    <label class="form-label small fw-semibold">Select Dedicated SMTP Node *</label>
                    <select name="smtp_account_id" id="camp_smtp_account_id" class="form-select">
                        <?php foreach ($smtpAccounts as $acc): ?>
                            <option value="<?= $acc['id'] ?>"><?= e($acc['name']) ?> (Host: <?= e($acc['host']) ?> | Daily: <?= $acc['daily_limit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" onclick="goToStep(1)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="goToStep(3)">
                    Next: Audience Selection <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: AUDIENCE SELECTION -->
        <div class="wizard-step-content d-none" id="step-3">
            <h5 class="fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i> Step 3: Recipient Selection</h5>
            
            <div class="alert alert-info py-2 small mb-3">
                <i class="bi bi-shield-check me-1"></i> <strong>Compliance Policy:</strong> Only contacts who have explicitly opted in will be messaged. Suppressed and bounced emails are automatically excluded.
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Select Target Contact Lists *</label>
                <?php if (empty($contactLists)): ?>
                    <div class="text-danger small">
                        No contact lists available. <a href="<?= url('/contacts') ?>" target="_blank">Create a list</a> first.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($contactLists as $list): ?>
                            <div class="p-3 border rounded d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input target-list-checkbox" type="checkbox" name="target_lists[]" value="<?= $list['id'] ?>" id="list_chk_<?= $list['id'] ?>">
                                        <label class="form-check-label fw-semibold small" for="list_chk_<?= $list['id'] ?>">
                                            <?= e($list['name']) ?>
                                        </label>
                                    </div>
                                    <div class="text-muted ps-4" style="font-size: 11px;"><?= e($list['description'] ?: 'General subscribers') ?></div>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary small">
                                    <?= number_format($list['total_contacts']) ?> Opted-in Contacts
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" onclick="goToStep(2)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="goToStep(4)">
                    Next: Email Content <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 4: EMAIL CONTENT -->
        <div class="wizard-step-content d-none" id="step-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary me-2"></i> Step 4: Email Content & Placeholders</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Load From Existing Template</label>
                    <select id="templateLoaderSelect" class="form-select" onchange="loadSelectedTemplate()">
                        <option value="">-- Choose a reusable template or compose custom --</option>
                        <?php foreach ($templates as $tpl): ?>
                            <option value="<?= $tpl['id'] ?>" data-html="<?= htmlspecialchars($tpl['body_html']) ?>" data-subject="<?= htmlspecialchars($tpl['subject_default'] ?? '') ?>">
                                <?= e($tpl['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="previewCurrentStep4Email()">
                        <i class="bi bi-eye me-1"></i> Quick Preview in Modal
                    </button>
                </div>
            </div>

            <!-- Merge tags helper toolbar -->
            <div class="mb-2 d-flex flex-wrap align-items-center gap-1 p-2 bg-light rounded">
                <span class="small fw-semibold text-muted me-2"><i class="bi bi-tags-fill me-1 text-primary"></i> Merge Tags:</span>
                <button type="button" class="btn btn-white btn-sm border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertTag('{{first_name}}')">{{first_name}}</button>
                <button type="button" class="btn btn-white btn-sm border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertTag('{{last_name}}')">{{last_name}}</button>
                <button type="button" class="btn btn-white btn-sm border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertTag('{{email}}')">{{email}}</button>
                <button type="button" class="btn btn-white btn-sm border py-0 px-2" onmousedown="event.preventDefault()" onclick="insertTag('{{unsubscribe_url}}')">{{unsubscribe_url}}</button>
            </div>

            <!-- Editor Mode Tabs -->
            <ul class="nav nav-tabs mb-0" id="campaignEditorTabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active fw-semibold small" id="camp-visual-tab" data-bs-toggle="tab" data-bs-target="#camp-visual-pane" role="tab">
                        <i class="bi bi-palette-fill me-1 text-primary"></i> Visual Design Editor
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-semibold small" id="camp-code-tab" data-bs-toggle="tab" data-bs-target="#camp-code-pane" role="tab">
                        <i class="bi bi-code-slash me-1 text-primary"></i> HTML Source Code
                    </button>
                </li>
            </ul>

            <div class="tab-content border-start border-end border-bottom rounded-bottom mb-3" id="campEditorPanes">
                <!-- Visual Design Editor Mode -->
                <div class="tab-pane fade show active p-0" id="camp-visual-pane" role="tabpanel">
                    <!-- Rich Visual Toolbar -->
                    <div class="p-2 bg-light border-bottom d-flex flex-wrap align-items-center gap-1">
                        <select class="form-select form-select-sm d-inline-block w-auto" id="campFormatBlockSelect" onchange="execCampFormatBlock(this.value)">
                            <option value="p">Paragraph</option>
                            <option value="h1">Heading 1</option>
                            <option value="h2">Heading 2</option>
                            <option value="h3">Heading 3</option>
                            <option value="h4">Heading 4</option>
                        </select>

                        <div class="vr mx-1"></div>

                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('bold')" title="Bold"><i class="bi bi-type-bold"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('italic')" title="Italic"><i class="bi bi-type-italic"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('underline')" title="Underline"><i class="bi bi-type-underline"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('strikeThrough')" title="Strikethrough"><i class="bi bi-type-strikethrough"></i></button>

                        <div class="vr mx-1"></div>

                        <!-- Text Color -->
                        <div class="d-inline-flex align-items-center position-relative">
                            <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Text Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                <i class="bi bi-palette text-primary"></i>
                                <input type="color" value="#334155" oninput="restoreCampSelection(); execCampCmd('foreColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                            </label>
                        </div>

                        <!-- Highlight Color -->
                        <div class="d-inline-flex align-items-center position-relative">
                            <label class="btn btn-sm btn-white border m-0 d-flex align-items-center gap-1" title="Highlight Color" style="cursor: pointer;" onmousedown="event.preventDefault()">
                                <i class="bi bi-paint-bucket text-warning"></i>
                                <input type="color" value="#fef08a" oninput="restoreCampSelection(); execCampCmd('hiliteColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute; left: 0;">
                            </label>
                        </div>

                        <div class="vr mx-1"></div>

                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('justifyLeft')" title="Align Left"><i class="bi bi-text-left"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('justifyCenter')" title="Align Center"><i class="bi bi-text-center"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('justifyRight')" title="Align Right"><i class="bi bi-text-right"></i></button>

                        <div class="vr mx-1"></div>

                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>

                        <div class="vr mx-1"></div>

                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="insertCampLinkPrompt()" title="Insert Link"><i class="bi bi-link-45deg"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('unlink')" title="Remove Link"><i class="bi bi-link-45deg text-danger"></i></button>
                        <button type="button" class="btn btn-sm btn-white border" onmousedown="event.preventDefault()" onclick="execCampCmd('removeFormat')" title="Clear Formatting"><i class="bi bi-eraser"></i></button>
                    </div>

                    <!-- Visual Design Canvas -->
                    <div id="campaignVisualCanvas" contenteditable="true" style="min-height: 340px; max-height: 480px; overflow-y: auto; background: #f8fafc; padding: 20px; outline: none; border: 1px solid #e2e8f0;"></div>
                </div>

                <!-- HTML Source Mode -->
                <div class="tab-pane fade p-0" id="camp-code-pane" role="tabpanel">
                    <textarea id="campaignCodeMirrorTextarea"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" onclick="goToStep(3)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="goToStep(5)">
                    Next: Schedule & Timing <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 5: SCHEDULE -->
        <div class="wizard-step-content d-none" id="step-5">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-primary me-2"></i> Step 5: Dispatch Schedule</h5>
            
            <div class="mb-4">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="schedule_option" id="sched_now" value="now" checked onchange="toggleScheduleDate(false)">
                    <label class="form-check-label fw-semibold" for="sched_now">
                        Send Immediately
                    </label>
                    <div class="text-muted small">Begin queuing and dispatching emails as soon as confirmed.</div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="schedule_option" id="sched_later" value="later" onchange="toggleScheduleDate(true)">
                    <label class="form-check-label fw-semibold" for="sched_later">
                        Schedule for Later
                    </label>
                    <div class="text-muted small">Queue jobs to be released automatically at a future UTC timestamp.</div>
                </div>
            </div>

            <div class="mb-3 d-none" id="scheduleDateContainer">
                <label class="form-label small fw-semibold">Dispatch Date & Time *</label>
                <input type="text" name="schedule_time" id="schedule_time" class="form-control datetimepicker" placeholder="Select future date & time">
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" onclick="goToStep(4)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="goToStep(6)">
                    Next: Review & Confirm <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 6: REVIEW & CONFIRM -->
        <div class="wizard-step-content d-none" id="step-6">
            <h5 class="fw-bold mb-3"><i class="bi bi-check-circle text-primary me-2"></i> Step 6: Review & Confirm</h5>

            <div class="card p-3 bg-light mb-4 border-0">
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Title:</div>
                    <div class="col-sm-8 fw-semibold" id="rev_title">-</div>

                    <div class="col-sm-4 text-muted">Subject:</div>
                    <div class="col-sm-8 fw-semibold" id="rev_subject">-</div>

                    <div class="col-sm-4 text-muted">Sender:</div>
                    <div class="col-sm-8" id="rev_sender">-</div>

                    <div class="col-sm-4 text-muted">Routing:</div>
                    <div class="col-sm-8" id="rev_routing">-</div>

                    <div class="col-sm-4 text-muted">Schedule:</div>
                    <div class="col-sm-8 fw-semibold text-primary" id="rev_schedule">Immediate Dispatch</div>
                </div>
            </div>

            <!-- Email Real Design Preview in Step 6 -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0"><i class="bi bi-display text-primary me-1"></i> Real Design Preview</h6>
                    <span class="text-muted small">Rendered with personalized sample data</span>
                </div>
                <div class="border rounded bg-light p-2">
                    <iframe id="wizardReviewIframe" class="w-100 bg-white rounded border" style="height: 360px;"></iframe>
                </div>
            </div>

            <!-- Test Email Dispatcher Before Launch -->
            <div class="border p-3 rounded mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-send-check text-success me-1"></i> Send a Test Email First</h6>
                <p class="text-muted small mb-2">Verify email formatting and deliverability before broadcasting to subscribers.</p>
                <div class="input-group" style="max-width: 480px;">
                    <input type="email" id="testEmailInput" class="form-control" placeholder="your-test-email@domain.com">
                    <button type="button" class="btn btn-outline-primary" id="btnSendTest">
                        <i class="bi bi-send"></i> Send Test
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" onclick="goToStep(5)">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm px-4" onclick="submitWizard(false)">
                        Save as Draft
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-4" onclick="submitWizard(true)">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> Confirm & Launch Broadcast
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Quick Preview Modal for Step 4 -->
<div class="modal fade" id="quickPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-display text-primary me-2"></i> Real Design Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 bg-light">
                <iframe id="quickPreviewIframe" class="w-100 bg-white rounded border" style="height: 480px;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
let cmCampaignEditor;
let campActiveMode = 'visual';
let campSavedSelectionRange = null;

let campaignDocWrapper = {
    hasWrapper: false,
    prefix: '',
    suffix: ''
};

function parseCampaignHtmlForVisual(raw) {
    if (raw && raw.includes('<body') && raw.includes('</body>')) {
        const bodyMatch = raw.match(/<body[^>]*>/i);
        if (bodyMatch) {
            const startIdx = raw.indexOf(bodyMatch[0]) + bodyMatch[0].length;
            const endIdx = raw.lastIndexOf('</body>');
            campaignDocWrapper.hasWrapper = true;
            campaignDocWrapper.prefix = raw.substring(0, startIdx);
            campaignDocWrapper.suffix = raw.substring(endIdx);
            return raw.substring(startIdx, endIdx).trim();
        }
    }
    campaignDocWrapper.hasWrapper = false;
    campaignDocWrapper.prefix = '';
    campaignDocWrapper.suffix = '';
    return raw || '';
}

function assembleCampaignFullHtml(content) {
    if (campaignDocWrapper.hasWrapper && campaignDocWrapper.prefix) {
        return campaignDocWrapper.prefix + "\n" + content + "\n" + campaignDocWrapper.suffix;
    }
    return content;
}

function getCampaignFinalHtml() {
    if (campActiveMode === 'code' && cmCampaignEditor) {
        return cmCampaignEditor.getValue();
    }
    const canvas = document.getElementById('campaignVisualCanvas');
    return assembleCampaignFullHtml(canvas ? canvas.innerHTML : '');
}

function saveCampSelection() {
    const sel = window.getSelection();
    if (sel && sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        const canvas = document.getElementById('campaignVisualCanvas');
        if (canvas && canvas.contains(range.commonAncestorContainer)) {
            campSavedSelectionRange = range;
        }
    }
}

function restoreCampSelection() {
    if (campSavedSelectionRange) {
        const sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(campSavedSelectionRange);
    }
}

function execCampCmd(command, value = null) {
    const canvas = document.getElementById('campaignVisualCanvas');
    canvas.focus();
    restoreCampSelection();
    document.execCommand(command, false, value);
    saveCampSelection();
}

function execCampFormatBlock(tag) {
    execCampCmd('formatBlock', '<' + tag + '>');
}

function insertCampLinkPrompt() {
    const url = prompt('Enter destination URL:', 'https://');
    if (url) {
        execCampCmd('createLink', url);
    }
}

function insertTag(tag) {
    if (campActiveMode === 'code' && cmCampaignEditor) {
        const doc = cmCampaignEditor.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(tag, cursor);
    } else {
        const canvas = document.getElementById('campaignVisualCanvas');
        canvas.focus();
        restoreCampSelection();
        document.execCommand('insertText', false, tag);
        saveCampSelection();
    }
}

function loadSelectedTemplate() {
    const select = document.getElementById('templateLoaderSelect');
    const option = select.options[select.selectedIndex];
    if (option && option.dataset.html) {
        const rawHtml = option.dataset.html;
        const visualContent = parseCampaignHtmlForVisual(rawHtml);
        const canvas = document.getElementById('campaignVisualCanvas');
        if (canvas) {
            canvas.innerHTML = visualContent;
        }
        if (cmCampaignEditor) {
            cmCampaignEditor.setValue(rawHtml);
        }
        if (option.dataset.subject && !document.getElementById('camp_subject').value) {
            document.getElementById('camp_subject').value = option.dataset.subject;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const visualCanvas = document.getElementById('campaignVisualCanvas');
    const textarea = document.getElementById('campaignCodeMirrorTextarea');

    // Default template or sample content if empty
    const initialHtml = `<p>Hello <strong>{{first_name}}</strong>,</p><p>We are excited to share our latest news with you. Here is your personalized update.</p><p>Best regards,<br>MailMoll Team</p>`;
    if (visualCanvas) {
        visualCanvas.innerHTML = initialHtml;
    }

    if (textarea) {
        cmCampaignEditor = CodeMirror.fromTextArea(textarea, {
            mode: 'htmlmixed',
            theme: 'dracula',
            lineNumbers: true,
            lineWrapping: true
        });
        cmCampaignEditor.setValue(initialHtml);
    }

    // Tab Switching
    const visualTab = document.getElementById('camp-visual-tab');
    const codemirrorTab = document.getElementById('camp-code-tab');

    if (visualTab) {
        visualTab.addEventListener('shown.bs.tab', function() {
            campActiveMode = 'visual';
            if (cmCampaignEditor) {
                const rawCode = cmCampaignEditor.getValue();
                visualCanvas.innerHTML = parseCampaignHtmlForVisual(rawCode);
            }
        });
    }

    if (codemirrorTab) {
        codemirrorTab.addEventListener('shown.bs.tab', function() {
            campActiveMode = 'code';
            const fullHtml = assembleCampaignFullHtml(visualCanvas.innerHTML);
            if (cmCampaignEditor) {
                cmCampaignEditor.setValue(fullHtml);
                cmCampaignEditor.refresh();
            }
        });
    }

    if (visualCanvas) {
        visualCanvas.addEventListener('keyup', saveCampSelection);
        visualCanvas.addEventListener('mouseup', saveCampSelection);
        visualCanvas.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                e.preventDefault();
            }
            saveCampSelection();
        });
    }

    // Form submission safeguard
    const form = document.getElementById('campaignWizardForm');
    if (form) {
        form.addEventListener('submit', function() {
            document.getElementById('hidden_body_html').value = getCampaignFinalHtml();
        });
    }

    // Handle test send button
    document.getElementById('btnSendTest').addEventListener('click', function() {
        const testEmail = document.getElementById('testEmailInput').value;
        if (!testEmail) {
            Swal.fire('Error', 'Please enter a test recipient email address.', 'error');
            return;
        }

        const subject = document.getElementById('camp_subject').value || 'Test Campaign';
        const html = getCampaignFinalHtml();
        const smtpId = document.getElementById('camp_smtp_account_id')?.value || 0;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                'test_email': testEmail,
                'subject': subject,
                'body_html': html,
                'smtp_account_id': smtpId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
            } else {
                Swal.fire('Send Failed', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', err.message, 'error');
        });
    });
});

function previewCurrentStep4Email() {
    let html = getCampaignFinalHtml()
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsub');

    if (!html.toLowerCase().includes('<html')) {
        html = `<!DOCTYPE html><html><head><meta charset="utf-8"><base target="_blank"><style>body{margin:0;padding:20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background-color:#f8fafc;}</style></head><body>${html}</body></html>`;
    } else if (html.includes('<head>')) {
        html = html.replace('<head>', '<head><base target="_blank">');
    } else {
        html = '<base target="_blank">' + html;
    }

    const iframe = document.getElementById('quickPreviewIframe');
    if (iframe) {
        iframe.srcdoc = html;
    }
    const modal = new bootstrap.Modal(document.getElementById('quickPreviewModal'));
    modal.show();
}

function goToStep(step) {
    if (step > currentStep) {
        // Validate current step
        if (currentStep === 1) {
            if (!document.getElementById('camp_title').value || !document.getElementById('camp_subject').value) {
                Swal.fire('Required Fields', 'Please complete title and subject.', 'warning');
                return;
            }
        }
        if (currentStep === 3) {
            const checked = document.querySelectorAll('.target-list-checkbox:checked');
            if (checked.length === 0) {
                Swal.fire('Selection Required', 'Please select at least one contact list.', 'warning');
                return;
            }
        }
    }

    // Hide old step
    document.getElementById('step-' + currentStep).classList.add('d-none');
    document.getElementById('step-nav-' + currentStep)?.classList.remove('active');

    // Show new step
    currentStep = step;
    document.getElementById('step-' + currentStep).classList.remove('d-none');
    document.getElementById('step-nav-' + currentStep)?.classList.add('active');

    if (currentStep === 6) {
        populateReview();
    }
}

function toggleRoutingSelection() {
    const type = document.getElementById('camp_routing_type').value;
    if (type === 'group') {
        document.getElementById('group_selection_div').classList.remove('d-none');
        document.getElementById('single_smtp_selection_div').classList.add('d-none');
    } else {
        document.getElementById('group_selection_div').classList.add('d-none');
        document.getElementById('single_smtp_selection_div').classList.remove('d-none');
    }
}

function toggleScheduleDate(show) {
    const container = document.getElementById('scheduleDateContainer');
    if (show) {
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}

function populateReview() {
    document.getElementById('rev_title').textContent = document.getElementById('camp_title').value;
    document.getElementById('rev_subject').textContent = document.getElementById('camp_subject').value;
    document.getElementById('rev_sender').textContent = document.getElementById('camp_sender_name').value + ' <' + document.getElementById('camp_sender_email').value + '>';
    document.getElementById('rev_routing').textContent = document.getElementById('camp_routing_type').value === 'group' ? 'SMTP Group Rotation' : 'Single SMTP Node';

    const schedLater = document.getElementById('sched_later').checked;
    if (schedLater) {
        document.getElementById('rev_schedule').textContent = 'Scheduled for: ' + (document.getElementById('schedule_time').value || 'Later');
    } else {
        document.getElementById('rev_schedule').textContent = 'Immediate Queue & Dispatch';
    }

    let reviewHtml = getCampaignFinalHtml()
        .replace(/\{\{first_name\}\}/g, 'Sarah')
        .replace(/\{\{last_name\}\}/g, 'Connor')
        .replace(/\{\{email\}\}/g, 'sarah.connor@cyberdyne.org')
        .replace(/\{\{unsubscribe_url\}\}/g, '#preview-unsub');

    if (!reviewHtml.toLowerCase().includes('<html')) {
        reviewHtml = `<!DOCTYPE html><html><head><meta charset="utf-8"><base target="_blank"><style>body{margin:0;padding:20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background-color:#f8fafc;}</style></head><body>${reviewHtml}</body></html>`;
    } else if (reviewHtml.includes('<head>')) {
        reviewHtml = reviewHtml.replace('<head>', '<head><base target="_blank">');
    } else {
        reviewHtml = '<base target="_blank">' + reviewHtml;
    }

    const reviewFrame = document.getElementById('wizardReviewIframe');
    if (reviewFrame) {
        reviewFrame.srcdoc = reviewHtml;
    }
}

function submitWizard(launchNow) {
    const finalHtml = getCampaignFinalHtml();
    document.getElementById('hidden_body_html').value = finalHtml;
    document.getElementById('hidden_launch_immediately').value = launchNow ? '1' : '0';
    document.getElementById('campaignWizardForm').submit();
}
</script>
