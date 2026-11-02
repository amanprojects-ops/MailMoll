<?php
/**
 * MailFlow - Pricing Page
 * Transparent self-hosted licensing with zero per-email fees
 */
?>

<!-- Pricing Hero -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-tag-fill me-1"></i> Predictable Economics
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-4">
                    Zero Per-Email Markups. Complete Cost Freedom.
                </h1>
                <p class="lead text-secondary mb-4 mx-auto" style="max-width: 720px;">
                    Traditional SaaS platforms punish your growth by charging more as your subscriber list expands. With MailFlow, you pay once for the software and send millions of emails at raw infrastructure cost.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row g-4 align-items-stretch justify-content-center">
            
            <!-- Tier 1: Community -->
            <div class="col-lg-4 col-md-6">
                <div class="card bg-dark border-secondary-subtle h-100 p-4 d-flex flex-column shadow-sm">
                    <div class="mb-4">
                        <span class="badge bg-secondary-subtle text-secondary mb-2">Open Community</span>
                        <h3 class="h3 fw-bold text-light mb-1">Community</h3>
                        <p class="text-secondary small">Essential multi-SMTP management for side projects and indie hackers.</p>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-extrabold text-light">$0</span>
                            <span class="text-secondary">/ forever free</span>
                        </div>
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-secondary small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success fs-5"></i> Up to 3 SMTP accounts
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success fs-5"></i> Unlimited contact lists & subscribers
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success fs-5"></i> Round-Robin routing strategy
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success fs-5"></i> Visual & Code email builder
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success fs-5"></i> Basic SQLite delivery logs
                        </li>
                        <li class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-x text-secondary fs-5"></i> REST API keys disabled
                        </li>
                        <li class="d-flex align-items-center gap-2 text-muted">
                            <i class="bi bi-x text-secondary fs-5"></i> Multi-user role management
                        </li>
                    </ul>
                    <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-outline-light w-100 py-2 fw-semibold">
                        Deploy Community Free
                    </a>
                </div>
            </div>

            <!-- Tier 2: Professional (Featured) -->
            <div class="col-lg-4 col-md-6">
                <div class="card bg-dark border-primary position-relative h-100 p-4 d-flex flex-column shadow-lg" style="box-shadow: 0 10px 40px rgba(99, 102, 241, 0.15) !important;">
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <span class="badge bg-primary px-3 py-1 text-uppercase fw-bold letter-spacing-1">Most Popular</span>
                    </div>
                    <div class="mb-4">
                        <span class="badge bg-primary-subtle text-primary mb-2">Self-Hosted Pro</span>
                        <h3 class="h3 fw-bold text-light mb-1">Professional</h3>
                        <p class="text-secondary small">For growing startups, agencies, and businesses demanding high deliverability.</p>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-extrabold text-light">$49</span>
                            <span class="text-secondary">/ single server license</span>
                        </div>
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-secondary small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 text-light">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> <strong>Unlimited</strong> SMTP accounts & groups
                        </li>
                        <li class="d-flex align-items-center gap-2 text-light">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> <strong>All routing algorithms:</strong> Weighted, Least Used, Failover
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> High-concurrency CLI queue worker
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> Scoped REST API & Webhooks
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> Automated bounce & suppression lists
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> Multi-format bulk imports (.csv, .xlsx, .json)
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle text-primary fs-5"></i> 1 Year of core updates & priority support
                        </li>
                    </ul>
                    <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                        Get Started with Pro <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Tier 3: Enterprise -->
            <div class="col-lg-4 col-md-6">
                <div class="card bg-dark border-secondary-subtle h-100 p-4 d-flex flex-column shadow-sm">
                    <div class="mb-4">
                        <span class="badge bg-warning-subtle text-warning mb-2">Fleet / Multi-Instance</span>
                        <h3 class="h3 fw-bold text-light mb-1">Enterprise</h3>
                        <p class="text-secondary small">For organizations managing multi-tenant infrastructure and compliance.</p>
                        <div class="d-flex align-items-baseline gap-1 my-3">
                            <span class="display-5 fw-extrabold text-light">$199</span>
                            <span class="text-secondary">/ multi-server pack</span>
                        </div>
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 text-secondary small mb-4 flex-grow-1">
                        <li class="d-flex align-items-center gap-2 text-light">
                            <i class="bi bi-check2 text-warning fs-5"></i> <strong>Everything in Pro</strong>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-warning fs-5"></i> Multi-server deployment fleet license
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-warning fs-5"></i> White-label customization (Custom branding & domain)
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-warning fs-5"></i> Role-based access control (RBAC: Admin, Operator, Viewer)
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-warning fs-5"></i> Enterprise security audit logs & 2FA enforcement
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-warning fs-5"></i> Dedicated architecture onboarding call
                        </li>
                    </ul>
                    <a href="<?= \App\Helpers\UrlHelper::to('/contact') ?>" class="btn btn-outline-secondary w-100 py-2 fw-semibold">
                        Contact Enterprise Sales
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Cost Comparison Table -->
<section class="py-5 bg-body-tertiary">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <h2 class="h2 fw-bold text-light mb-2">Real Cost Comparison: 500,000 Emails / Month</h2>
            <p class="text-secondary">See how MailFlow saves fast-growing companies thousands of dollars annually.</p>
        </div>
        <div class="card bg-dark border-secondary-subtle overflow-hidden shadow-sm">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-secondary text-uppercase fs-7 border-bottom border-secondary-subtle">
                            <th class="ps-4 py-3">Platform</th>
                            <th>Subscribers / Mo</th>
                            <th>Emails / Mo</th>
                            <th>Cost / Month</th>
                            <th class="pe-4 text-end">Annual Spend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 fw-semibold text-light"><i class="bi bi-envelope me-2 text-danger"></i>Mailchimp Standard</td>
                            <td>50,000</td>
                            <td>600,000</td>
                            <td class="text-danger fw-bold">$385 / mo</td>
                            <td class="pe-4 text-end text-danger fw-bold">$4,620</td>
                        </tr>
                        <tr>
                            <td class="ps-4 fw-semibold text-light"><i class="bi bi-envelope me-2 text-primary"></i>SendGrid Pro</td>
                            <td>Unlimited</td>
                            <td>500,000</td>
                            <td class="text-danger fw-bold">$249 / mo</td>
                            <td class="pe-4 text-end text-danger fw-bold">$2,988</td>
                        </tr>
                        <tr class="table-primary bg-primary bg-opacity-10">
                            <td class="ps-4 fw-bold text-primary"><i class="bi bi-check-circle-fill me-2 text-success"></i>MailFlow + Amazon SES</td>
                            <td><strong>Unlimited</strong></td>
                            <td><strong>500,000</strong></td>
                            <td class="text-success fw-bold">$50 SES relay</td>
                            <td class="pe-4 text-end text-success fw-bold">$600 + $49 license ($649 total)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-top border-secondary-subtle p-3 text-center">
                <span class="text-emerald fw-bold"><i class="bi bi-piggy-bank me-2"></i>Net annual savings with MailFlow: Over $3,900 / year</span>
            </div>
        </div>
    </div>
