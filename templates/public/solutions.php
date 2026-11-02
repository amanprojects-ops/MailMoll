<?php
/**
 * MailFlow - Solutions Page
 * Enterprise use cases, industries, and operational architectures
 */
?>

<!-- Solutions Hero Section -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-briefcase me-1"></i> Tailored Implementations
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-4">
                    Architected for Every High-Volume Email Challenge
                </h1>
                <p class="lead text-secondary mb-4 mx-auto" style="max-width: 720px;">
                    Whether you are an engineering team building SaaS notifications, an agency managing dozens of client sending domains, or an enterprise demanding strict privacy compliance, MailFlow delivers.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-primary btn-lg px-4 py-2 fw-semibold">
                        Start Deploying Today <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="<?= \App\Helpers\UrlHelper::to('/contact') ?>" class="btn btn-outline-secondary btn-lg px-4 py-2">
                        Talk to an Architect
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Solutions Grid by Industry / Use Case -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row g-4">
            
            <!-- Use Case 1: SaaS & Product Engineering -->
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm hover-elevate">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar bg-primary bg-opacity-10 text-primary p-3 rounded-4 fs-3">
                            <i class="bi bi-code-square"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1">Developers & Startups</span>
                            <h3 class="h4 fw-bold text-light mb-0">SaaS Transactional Email</h3>
                        </div>
                    </div>
                    <p class="text-secondary mb-4">
                        Critical auth codes, password resets, and invoice receipts can never be delayed by downstream provider downtime or quota exhaustion.
                    </p>
                    <ul class="list-unstyled text-secondary d-flex flex-column gap-2 mb-4 small">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Instant multi-SMTP failover if SES or Postmark has an outage.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Simple cURL / REST API integration with scoped API keys.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Real-time webhooks and delivery attempt telemetry.
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Deliverability SLA: <strong class="text-light">99.99%</strong></span>
                        <a href="<?= \App\Helpers\UrlHelper::to('/docs') ?>" class="text-primary fw-semibold small text-decoration-none">
                            View API Docs <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Use Case 2: Digital Marketing Agencies -->
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm hover-elevate">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar bg-info bg-opacity-10 text-info p-3 rounded-4 fs-3">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <span class="badge bg-info-subtle text-info mb-1">Growth & Marketing</span>
                            <h3 class="h4 fw-bold text-light mb-0">Marketing Agencies & Multi-Client</h3>
                        </div>
                    </div>
                    <p class="text-secondary mb-4">
                        Isolate sending reputations for multiple client brands while centralizing campaign execution, templates, and analytics under one self-hosted portal.
                    </p>
                    <ul class="list-unstyled text-secondary d-flex flex-column gap-2 mb-4 small">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Dedicated SMTP server groups per brand to keep domain reputations clean.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Reusable template library with visual WYSIWYG & HTML editing.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Bulk contact import from spreadsheets (.csv, .xlsx, .json).
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Brand Isolation: <strong class="text-light">100% Guaranteed</strong></span>
                        <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="text-info fw-semibold small text-decoration-none">
                            Get Started <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Use Case 3: E-Commerce Platforms -->
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm hover-elevate">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar bg-warning bg-opacity-10 text-warning p-3 rounded-4 fs-3">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning-subtle text-warning mb-1">Online Retail</span>
                            <h3 class="h4 fw-bold text-light mb-0">E-Commerce & High-Velocity Stores</h3>
                        </div>
                    </div>
                    <p class="text-secondary mb-4">
                        Handle Black Friday order surges, shipping tracking notifications, and segmented product promotions without expensive per-email SaaS tier jumps.
                    </p>
                    <ul class="list-unstyled text-secondary d-flex flex-column gap-2 mb-4 small">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Weighted distribution across multiple cost-effective SMTP relays.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Zero email markups – pay only your direct relay infrastructure cost.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Automated suppression protects against spam complaints and invalid emails.
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Infrastructure Cost: <strong class="text-light">Up to 90% Savings</strong></span>
                        <a href="<?= \App\Helpers\UrlHelper::to('/pricing') ?>" class="text-warning fw-semibold small text-decoration-none">
                            Compare Savings <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Use Case 4: Enterprise & Compliance -->
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm hover-elevate">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar bg-success bg-opacity-10 text-success p-3 rounded-4 fs-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success mb-1">Security & Privacy</span>
                            <h3 class="h4 fw-bold text-light mb-0">Enterprise & Data Sovereignty</h3>
                        </div>
                    </div>
                    <p class="text-secondary mb-4">
                        Finance, healthcare, and government organizations requiring 100% on-premises data residency, GDPR compliance, and air-gapped infrastructure.
                    </p>
                    <ul class="list-unstyled text-secondary d-flex flex-column gap-2 mb-4 small">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Complete data residency on your own private cloud or physical hardware.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Detailed audit logs of all user actions, campaigns, and logins.
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Role-based permissions (Super Admin, Operator, Viewer).
                        </li>
                    </ul>
                    <div class="mt-auto pt-3 border-top border-secondary-subtle d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Data Sovereignty: <strong class="text-light">100% Air-Gapped Capable</strong></span>
                        <a href="<?= \App\Helpers\UrlHelper::to('/contact') ?>" class="text-success fw-semibold small text-decoration-none">
                            Request Enterprise Brief <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Comparison Table Banner -->
<section class="py-5 bg-body-tertiary">
    <div class="container py-4 text-center">
        <div class="max-w-700 mx-auto">
            <h2 class="h1 fw-bold text-light mb-3">Why Top Teams Switch to Self-Hosted MailFlow</h2>
            <p class="text-secondary mb-4">
                Traditional SaaS platforms like SendGrid or Mailchimp charge exorbitant monthly rates as your recipient list grows. MailFlow provides the same sleek UI and high deliverability on your own terms.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= \App\Helpers\UrlHelper::to('/pricing') ?>" class="btn btn-outline-light px-4">
                    Explore Pricing & Tiers
                </a>
                <a href="<?= \App\Helpers\UrlHelper::to('/register') ?>" class="btn btn-primary px-4">
                    Create Free Account
                </a>
            </div>
        </div>
    </div>
</section>
