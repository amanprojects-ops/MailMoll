<!-- HERO SECTION -->
<section class="hero-section text-center">
    <div class="container">
        <!-- Top Pill -->
        <div class="hero-pill shadow-xs">
            <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size: 10px;">NEW</span>
            <span>MailFlow 2.0 &bull; Self-Hosted Enterprise Multi-SMTP Engine</span>
            <i class="bi bi-chevron-right small"></i>
        </div>

        <!-- Headline -->
        <h1 class="hero-title">
            One Platform.<br>
            <span class="gradient-text">Smarter Email Infrastructure.</span>
        </h1>

        <!-- Subheading -->
        <p class="hero-subheading">
            Manage your authorized SMTP connections, organize campaigns, monitor email delivery and streamline communication from one powerful dashboard.
        </p>

        <!-- CTA Buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= url('/register') ?>" class="btn btn-primary btn-lg px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow">
                <span>Get Started Free</span>
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="<?= url('/features') ?>" class="btn btn-white btn-lg px-4 py-2 fw-semibold d-flex align-items-center gap-2 border shadow-xs">
                <i class="bi bi-play-circle text-primary"></i>
                <span>Explore Features</span>
            </a>
        </div>

        <!-- Realistic Dashboard Hero Preview -->
        <div class="hero-preview-wrapper mt-5">
            <div class="hero-preview-inner text-start p-3 p-md-4">
                <!-- Browser Chrome Bar -->
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-light-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-danger bg-opacity-75 d-inline-block" style="width: 11px; height: 11px;"></span>
                        <span class="rounded-circle bg-warning bg-opacity-75 d-inline-block" style="width: 11px; height: 11px;"></span>
                        <span class="rounded-circle bg-success bg-opacity-75 d-inline-block" style="width: 11px; height: 11px;"></span>
                        <span class="text-muted small ms-2 font-monospace" style="font-size: 11px;"><i class="bi bi-lock-fill text-success me-1"></i> mailflow.local/dashboard</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" style="font-size: 11px;">
                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span> Live Dispatch Worker
                        </span>
                    </div>
                </div>

                <!-- Preview Metric Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="p-3 rounded border bg-light-subtle">
                            <div class="text-muted small fw-semibold text-uppercase">Active SMTP Nodes</div>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h3 class="fw-bold mb-0 text-dark">4 / 4</h3>
                                <span class="badge bg-success bg-opacity-10 text-success small">100% Online</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;"><i class="bi bi-shield-check text-success me-1"></i> Round-Robin Rotation</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="p-3 rounded border bg-light-subtle">
                            <div class="text-muted small fw-semibold text-uppercase">Delivery Acceptance</div>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h3 class="fw-bold mb-0 text-primary">99.8%</h3>
                                <span class="text-success small"><i class="bi bi-graph-up-arrow"></i> +0.4%</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">14,820 messages accepted</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="p-3 rounded border bg-light-subtle">
                            <div class="text-muted small fw-semibold text-uppercase">Queue In-Flight</div>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h3 class="fw-bold mb-0 text-info">12</h3>
                                <span class="badge bg-info bg-opacity-10 text-info small">Processing</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Next dispatch in 3s</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="p-3 rounded border bg-light-subtle">
                            <div class="text-muted small fw-semibold text-uppercase">Bounce & Suppression</div>
                            <div class="d-flex align-items-baseline gap-2 mt-1">
                                <h3 class="fw-bold mb-0 text-success">0.02%</h3>
                                <span class="badge bg-light text-secondary border small">RFC 8058</span>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">Auto-filtered & clean</div>
                        </div>
                    </div>
                </div>

                <!-- Preview SMTP Health Table Mockup -->
                <div class="rounded border overflow-hidden">
                    <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="small fw-bold text-dark"><i class="bi bi-server text-primary me-2"></i> Authorized SMTP Nodes & Quota Pools</span>
                        <span class="small text-muted font-monospace" style="font-size: 11px;">Last synchronized: Just now</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" style="font-size: 12px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Server Host</th>
                                    <th>Security</th>
                                    <th>Hourly Quota</th>
                                    <th>Daily Usage</th>
                                    <th>Status</th>
                                    <th class="text-end">Health</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-semibold"><i class="bi bi-hdd-network text-primary me-1"></i> smtp.sendgrid.net</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Port 587 / TLS</span></td>
                                    <td>32 / 200 msg</td>
                                    <td>
                                        <div class="progress" style="height: 5px; width: 100px;">
                                            <div class="progress-bar bg-primary" style="width: 28%;"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Connected</span></td>
                                    <td class="text-end text-success fw-bold"><i class="bi bi-heart-pulse-fill me-1"></i> 100%</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="bi bi-hdd-network text-primary me-1"></i> email-smtp.us-east-1.amazonaws.com</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Port 465 / SSL</span></td>
                                    <td>84 / 500 msg</td>
                                    <td>
                                        <div class="progress" style="height: 5px; width: 100px;">
                                            <div class="progress-bar bg-success" style="width: 42%;"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Connected</span></td>
                                    <td class="text-end text-success fw-bold"><i class="bi bi-heart-pulse-fill me-1"></i> 100%</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold"><i class="bi bi-hdd-network text-primary me-1"></i> smtp.earnbro.site</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Port 465 / SSL</span></td>
                                    <td>15 / 100 msg</td>
                                    <td>
                                        <div class="progress" style="height: 5px; width: 100px;">
                                            <div class="progress-bar bg-info" style="width: 15%;"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Connected</span></td>
                                    <td class="text-end text-success fw-bold"><i class="bi bi-heart-pulse-fill me-1"></i> 100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TRUST SECTION -->