</section>

<!-- Pricing FAQ -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <h2 class="h2 fw-bold text-light mb-2">Frequently Asked Pricing Questions</h2>
            <p class="text-secondary">Clear answers about licensing, self-hosting, and relay costs.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion accordion-flush" id="pricingFaqAccordion">
                    <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#pfaq1">
                                Does MailFlow charge per email sent or per contact stored?
                            </button>
                        </h2>
                        <div id="pfaq1" class="accordion-collapse collapse" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body text-secondary">
                                Absolutely not. MailFlow is self-hosted software running on your server. You can store 1,000 or 1,000,000 contacts and send unlimited emails without paying us any extra fees. You only pay your connected SMTP relays directly (e.g., Amazon SES charges ~$0.10 per 1,000 emails).
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#pfaq2">
                                Where can I host MailFlow?
                            </button>
                        </h2>
                        <div id="pfaq2" class="accordion-collapse collapse" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body text-secondary">
                                MailFlow requires standard PHP 8.2+ and SQLite 3 PDO. It runs effortlessly on standard cPanel shared hosting, $5/mo DigitalOcean droplets, AWS EC2, Hetzner, or internal Docker containers.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-dark border-secondary-subtle mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-light" type="button" data-bs-toggle="collapse" data-bs-target="#pfaq3">
                                Can I upgrade from Community to Pro later?
                            </button>
                        </h2>
                        <div id="pfaq3" class="accordion-collapse collapse" data-bs-parent="#pricingFaqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes! Upgrading is seamless. Your contacts, templates, campaign history, and SMTP configurations remain intact. Simply apply your license key in your Super Admin panel.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
