<?php
/**
 * MailFlow - Features Page
 * Complete showcase of MailFlow multi-SMTP email management SaaS capabilities
 */
?>

<!-- Features Hero Section -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-stars me-1"></i> Enterprise Capabilities
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-4">
                    Engineered for High-Deliverability, Scalability & Full Control
                </h1>
                <p class="lead text-secondary mb-4 mx-auto" style="max-width: 720px;">
                    Explore the full suite of MailFlow features designed to eliminate single points of failure in your email infrastructure, maintain sender reputation, and scale your communications effortlessly.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-primary btn-lg px-4 py-2 fw-semibold">
                        Deploy Your Instance <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="<?= \App\Helpers\UrlHelper::to('/docs') ?>" class="btn btn-outline-secondary btn-lg px-4 py-2">
                        Read Technical Docs
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Deep Dive Feature 1: Multi-SMTP & Failover -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="d-inline-flex p-3 rounded-4 bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="bi bi-hdd-network fs-2"></i>
                </div>
                <h2 class="h1 fw-bold text-light mb-3">Multi-SMTP Aggregation & Dynamic Failover</h2>
                <p class="text-secondary lead fs-6 mb-4">
                    Eliminate reliance on a single transactional provider. Connect Amazon SES, Mailgun, Postmark, SendGrid, Brevo, or private custom postfix servers into unified sending groups with zero code changes.
                </p>
                <ul class="list-unstyled text-secondary d-flex flex-column gap-3 mb-4">
                    <li class="d-flex align-items-start gap-3">
                        <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0 mt-1"></i>
                        <div><strong class="text-light">Round-Robin & Weighted Routing:</strong> Distribute traffic mathematically across nodes based on defined percentage allocations.</div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0 mt-1"></i>
                        <div><strong class="text-light">Automated Health Probing:</strong> Real-time socket and auth diagnostics instantly downgrade impaired hosts and route via healthy backups.</div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <i class="bi bi-check2-circle text-success fs-5 flex-shrink-0 mt-1"></i>
                        <div><strong class="text-light">AES-256-GCM Encryption:</strong> Sensitive passwords and API credentials encrypted at rest using military-grade authenticated ciphers.</div>
                    </li>
                </ul>
                <div class="p-3 rounded-3 bg-dark border border-secondary-subtle">
                    <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                        <span class="badge bg-success-subtle text-success">Algorithm Demo</span>
                        <span>Routing Rules: Active Fallback</span>
                    </div>
                    <code class="text-info small">Rule #1: Priority SES (weight: 70) &rarr; Failover: Mailgun (weight: 30) &rarr; Backup: Self-Hosted Postfix</code>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle shadow-2-strong p-3">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-2">
                        <span class="text-light fw-bold"><i class="bi bi-layers me-2 text-primary"></i>SMTP Routing Pool</span>
                        <span class="badge bg-success-subtle text-success"><i class="bi bi-activity me-1"></i>100% Operational</span>
                    </div>
                    <div class="card-body p-2 d-flex flex-column gap-3">
                        <div class="p-3 rounded bg-body-tertiary border border-secondary-subtle d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-primary text-white p-2 rounded-3"><i class="bi bi-cloud-check fs-5"></i></div>
                                <div>
                                    <h6 class="mb-0 text-light">Amazon SES US-East</h6>
                                    <small class="text-secondary">email-smtp.us-east-1.amazonaws.com:587</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">Weight: 60%</span>
                                <div class="text-success small mt-1"><i class="bi bi-check-circle me-1"></i>Active</div>
                            </div>
                        </div>
                        <div class="p-3 rounded bg-body-tertiary border border-secondary-subtle d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-info text-white p-2 rounded-3"><i class="bi bi-send-check fs-5"></i></div>
                                <div>
                                    <h6 class="mb-0 text-light">Postmark Transactional</h6>
                                    <small class="text-secondary">smtp.postmarkapp.com:587</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">Weight: 30%</span>
                                <div class="text-success small mt-1"><i class="bi bi-check-circle me-1"></i>Active</div>
                            </div>
                        </div>
                        <div class="p-3 rounded bg-body-tertiary border border-secondary-subtle d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-warning text-dark p-2 rounded-3"><i class="bi bi-server fs-5"></i></div>
                                <div>
                                    <h6 class="mb-0 text-light">Internal Postfix Relay</h6>
                                    <small class="text-secondary">mail.internal.corp:465 (SSL)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">Weight: 10%</span>
                                <div class="text-success small mt-1"><i class="bi bi-shield-check me-1"></i>Standby</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Deep Dive Feature 2: High Throughput Queue Engine -->
