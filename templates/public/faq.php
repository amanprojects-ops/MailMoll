<?php
/**
 * MailFlow - FAQ Page
 * Comprehensive questions and answers regarding infrastructure, deliverability, and licensing
 */
?>

<!-- FAQ Hero -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-4 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-question-circle me-1"></i> Knowledge Base
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-3">
                    Frequently Asked Questions
                </h1>
                <p class="lead text-secondary mb-0">
                    Everything you need to know about self-hosting MailFlow, connecting SMTP relays, campaign management, and pricing.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion Sections -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                <!-- Category 1: General & Architecture -->
                <div class="mb-5">
                    <h3 class="h4 fw-bold text-light mb-3"><i class="bi bi-cpu text-primary me-2"></i>General & Architecture</h3>
                    <div class="accordion accordion-flush" id="faqGeneral">
                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#gfaq1">
                                    What exactly is MailFlow?
                                </button>
                            </h2>
                            <div id="gfaq1" class="accordion-collapse collapse" data-bs-parent="#faqGeneral">
                                <div class="accordion-body text-secondary">
                                    MailFlow is a modern, self-hosted multi-SMTP email management SaaS application. It acts as an intelligent abstraction layer between your email campaigns/transactional messages and your underlying SMTP relays (Amazon SES, Mailgun, Postmark, Google Workspace, private postfix servers). It provides contact management, template design, queue processing, rate-limiting, and deep delivery analytics on your own server.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#gfaq2">
                                    Does MailFlow require Node.js or Redis at runtime?
                                </button>
                            </h2>
                            <div id="gfaq2" class="accordion-collapse collapse" data-bs-parent="#faqGeneral">
                                <div class="accordion-body text-secondary">
                                    No! MailFlow is intentionally architected in pure PHP 8.2+ and SQLite 3 PDO. Queue management is handled with atomic database transaction locks, meaning you do not need to install, configure, or maintain Redis, Node.js, or external daemon dependencies.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 2: SMTP & Deliverability -->
                <div class="mb-5">
                    <h3 class="h4 fw-bold text-light mb-3"><i class="bi bi-hdd-network text-success me-2"></i>SMTP & Deliverability</h3>
                    <div class="accordion accordion-flush" id="faqSmtp">
                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#sfaq1">
                                    Can I connect multiple different SMTP providers simultaneously?
                                </button>
                            </h2>
                            <div id="sfaq1" class="accordion-collapse collapse" data-bs-parent="#faqSmtp">
                                <div class="accordion-body text-secondary">
                                    Yes! You can connect as many SMTP providers as you wish. You can group them into logical pools (e.g., "Marketing Pool", "Transactional Pool") and configure Round-Robin, Weighted Distribution, or Priority-Based failover strategies across them.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#sfaq2">
                                    How are SMTP passwords protected?
                                </button>
                            </h2>
                            <div id="sfaq2" class="accordion-collapse collapse" data-bs-parent="#faqSmtp">
                                <div class="accordion-body text-secondary">
                                    All SMTP passwords and access tokens are encrypted at rest using AES-256-GCM cipher with a unique application key. Passwords are never sent to frontend responses or logged in plain text.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category 3: Contacts & Campaigns -->
                <div class="mb-5">
                    <h3 class="h4 fw-bold text-light mb-3"><i class="bi bi-people text-info me-2"></i>Contacts & Campaigns</h3>
                    <div class="accordion accordion-flush" id="faqContacts">
                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#cfaq1">
                                    What file formats are supported for contact imports?
                                </button>
                            </h2>
                            <div id="cfaq1" class="accordion-collapse collapse" data-bs-parent="#faqContacts">
                                <div class="accordion-body text-secondary">
                                    MailFlow natively supports CSV (.csv), Excel spreadsheets (.xlsx), JSON data arrays (.json), and line-separated plain text (.txt) files. Our smart parser auto-detects name and email columns and skips duplicate records automatically.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#cfaq2">
                                    How are unsubscribes and bounce complaints handled?
                                </button>
                            </h2>
                            <div id="cfaq2" class="accordion-collapse collapse" data-bs-parent="#faqContacts">
                                <div class="accordion-body text-secondary">
                                    Every campaign supports one-click unsubscribe tags (<code>{{unsubscribe_url}}</code>). When a subscriber clicks unsubscribe or an address returns a permanent hard bounce (5xx error), MailFlow instantly writes to the suppression table, preventing future accidental sends.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