<section class="trust-section">
    <div class="container">
        <div class="row g-4 text-center justify-content-center">
            <div class="col-md-3 col-6">
                <div class="trust-item justify-content-center">
                    <div class="trust-icon"><i class="bi bi-hdd-rack"></i></div>
                    <div>100% Self-Hosted Architecture</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="trust-item justify-content-center">
                    <div class="trust-icon"><i class="bi bi-shield-lock"></i></div>
                    <div>Privacy-First & GDPR Ready</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="trust-item justify-content-center">
                    <div class="trust-icon"><i class="bi bi-code-slash"></i></div>
                    <div>Open-Source Foundation</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="trust-item justify-content-center">
                    <div class="trust-icon"><i class="bi bi-safe"></i></div>
                    <div>AES-256 Encrypted SMTP Vault</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 12 ENTERPRISE FEATURE PILLARS -->
<section class="py-6 py-lg-7" id="features">
    <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-1 mb-2">COMPLETE FEATURE SUITE</span>
            <h2 class="fw-bold tracking-tight mb-3">Engineered for Reliable, High-Volume Delivery</h2>
            <p class="text-muted">Everything you need to orchestrate multi-provider email infrastructure, manage campaigns, and protect your domain reputation.</p>
        </div>

        <div class="row g-4">
            <!-- 1. Multi-SMTP Management -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-primary">
                        <i class="bi bi-server"></i>
                    </div>
                    <h5 class="fw-bold mb-2">1. Multi-SMTP Management</h5>
                    <p class="text-muted small mb-0">
                        Connect and authenticate unlimited SMTP accounts across Google, SendGrid, Amazon SES, Brevo, Postmark, and private dedicated relays.
                    </p>
                </div>
            </div>

            <!-- 2. SMTP Health Monitoring -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-success">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <h5 class="fw-bold mb-2">2. SMTP Health Monitoring</h5>
                    <p class="text-muted small mb-0">
                        Live connection heartbeat testing, handshake latency measurements, and automatic alert tracking on repeated authentication failures.
                    </p>
                </div>
            </div>

            <!-- 3. Smart Routing & Quotas -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-warning">
                        <i class="bi bi-shuffle"></i>
                    </div>
                    <h5 class="fw-bold mb-2">3. Routing & Quota Engine</h5>
                    <p class="text-muted small mb-0">
                        Group relays into rotation pools with Round-Robin, Weighted Distribution, Least-Used, or Priority strategies while respecting hourly and daily limits.
                    </p>
                </div>
            </div>

            <!-- 4. Campaign Management -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-info">
                        <i class="bi bi-send-check"></i>
                    </div>
                    <h5 class="fw-bold mb-2">4. Campaign Management</h5>
                    <p class="text-muted small mb-0">
                        Create, schedule, draft-edit, pause, and resume personalized broadcasts. Complete visibility into queued, sent, and failed recipients.
                    </p>
                </div>
            </div>

            <!-- 5. HTML & Visual Templates -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-primary">
                        <i class="bi bi-palette"></i>
                    </div>
                    <h5 class="fw-bold mb-2">5. Visual & Code Template Editor</h5>
                    <p class="text-muted small mb-0">
                        Built-in isolated iframe visual editor and CodeMirror HTML source code view. Instant mobile/desktop preview and one-click merge tags.
                    </p>
                </div>
            </div>

            <!-- 6. Multi-Format Contact Lists -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-success">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5 class="fw-bold mb-2">6. Contact Lists & Importer</h5>
                    <p class="text-muted small mb-0">
                        Multi-format contact importer supporting <code>.csv</code>, <code>.xlsx</code>, <code>.json</code>, and <code>.txt</code> with sample downloads and dedicated column filtering.
                    </p>
                </div>
            </div>

            <!-- 7. Email Queue Processing -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h5 class="fw-bold mb-2">7. High-Throughput Queue</h5>
                    <p class="text-muted small mb-0">
                        Atomic SQLite queue processing with transaction locks, intelligent exponential retry backoff, and zero risk of duplicate dispatch.
                    </p>
                </div>
            </div>

            <!-- 8. Real-time Delivery Logs -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-info">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h5 class="fw-bold mb-2">8. Real-time Delivery Logs</h5>
                    <p class="text-muted small mb-0">
                        Full audit trail of every SMTP transmission attempt, server handshake duration, response codes, and recipient delivery events.
                    </p>
                </div>
            </div>

            <!-- 9. Bounce & Suppression Management -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-danger">
                        <i class="bi bi-shield-slash"></i>
                    </div>
                    <h5 class="fw-bold mb-2">9. Bounce & Suppression Vault</h5>
                    <p class="text-muted small mb-0">
                        RFC 8058 compliant one-click unsubscribe headers and automated suppression lists protecting your sender score from hard bounces.
                    </p>
                </div>
            </div>

            <!-- 10. Campaign Analytics -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-primary">
                        <i class="bi bi-bar-chart-line"></i>
                    </div>
                    <h5 class="fw-bold mb-2">10. Deep Campaign Analytics</h5>
                    <p class="text-muted small mb-0">
                        Real-time delivery progress bars, acceptance rates, hourly throughput charts, and provider reliability comparisons.
                    </p>
                </div>
            </div>

            <!-- 11. REST API -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-success">
                        <i class="bi bi-code-square"></i>
                    </div>
                    <h5 class="fw-bold mb-2">11. Developer-Friendly REST API</h5>
                    <p class="text-muted small mb-0">
                        Trigger test messages, launch batch queues, query delivery statuses, and programmatically feed contacts via clean JSON endpoints.
                    </p>
                </div>
            </div>

            <!-- 12. Self-Hosted Privacy -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon-box icon-box-warning">
                        <i class="bi bi-lock"></i>
                    </div>
                    <h5 class="fw-bold mb-2">12. Self-Hosted Infrastructure</h5>
                    <p class="text-muted small mb-0">
                        Deploy on any LAMP, cPanel, or VPS environment. Zero third-party telemetry, SQLite database portability, and zero Node.js runtime bloat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS SECTION -->