<section class="py-5 bg-body-tertiary">
    <div class="container py-4">
        <div class="row align-items-center g-5 flex-lg-row-reverse">
            <div class="col-lg-6">
                <div class="d-inline-flex p-3 rounded-4 bg-success bg-opacity-10 text-success mb-3">
                    <i class="bi bi-cpu fs-2"></i>
                </div>
                <h2 class="h1 fw-bold text-light mb-3">Atomic Queue Engine & Rate Enforcers</h2>
                <p class="text-secondary lead fs-6 mb-4">
                    Send high-volume campaigns without overwhelming server RAM or triggering provider rate limit bans. Our transactional SQLite lock-free worker architecture processes queues reliably.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <i class="bi bi-speedometer2 text-warning fs-3 mb-2 d-block"></i>
                            <h6 class="text-light fw-bold">Hourly & Daily Caps</h6>
                            <p class="text-secondary small mb-0">Set strict sending throttles per SMTP host to stay well inside provider tier limits.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle h-100">
                            <i class="bi bi-arrow-repeat text-info fs-3 mb-2 d-block"></i>
                            <h6 class="text-light fw-bold">Intelligent Retries</h6>
                            <p class="text-secondary small mb-0">Exponential backoff for transient 4xx network glitches with maximum retry guards.</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-emerald text-light px-3 py-2 rounded-pill"><i class="bi bi-lightning-charge me-1"></i>Zero Duplicate Guarantee</span>
                    <span class="text-secondary small">Atomic PDO transactions lock rows before dispatch.</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle shadow-2-strong p-3">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pb-2">
                        <span class="text-light fw-bold"><i class="bi bi-terminal me-2 text-success"></i>CLI Queue Worker</span>
                        <span class="badge bg-primary">v2.4.0 High-Concurrency</span>
                    </div>
                    <div class="card-body bg-black rounded font-monospace p-3 text-start small">
                        <div class="text-secondary">$ php cron/email-worker.php --batch=250 --daemon</div>
                        <div class="text-muted mt-2">[2026-11-02 21:20:01] Worker thread initialized (PID: 18492)</div>
                        <div class="text-muted">[2026-11-02 21:20:02] Claiming 250 pending queue items with atomic lock...</div>
                        <div class="text-info">[2026-11-02 21:20:03] Dispatched to SES-East (msg_id: #94012) &rarr; OK (42ms)</div>
                        <div class="text-info">[2026-11-02 21:20:04] Dispatched to Postmark (msg_id: #94013) &rarr; OK (55ms)</div>
                        <div class="text-warning">[2026-11-02 21:20:05] Throttled Gmail SMTP: Hourly quota 95% reached</div>
                        <div class="text-success">[2026-11-02 21:20:06] Re-routed remaining 45 items to SES fallback pool</div>
                        <div class="text-light mt-2">[STATUS] Batch complete: 250 sent, 0 failed, Memory: 14.2 MB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 12 Core Pillars Grid -->
<section class="py-5">
    <div class="container py-lg-5">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold mb-3">All-In-One Toolkit</span>
            <h2 class="display-5 fw-extrabold text-light mb-3">Everything Required to Run Enterprise Email</h2>
            <p class="text-secondary">MailFlow bridges the gap between raw SMTP relays and full-featured email automation software.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-hdd-stack text-primary"></i></div>
                    <h5 class="fw-bold text-light mb-2">1. Multi-SMTP Aggregation</h5>
                    <p class="text-secondary small mb-0">Add unlimited SMTP servers across AWS, Mailgun, Postmark, Google Workspace, or on-premise postfix instances.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-heart-pulse text-danger"></i></div>
                    <h5 class="fw-bold text-light mb-2">2. Live Health Telemetry</h5>
                    <p class="text-secondary small mb-0">Automated socket connection probes and authentication handshakes continuously measure latency and uptime.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-diagram-3 text-info"></i></div>
                    <h5 class="fw-bold text-light mb-2">3. Smart Routing & Quotas</h5>
                    <p class="text-secondary small mb-0">Round-robin, weighted, and priority routing rules with strict hourly and daily rate limiting per server.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-envelope-paper text-warning"></i></div>
                    <h5 class="fw-bold text-light mb-2">4. Campaign Orchestrator</h5>
                    <p class="text-secondary small mb-0">Craft, test, schedule, pause, and resume mass broadcast campaigns with plain-text fallback generation.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-code-slash text-success"></i></div>
                    <h5 class="fw-bold text-light mb-2">5. Dual-Mode Template Builder</h5>
                    <p class="text-secondary small mb-0">Visual WYSIWYG editor alongside CodeMirror raw HTML mode, dynamic tags, and mobile preview iframe.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-people text-primary"></i></div>
                    <h5 class="fw-bold text-light mb-2">6. High-Capacity Contact Lists</h5>
                    <p class="text-secondary small mb-0">Manage segmented audiences with multi-extension bulk import (.csv, .xlsx, .json, .txt) and deduplication.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-cpu text-info"></i></div>
                    <h5 class="fw-bold text-light mb-2">7. High-Throughput Queue</h5>
                    <p class="text-secondary small mb-0">SQLite transaction-based job queue capable of processing tens of thousands of messages with zero memory leaks.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-journal-text text-light"></i></div>
                    <h5 class="fw-bold text-light mb-2">8. Detailed Audit & Delivery Logs</h5>
                    <p class="text-secondary small mb-0">Inspect raw SMTP handshake transcripts, recipient responses, timestamps, and error codes for every email.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-shield-x text-danger"></i></div>
                    <h5 class="fw-bold text-light mb-2">9. Automated Bounce & Suppression</h5>
                    <p class="text-secondary small mb-0">Automatic suppression tables protect your sender score by blacklisting hard bounces and unsubscribed recipients.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-bar-chart-line text-success"></i></div>
                    <h5 class="fw-bold text-light mb-2">10. Deep Deliverability Analytics</h5>
                    <p class="text-secondary small mb-0">Track delivery acceptance, bounce ratios, opens, and clicks grouped by campaign, SMTP host, and domain.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-key text-warning"></i></div>
                    <h5 class="fw-bold text-light mb-2">11. REST API & Webhooks</h5>
                    <p class="text-secondary small mb-0">Scoped API keys allow your microservices, web apps, and mobile backends to enqueue transactional mail instantly.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card h-100 p-4">
                    <div class="feature-icon mb-3"><i class="bi bi-shield-lock text-primary"></i></div>
                    <h5 class="fw-bold text-light mb-2">12. 100% Self-Hosted & Private</h5>
                    <p class="text-secondary small mb-0">Run completely on your own hardware or cPanel server. No external data telemetry, 100% GDPR compliant.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-5 bg-primary bg-opacity-10 border-top border-bottom border-primary-subtle text-center">
    <div class="container py-4">
        <h2 class="display-6 fw-bold text-light mb-3">Ready to upgrade your email infrastructure?</h2>
        <p class="text-secondary lead fs-6 mb-4 max-w-600 mx-auto">Get up and running in under five minutes with zero external SaaS dependencies.</p>
        <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-primary btn-lg px-4 fw-semibold">
            Get Started with MailFlow Free <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</section>
