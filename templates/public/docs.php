<?php
/**
 * MailFlow - Documentation Page
 * Technical architecture guide, installation instructions, and REST API reference
 */
?>

<!-- Docs Layout -->
<section class="py-5">
    <div class="container py-lg-3">
        <div class="row g-4">
            
            <!-- Docs Sidebar Navigation -->
            <div class="col-lg-3">
                <div class="card bg-dark border-secondary-subtle p-3 sticky-top" style="top: 100px;">
                    <div class="fw-bold text-light small text-uppercase mb-2 letter-spacing-1">Getting Started</div>
                    <ul class="nav flex-column nav-pills gap-1 mb-4 small">
                        <li class="nav-item"><a href="#overview" class="nav-link active py-1 px-2">Overview</a></li>
                        <li class="nav-item"><a href="#requirements" class="nav-link text-secondary py-1 px-2">System Requirements</a></li>
                        <li class="nav-item"><a href="#installation" class="nav-link text-secondary py-1 px-2">Quick Installation</a></li>
                        <li class="nav-item"><a href="#cron-worker" class="nav-link text-secondary py-1 px-2">Configuring Cron Worker</a></li>
                    </ul>

                    <div class="fw-bold text-light small text-uppercase mb-2 letter-spacing-1">SMTP Infrastructure</div>
                    <ul class="nav flex-column nav-pills gap-1 mb-4 small">
                        <li class="nav-item"><a href="#smtp-setup" class="nav-link text-secondary py-1 px-2">Connecting Relays</a></li>
                        <li class="nav-item"><a href="#routing-strategies" class="nav-link text-secondary py-1 px-2">Routing & Failover</a></li>
                        <li class="nav-item"><a href="#rate-limits" class="nav-link text-secondary py-1 px-2">Rate Limiting & Quotas</a></li>
                    </ul>

                    <div class="fw-bold text-light small text-uppercase mb-2 letter-spacing-1">REST API Reference</div>
                    <ul class="nav flex-column nav-pills gap-1 small">
                        <li class="nav-item"><a href="#api-auth" class="nav-link text-secondary py-1 px-2">Authentication</a></li>
                        <li class="nav-item"><a href="#api-send" class="nav-link text-secondary py-1 px-2">POST /emails/send</a></li>
                        <li class="nav-item"><a href="#api-queue" class="nav-link text-secondary py-1 px-2">GET /queue/status</a></li>
                    </ul>
                </div>
            </div>

            <!-- Docs Main Content -->
            <div class="col-lg-9">
                
                <!-- Overview -->
                <div id="overview" class="mb-5 pb-4 border-bottom border-secondary-subtle">
                    <span class="badge bg-primary-subtle text-primary mb-2">Technical Overview</span>
                    <h1 class="display-5 fw-bold text-light mb-3">MailFlow Architecture Guide</h1>
                    <p class="lead text-secondary">
                        MailFlow is an enterprise-grade, self-hosted email management SaaS engineered in modern PHP 8.2+ and SQLite 3. It decouples high-level campaign and transactional dispatch from underlying physical or cloud SMTP relays.
                    </p>
                </div>

                <!-- Requirements -->
                <div id="requirements" class="mb-5 pb-4 border-bottom border-secondary-subtle">
                    <h2 class="h3 fw-bold text-light mb-3">System Requirements</h2>
                    <p class="text-secondary small mb-3">
                        MailFlow is deliberately engineered without heavy background dependencies like Redis or Node.js. It runs on virtually any VPS or shared cPanel environment.
                    </p>
                    <div class="card bg-dark border-secondary-subtle p-3">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-secondary small">
                            <li><strong class="text-light">PHP:</strong> Version 8.2 or higher</li>
                            <li><strong class="text-light">Extensions:</strong> <code>pdo_sqlite</code>, <code>curl</code>, <code>openssl</code>, <code>mbstring</code>, <code>intl</code></li>
                            <li><strong class="text-light">Database:</strong> SQLite 3 with Write-Ahead Logging (WAL enabled automatically)</li>
                            <li><strong class="text-light">Web Server:</strong> Apache (with <code>mod_rewrite</code>) or Nginx</li>
                        </ul>
                    </div>
                </div>

                <!-- Installation -->
                <div id="installation" class="mb-5 pb-4 border-bottom border-secondary-subtle">
                    <h2 class="h3 fw-bold text-light mb-3">Quick Installation</h2>
                    <p class="text-secondary small mb-3">Clone the repository and install dependencies with Composer:</p>
                    <div class="bg-black p-3 rounded-3 font-monospace small mb-3">
                        <div class="text-secondary"># 1. Clone repository</div>
                        <div class="text-light">git clone https://github.com/your-org/mailflow.git /var/www/mailflow</div>
                        <div class="text-secondary mt-2"># 2. Install PHP dependencies</div>
                        <div class="text-light">cd /var/www/mailflow && composer install --no-dev --optimize-autoloader</div>
                        <div class="text-secondary mt-2"># 3. Setup environment configuration</div>
                        <div class="text-light">cp .env.example .env</div>
                    </div>
                    <p class="text-secondary small">
                        Ensure directory write permissions on <code>storage/database/</code> and <code>storage/logs/</code>.
                    </p>
                </div>

                <!-- Cron Worker -->
                <div id="cron-worker" class="mb-5 pb-4 border-bottom border-secondary-subtle">
                    <h2 class="h3 fw-bold text-light mb-3">Configuring the Queue Worker</h2>
                    <p class="text-secondary small mb-3">
                        To process transactional emails and scheduled campaigns reliably in the background, set up a cron job on your server to execute every minute:
                    </p>
                    <div class="bg-black p-3 rounded-3 font-monospace small mb-3">
                        <div class="text-success">* * * * * php /var/www/mailflow/cron/email-worker.php >> /var/www/mailflow/storage/logs/cron.log 2>&1</div>
                    </div>
                    <p class="text-secondary small">
                        The worker uses atomic PDO row locking to claim batches of up to 100 messages, ensuring zero double-sends even under multi-process execution.
                    </p>
                </div>

                <!-- REST API Reference -->
                <div id="api-auth" class="mb-5 pb-4 border-bottom border-secondary-subtle">
                    <span class="badge bg-warning-subtle text-warning mb-2">REST API</span>
                    <h2 class="h3 fw-bold text-light mb-3">Authentication</h2>
                    <p class="text-secondary small mb-3">
                        All programmatic API requests require a valid Bearer token generated from your customer dashboard under <strong>API Keys</strong>.
                    </p>
                    <div class="bg-black p-3 rounded-3 font-monospace small mb-3">
                        <div class="text-light">Authorization: Bearer mflw_live_xxxxxxxxxxxxxxxxxxxx</div>
                    </div>
                </div>

                <div id="api-send" class="mb-5">
                    <h2 class="h4 fw-bold text-light mb-2">POST /api/v1/emails/send</h2>
                    <p class="text-secondary small mb-3">Enqueue a transactional message to be dispatched via an SMTP group.</p>
                    <div class="bg-black p-3 rounded-3 font-monospace small mb-3">
<pre class="text-light mb-0"><code>curl -X POST https://your-mailflow-domain.com/api/v1/emails/send \
  -H "Authorization: Bearer mflw_live_xxxxxxxx" \
  -H "Content-Type: application/json" \
  -d '{
    "to": "user@example.com",
    "subject": "Your Welcome Code",
    "body_html": "&lt;h1&gt;Welcome!&lt;/h1&gt;&lt;p&gt;Your verification code is 492011.&lt;/p&gt;",
    "smtp_group_id": 1
  }'</code></pre>
                    </div>
                    <p class="text-secondary small">Expected Response (200 OK):</p>
                    <div class="bg-black p-3 rounded-3 font-monospace small text-success">
                        {"status": "queued", "message_id": "msg_98410294", "timestamp": "2026-11-02T21:20:00Z"}
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
