<?php
/**
 * MailFlow - Privacy Policy
 * GDPR compliance, self-hosted data ownership, and telemetry disclosure
 */
?>

<!-- Privacy Policy Hero -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-4 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-shield-check me-1"></i> Data Protection
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-3">
                    Privacy Policy & Data Sovereignty
                </h1>
                <p class="lead text-secondary mb-0">
                    Last updated: November 2, 2026. MailFlow is designed around complete self-hosted data ownership.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Legal Content -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-secondary">
                
                <div class="p-4 rounded-3 bg-dark border border-secondary-subtle mb-4">
                    <h5 class="text-light fw-bold mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>The Core Principle</h5>
                    <p class="mb-0 small">
                        Because MailFlow is <strong>self-hosted software</strong> installed on your infrastructure, <strong>we do not collect, store, or process your recipient lists, campaign message bodies, or SMTP login credentials</strong>. Your data remains 100% on your own server.
                    </p>
                </div>

                <h3 class="h4 fw-bold text-light mt-4 mb-3">1. Information Processed by Your Self-Hosted Instance</h3>
                <p class="small">
                    When you operate MailFlow on your server, your instance stores contact records, campaign drafts, delivery status codes, and encrypted SMTP passwords directly in your local SQLite database (<code>storage/database/</code>). This information never leaves your server unless transmitted directly to your configured SMTP relays.
                </p>

                <h3 class="h4 fw-bold text-light mt-4 mb-3">2. Third-Party SMTP Relays</h3>
                <p class="small">
                    When sending outbound campaigns or transactional notifications, your server initiates direct TLS connections to the SMTP endpoints you specify (e.g. Amazon SES, Postmark, Mailgun, or private postfix hosts). Those transmissions are governed exclusively by your agreements with those respective cloud providers.
                </p>

                <h3 class="h4 fw-bold text-light mt-4 mb-3">3. GDPR & Recipient Rights</h3>
                <p class="small">
                    MailFlow includes built-in compliance tools to support GDPR and CAN-SPAM regulations, including automated suppression tables, one-click unsubscribe token handling, and contact export/erasure utilities.
                </p>

                <h3 class="h4 fw-bold text-light mt-4 mb-3">4. Security Safeguards</h3>
                <p class="small">
                    MailFlow employs AES-256-GCM cipher encryption for sensitive credentials at rest, enforces CSRF token validation on all mutating web requests, sanitizes HTML template tags, and implements rate-limited login endpoints.
                </p>

                <h3 class="h4 fw-bold text-light mt-4 mb-3">5. Contact Us Regarding Privacy</h3>
                <p class="small">
                    For any privacy or data security questions regarding the software codebase, contact our data protection team at <a href="mailto:privacy@mailflow.local" class="text-primary text-decoration-none">privacy@mailflow.local</a>.
                </p>

            </div>
        </div>
    </div>
</section>