<section class="py-6 bg-light" id="how-it-works">
    <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-1 mb-2">SIMPLE 4-STEP WORKFLOW</span>
            <h2 class="fw-bold tracking-tight mb-3">From Connection to Delivery in Minutes</h2>
            <p class="text-muted">A clear, organized process to manage and send emails with enterprise deliverability standards.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="p-4 bg-white rounded border h-100 shadow-xs">
                    <div class="step-number-badge">1</div>
                    <h5 class="fw-bold">Connect SMTP Nodes</h5>
                    <p class="text-muted small mb-0">Add authorized SMTP credentials individually or bulk import via CSV, XLSX, or JSON with AES-256 encryption.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded border h-100 shadow-xs">
                    <div class="step-number-badge">2</div>
                    <h5 class="fw-bold">Organize Audience</h5>
                    <p class="text-muted small mb-0">Import opted-in subscriber lists and craft branded email templates using our visual WYSIWYG editor.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded border h-100 shadow-xs">
                    <div class="step-number-badge">3</div>
                    <h5 class="fw-bold">Launch Campaigns</h5>
                    <p class="text-muted small mb-0">Configure your routing pool, select delivery speed, preview rendered HTML, and dispatch or schedule with confidence.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-4 bg-white rounded border h-100 shadow-xs">
                    <div class="step-number-badge">4</div>
                    <h5 class="fw-bold">Monitor Events</h5>
                    <p class="text-muted small mb-0">Track real-time queue workers, delivery acceptance, hourly quota consumption, and suppression lists.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURE COMPARISON TABLE -->
