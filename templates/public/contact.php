<?php
/**
 * MailFlow - Contact & Support Page
 * Interactive inquiry form and engineering support channels
 */
?>

<!-- Contact Hero -->
<section class="py-5 text-center position-relative overflow-hidden">
    <div class="marketing-glow top-0 start-50 translate-middle-x"></div>
    <div class="container py-lg-4 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-3">
                    <i class="bi bi-chat-dots me-1"></i> We're Here to Help
                </span>
                <h1 class="display-4 fw-extrabold text-light tracking-tight mb-3">
                    Get in Touch with Our Engineering Team
                </h1>
                <p class="lead text-secondary mb-0">
                    Have questions about self-hosting, multi-SMTP routing architectures, or enterprise volume licensing? We usually reply within a few hours.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form and Channels -->
<section class="py-5 border-top border-secondary-subtle">
    <div class="container py-4">
        <div class="row g-5">
            
            <!-- Left Column: Contact Channels -->
            <div class="col-lg-5">
                <div class="pe-lg-4">
                    <h3 class="h4 fw-bold text-light mb-3">Connect Directly</h3>
                    <p class="text-secondary small mb-4">
                        Choose the channel that best suits your needs, whether you are seeking rapid technical troubleshooting or bespoke enterprise deployment advice.
                    </p>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle d-flex align-items-center gap-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                                <i class="bi bi-envelope-at"></i>
                            </div>
                            <div>
                                <small class="text-secondary d-block">Technical Support</small>
                                <a href="mailto:support@mailflow.local" class="text-light fw-semibold text-decoration-none">support@mailflow.local</a>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle d-flex align-items-center gap-3">
                            <div class="avatar bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                                <i class="bi bi-building-check"></i>
                            </div>
                            <div>
                                <small class="text-secondary d-block">Enterprise Sales & Fleet Licensing</small>
                                <a href="mailto:sales@mailflow.local" class="text-light fw-semibold text-decoration-none">sales@mailflow.local</a>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 bg-dark border border-secondary-subtle d-flex align-items-center gap-3">
                            <div class="avatar bg-info bg-opacity-10 text-info p-3 rounded-3 fs-4">
                                <i class="bi bi-discord"></i>
                            </div>
                            <div>
                                <small class="text-secondary d-block">Developer Community Discord</small>
                                <span class="text-light fw-semibold">discord.gg/mailflow</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary-subtle">
                        <div class="d-flex align-items-center gap-2 mb-1 text-light fw-semibold small">
                            <i class="bi bi-clock-history text-primary"></i> Support Response SLA
                        </div>
                        <p class="text-secondary small mb-0">
                            Community inquiries: within 24-48 hours.<br>
                            Pro & Enterprise license holders: <strong>Under 4 hours guaranteed</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Form -->
            <div class="col-lg-7">
                <div class="card bg-dark border-secondary-subtle p-4 p-lg-5 shadow-sm">
                    <h3 class="h4 fw-bold text-light mb-1">Send Us a Message</h3>
                    <p class="text-secondary small mb-4">Fill out the form below and an engineer will review your request.</p>

                    <form action="<?= \App\Helpers\UrlHelper::to('/contact') ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label text-light small fw-semibold">Your Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-body-tertiary border-secondary-subtle text-light" id="name" name="name" placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-light small fw-semibold">Work Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control bg-body-tertiary border-secondary-subtle text-light" id="email" name="email" placeholder="jane@company.com" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label text-light small fw-semibold">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-body-tertiary border-secondary-subtle text-light" id="subject" name="subject" placeholder="Inquiry regarding multi-SMTP routing" required>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label text-light small fw-semibold">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control bg-body-tertiary border-secondary-subtle text-light" id="message" name="message" rows="5" placeholder="Tell us about your email volume, current SMTP relays, or technical questions..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold w-100">
                                    <i class="bi bi-send-fill me-1"></i> Send Inquiries
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
