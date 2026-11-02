<?php
/**
 * MailFlow - About Page
 * Mission, architectural philosophy, and open-source heritage
 */
?>

<!-- About Hero -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-compass me-1"></i> Our Mission
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-4">
                    Putting Email Sovereignty Back in the Hands of Builders
                </h1>
                <p class="lead text-secondary mb-4 mx-auto" style="max-width: 720px;">
                    We built MailFlow because we believe businesses should own their subscriber data, control their email infrastructure, and never be held hostage by arbitrary SaaS tier price hikes.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="h1 fw-bold text-light mb-3">Why We Created MailFlow</h2>
                <p class="text-secondary mb-3">
                    For over a decade, sending emails has been monopolized by centralized marketing SaaS behemoths. When you grow, they 10x your bill. When one of their shared IP pools gets blacklisted, your transactional confirmation emails stop delivering.
                </p>
                <p class="text-secondary mb-4">
                    MailFlow fundamentally reimagines this model. Instead of relying on a single provider, MailFlow abstracts multiple authorized SMTP relays into high-availability pools with intelligent failover, live quota management, and complete data ownership.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle">
                            <h3 class="h2 fw-bold text-primary mb-1">100%</h3>
                            <p class="text-secondary small mb-0">Self-hosted data residency</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle">
                            <h3 class="h2 fw-bold text-success mb-1">0%</h3>
                            <p class="text-secondary small mb-0">Per-email platform markup</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-dark border-secondary-subtle p-4 shadow-sm">
                    <h4 class="text-light fw-bold mb-3"><i class="bi bi-shield-check text-primary me-2"></i>Engineering Principles</h4>
                    <div class="d-flex flex-column gap-3 text-secondary small">
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary p-2 rounded-2 mt-1"><i class="bi bi-check2"></i></div>
                            <div>
                                <strong class="text-light d-block">Lean & Runtime-Free:</strong>
                                Pure PHP 8.2+ and SQLite 3. No cumbersome Node.js or Redis background daemons required to run your core software.
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar bg-success bg-opacity-10 text-success p-2 rounded-2 mt-1"><i class="bi bi-lock"></i></div>
                            <div>
                                <strong class="text-light d-block">Default Privacy:</strong>
                                Your recipient emails, contact metadata, and message contents never transit our servers. You run everything on your own VPS.
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar bg-info bg-opacity-10 text-info p-2 rounded-2 mt-1"><i class="bi bi-palette"></i></div>
                            <div>
                                <strong class="text-light d-block">Crafted Design:</strong>
                                Enterprise tools don't have to look clunky. We obsess over typography, spacing, and micro-interactions.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-body-tertiary">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <h2 class="h2 fw-bold text-light mb-2">Our Core Values</h2>
            <p class="text-secondary">What drives our architecture and product roadmap.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm">
                    <div class="feature-icon mb-3"><i class="bi bi-fingerprint text-primary fs-3"></i></div>
                    <h5 class="fw-bold text-light mb-2">Respect User Privacy</h5>
                    <p class="text-secondary small mb-0">We never collect, track, or sell your subscriber telemetry. Open and auditable code you can trust.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm">
                    <div class="feature-icon mb-3"><i class="bi bi-sliders text-success fs-3"></i></div>
                    <h5 class="fw-bold text-light mb-2">Uncompromising Performance</h5>
                    <p class="text-secondary small mb-0">Engineered with atomic database locks and lightweight memory footprints to process large queues on modest hardware.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary-subtle p-4 h-100 shadow-sm">
                    <div class="feature-icon mb-3"><i class="bi bi-boxes text-info fs-3"></i></div>
                    <h5 class="fw-bold text-light mb-2">Modular Extensibility</h5>
                    <p class="text-secondary small mb-0">Built with clean MVC architecture, scoped REST APIs, and extensible middleware so you can adapt MailFlow to any stack.</p>
                </div>
            </div>
        </div>
    </div>
</section>