<section class="py-6">
    <div class="container">
        <div class="text-center max-w-2xl mx-auto mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-1 mb-2">WHY SELF-HOSTED</span>
            <h2 class="fw-bold tracking-tight mb-3">MailFlow vs Traditional Cloud SaaS</h2>
            <p class="text-muted">See why engineering-led teams choose self-hosted email orchestration over costly per-contact subscriptions.</p>
        </div>

        <div class="comparison-table-wrapper">
            <table class="table comparison-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40%;">Capability / Feature</th>
                        <th style="width: 30%;" class="text-primary fw-bold">MailFlow (Self-Hosted)</th>
                        <th style="width: 30%;">Traditional Cloud Email SaaS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-semibold">Contact & Subscriber Limits</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Unlimited (Your hardware)</td>
                        <td class="text-danger"><i class="bi bi-x-circle me-1"></i> Expensive tiered tiers</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Multi-SMTP Load Balancing</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> 4 Routing Strategies included</td>
                        <td class="text-danger"><i class="bi bi-x-circle me-1"></i> Vendor locked to 1 engine</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Data Privacy & Sovereignty</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> 100% on your server</td>
                        <td class="text-danger"><i class="bi bi-x-circle me-1"></i> Shared cloud & tracking</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Credential Security</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> AES-256 vault encryption</td>
                        <td class="text-muted">Encrypted in cloud database</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Import Formats</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> CSV, XLSX, JSON, TXT</td>
                        <td class="text-muted">CSV only</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Runtime Requirements</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> PHP 8.2 + SQLite (Zero Node.js)</td>
                        <td class="text-muted">Proprietary hosted stacks</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">One-Click RFC 8058 Unsubscribe</td>
                        <td class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Automated header injection</td>
                        <td class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Included</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ SECTION -->
<section class="py-6 bg-light" id="faq">
    <div class="container max-w-3xl">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-1 mb-2">HAVE QUESTIONS?</span>
            <h2 class="fw-bold tracking-tight mb-3">Frequently Asked Questions</h2>
            <p class="text-muted">Common questions about self-hosting, SMTP routing, and compliance.</p>
        </div>

        <div class="accordion faq-accordion" id="marketingFaqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Can I use MailFlow with any SMTP server?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#marketingFaqAccordion">
                    <div class="accordion-body text-muted small">
                        Yes. MailFlow works with any standard RFC-compliant SMTP server, including SendGrid, Amazon SES, Google Workspace, Mailgun, Brevo, Postmark, and private dedicated relays. It supports TLS, SSL (port 465), STARTTLS (port 587), and multiple authentication types (LOGIN, PLAIN).
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How does the multi-SMTP routing work?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#marketingFaqAccordion">
                    <div class="accordion-body text-muted small">
                        You can create SMTP Routing Groups and choose from four strategies: Round-Robin (equal distribution), Weighted (proportionate to weight), Least-Used (prioritizing servers with lowest quota usage), or Priority-Based (fails over to backup relays). Every node strictly adheres to its configured daily and hourly limits.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Do I need Node.js or Redis to run the queue?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#marketingFaqAccordion">
                    <div class="accordion-body text-muted small">
                        No. MailFlow is built on pure PHP 8.2+ and SQLite 3 with WAL mode. Queue processing uses atomic job locking and transactions, so you can execute the queue worker via cron or right from the web dashboard with zero Node.js or Redis memory overhead.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Is my subscriber data safe and private?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#marketingFaqAccordion">
                    <div class="accordion-body text-muted small">
                        100%. Because MailFlow is self-hosted on your own server, your contacts, campaign logs, and passwords never touch third-party servers. All SMTP credentials are encrypted at rest using AES-256.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                        What contact import formats are supported?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#marketingFaqAccordion">
                    <div class="accordion-body text-muted small">
                        MailFlow supports <code>.csv</code> (with auto-delimiter and BOM detection), native Microsoft Excel <code>.xlsx</code>, <code>.json</code> arrays, and plain <code>.txt</code> lists. Ready-to-use sample templates can be downloaded directly from the importer modal.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEWSLETTER WITH EXPLICIT CONSENT -->
<section class="py-6 border-top" id="newsletter">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold rounded-pill px-3 py-1 mb-2">STAY UPDATED</span>
                <h3 class="fw-bold mb-2">Product Updates & Release Notes</h3>
                <p class="text-muted small mb-4">Subscribe to our newsletter for the latest security patches, architectural updates, and deliverability insights.</p>

                <form action="<?= url('/newsletter/subscribe') ?>" method="POST" class="text-start">
                    <?= csrf_field() ?>
                    <div class="input-group mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Enter your business email" required>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Subscribe</button>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="consent" id="consentCheck" required>
                        <label class="form-check-label text-muted" for="consentCheck" style="font-size: 11px;">
                            I consent to receive release notes and product updates from MailFlow in compliance with GDPR. Unsubscribe anytime.
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FINAL HIGH-CONVERTING CTA -->
<section class="py-6">
    <div class="container">
        <div class="cta-banner text-center">
            <h2 class="fw-bold mb-3">Take Full Control of Your Email Delivery</h2>
            <p class="mb-4 mx-auto opacity-90" style="max-width: 600px;">
                Deploy MailFlow on your infrastructure today. Manage multiple SMTP providers with zero subscription lock-in and enterprise reliability.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="<?= url('/register') ?>" class="btn btn-white btn-lg px-4 py-2 fw-bold text-primary shadow">
                    Create Free Account <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <a href="<?= url('/docs') ?>" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                    Read Documentation
                </a>
            </div>
        </div>
    </div>
</section>
