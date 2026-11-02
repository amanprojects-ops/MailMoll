# MailFlow – Changelog, Issues & Comprehensive Fixes Log

All notable changes, bug fixes, features, and resolved issues for MailFlow are documented in this file.

---

## [Release 2.0.0] - Master SaaS Transformation & Enterprise Upgrade (2026-11-02)

### 🚀 Major Feature Additions

#### 1. Brand Identity & Design System (Part 1)
- Rebranded application from **MailMoll** to **MailFlow**.
- Established unified design tokens with CSS variables:
  - Primary: `#6366F1` (Indigo)
  - Secondary: `#64748B` (Slate)
  - Success: `#10B981` (Emerald)
  - Warning: `#F59E0B` (Amber)
  - Danger: `#EF4444` (Red)
- Built [`public/assets/css/marketing.css`](public/assets/css/marketing.css) featuring glassmorphism navbar, hero glow, feature cards, and interactive light/dark theme switcher.

#### 2. Public Marketing Website (Part 2)
- Added marketing master layout [`templates/layouts/marketing.php`](templates/layouts/marketing.php).
- Created flagship homepage [`templates/public/home.php`](templates/public/home.php) with interactive dashboard preview, trust section, 12 feature pillars, How It Works, comparison table, FAQ accordion, and newsletter consent.
- Created dedicated public pages:
  - [`/features`](templates/public/features.php) – Deep dive into multi-SMTP failover and queue throughput.
  - [`/solutions`](templates/public/solutions.php) – SaaS notifications, marketing agencies, and enterprise privacy.
  - [`/pricing`](templates/public/pricing.php) – Self-hosted pricing matrix with $0 Community, $49 Pro, $199 Enterprise tiers.
  - [`/about`](templates/public/about.php) – Mission, architectural philosophy, and data sovereignty.
  - [`/contact`](templates/public/contact.php) – Support inquiry form with SLA response indicators.
  - [`/docs`](templates/public/docs.php) – Technical guide, cron worker setup, cURL REST API reference.
  - [`/faq`](templates/public/faq.php) – Searchable accordion knowledge base.
  - [`/privacy-policy`](templates/public/privacy.php) & [`/terms`](templates/public/terms.php) – GDPR compliance, self-hosted data residency, and anti-spam terms.
  - `/sitemap.xml` & `/robots.txt` – Native SEO routing.

#### 3. Complete Authentication System (Part 3)
- Created dedicated auth layout [`templates/layouts/auth.php`](templates/layouts/auth.php).
- Created authentication views:
  - [`/login`](templates/auth/login.php) – Modern dark-theme login with session regeneration and rate limiting.
  - [`/register`](templates/auth/register.php) – New user registration with automatic Community tier provisioning.
  - [`/forgot-password`](templates/auth/forgot-password.php) & [`/reset-password`](templates/auth/reset-password.php) – Cryptographically secure token reset flow.
  - [`/verify-email`](templates/auth/verify-email.php) – Email verification and resend handling.
  - [`/two-factor`](templates/auth/two-factor.php) – TOTP 6-digit authenticator verification.
- Expanded [`app/Controllers/AuthController.php`](app/Controllers/AuthController.php) with registration, password resets, verification, and 2FA.

#### 4. REST API Token Management (Part 4)
- Created [`app/Controllers/ApiKeyController.php`](app/Controllers/ApiKeyController.php) for scoped Bearer token generation (`mflw_live_...`).
- Built [`templates/api_keys/index.php`](templates/api_keys/index.php) with one-time secret reveals, clipboard copy, and token revocation.

#### 5. Super Admin Control Center (Part 9)
- Implemented [`app/Middleware/AdminMiddleware.php`](app/Middleware/AdminMiddleware.php) to enforce role-based access for `admin` role.
- Created dedicated master cluster theme [`templates/layouts/admin.php`](templates/layouts/admin.php).
- Implemented [`app/Controllers/AdminController.php`](app/Controllers/AdminController.php) powering 10 admin modules:
  - [`/admin/dashboard`](templates/admin/dashboard.php) – Real-time tenant metrics, database size, disk space, worker heartbeat.
  - [`/admin/users`](templates/admin/users.php) – User directory with role (Admin, Operator, Viewer) and status management.
  - [`/admin/subscriptions`](templates/admin/subscriptions.php) – Customer license tiers.
  - [`/admin/system-health`](templates/admin/system_health.php) – SQLite WAL mode verification, PHP runtime parameters, filesystem write permissions.
  - [`/admin/queue`](templates/admin/queue.php) – Global queue inspection, job retry, and batch flush controls.
  - [`/admin/email-logs`](templates/admin/email_logs.php) – SMTP socket handshake transcripts and latency metrics.
  - [`/admin/security`](templates/admin/security.php) – 2FA enrollments and active reset tokens.
  - [`/admin/audit-logs`](templates/admin/audit_logs.php) – Platform audit trail.
  - [`/admin/settings`](templates/admin/settings.php) – Master batch size, daemon sleep interval, debug toggles.
  - [`/admin/backups`](templates/admin/backups.php) – Instant point-in-time SQLite snapshots and disaster recovery guide.

#### 6. Database Migrations (Part 10)
- Applied [`app/Database/migrations/003_auth_subscriptions_api_keys.sql`](app/Database/migrations/003_auth_subscriptions_api_keys.sql) adding:
  - `api_keys`
  - `subscriptions`
  - `password_resets`
  - `email_verifications`
  - `two_factor_auth`

---

## 🛠️ Issues Encountered & Resolved (Root Cause & Fix Details)

### 1. Issue: "Uncaught Exception: Call to undefined function auth_check()"
- **Reported Error**: `Call to undefined function auth_check() in templates/layouts/marketing.php (Line 76)`
- **Root Cause**: The marketing navigation bar called `auth_check()` which was missing from the helper definitions.
- **Resolution**:
  - Replaced `auth_check()` with `auth_user()` in [`templates/layouts/marketing.php`](templates/layouts/marketing.php).
  - Added global `auth_check()` helper to [`app/Helpers/helpers.php`](app/Helpers/helpers.php).
  - Created [`app/Helpers/UrlHelper.php`](app/Helpers/UrlHelper.php) for safe static URL routing across views.
  - Regenerated optimized Composer autoloader (`composer dump-autoload -o`).

### 2. Issue: "Session token expired. Please retry." on Login
- **Reported Error**: Submitting login credentials at `/login` redirected back with "Session token expired. Please retry."
- **Root Cause**:
  - [`app/Middleware/CsrfMiddleware.php`](app/Middleware/CsrfMiddleware.php) only checked `$_POST['_csrf_token']`, while forms passed `name="csrf_token"`.
  - In addition, session storage had `$_SESSION['_csrf_token']` while forms read `$_SESSION['csrf_token']`.
- **Resolution**:
  - Updated [`app/Helpers/Session.php`](app/Helpers/Session.php) to initialize and validate both `_csrf_token` and `csrf_token`.
  - Updated [`app/Middleware/CsrfMiddleware.php`](app/Middleware/CsrfMiddleware.php) to accept `_csrf_token`, `csrf_token`, and HTTP header `X-CSRF-TOKEN`.
  - Updated [`templates/auth/login.php`](templates/auth/login.php) and all auth forms to use standard `<?= csrf_field() ?>`.

### 3. Issue: Queue Flush Button Not Working
- **Reported Error**: Clicking "Clear Failed Jobs" on `/queue` or "Flush Failed" on `/admin/queue` did not clear stuck jobs.
- **Root Cause**:
  - 24 jobs had become permanently stuck in `status = 'processing'` with lock timestamps from earlier failed dispatches.
  - `QueueController::clearFailed()` was only deleting `status IN ('failed', 'cancelled')`, so timed-out `processing` jobs were completely ignored.
  - The form was missing GET route fallback and redirected to a hardcoded URL instead of returning to the referer page.
- **Resolution**:
  - Permanently purged all 24 timed-out processing jobs from `email_queue`.
  - Wrapped job execution loop in [`app/Services/QueueWorkerService.php`](app/Services/QueueWorkerService.php) with a comprehensive `try/catch` to ensure unexpected dispatch exceptions mark jobs as `failed`/`retry` instead of leaving them stranded in `processing`.
  - Upgraded [`app/Controllers/QueueController.php`](app/Controllers/QueueController.php):
    - `clearFailed()`: deletes `failed`, `cancelled`, `retry`, and timed-out `processing` jobs (>2 min).
    - `resetStuck()`: resets timed-out locks back to `pending`.
    - `purgeAll()`: purges all queue records.
    - Used `Response::back()` so user stays on `/queue` or `/admin/queue` with flash confirmation.
  - Registered GET fallback routes in [`routes/web.php`](routes/web.php).
  - Upgraded [`templates/queue/index.php`](templates/queue/index.php) and [`templates/admin/queue.php`](templates/admin/queue.php) with explicit **Flush Failed**, **Reset Stuck**, and **Flush All** controls.

### 4. Issue: Multi-Format Contact & SMTP Bulk Imports
- **Reported Issue**: Imports were limited to CSV only and lacked sample templates.
- **Resolution**:
  - Implemented multi-format parsers supporting `.csv`, `.xlsx`, `.json`, and `.txt`.
  - Added dedicated sample template downloads for both Contacts and SMTP accounts.
  - Added duplicate detection and group auto-assignment.

### 5. Issue: Draft Campaign Editing & List Rename Missing
- **Reported Issue**: Could not edit saved draft campaigns or rename existing contact lists.
- **Resolution**:
  - Implemented draft campaign edit view with visual WYSIWYG and CodeMirror HTML toggle.
  - Implemented contact list rename modal in both list header and sidebar.

---

## 📈 Git Version History

All features, migrations, and bug fixes have been committed with individual conventional Git commits and synchronized with `origin/main`:

```
f4a53a0 feat(views): add explicit Flush Failed and Flush All buttons in customer queue monitor
006fbd1 feat(admin): upgrade queue flush controls with reset stuck and purge all actions
2f4b7a1 feat(routes): register GET fallback routes for reset-stuck and purge-all
e663ba6 fix(queue): use Response::back and broaden clearFailed to include retry status
cb42142 feat(views): enhance queue monitor actions with direct clear failed and reset stuck controls
183878d fix(worker): catch worker dispatch exceptions to prevent jobs becoming stuck in processing
a742981 feat(queue): expand clearFailed to clear stuck locks and add resetStuck and purgeAll
8d72dce feat(routes): add GET clear-failed fallback, reset-stuck, and purge-all routes
b2e3904 fix(contact): use csrf_field helper in public contact form
2786fd7 fix(auth): use csrf_field helper in two-factor form
b53669a fix(auth): use csrf_field helper in verify-email form
c1ae42b fix(auth): use csrf_field helper in reset-password form
a74548d fix(auth): use csrf_field helper in forgot-password form
61fa6d7 fix(auth): use csrf_field helper in register form
b6f2d95 fix(auth): use csrf_field helper in login form
93fb102 fix(csrf): accept both _csrf_token and csrf_token in CsrfMiddleware
edcc44b fix(session): populate and validate both _csrf_token and csrf_token session keys
365de5a fix(marketing): use auth_user instead of auth_check in marketing navbar
2510a50 feat(helpers): add auth_check helper function
69992ee feat(helpers): create UrlHelper utility class
6eb2be7 docs: comprehensive MailFlow architecture, deployment, and operational guide
1c33be8 refactor(cron): brand cleanup logs to MailFlow
1978e6c refactor(cron): brand worker logs to MailFlow
740ef7e feat(routes): register Super Admin control center routes
b7f4b41 feat(admin): create database snapshot and disaster recovery view
34c88cf feat(admin): create global platform settings view
277612c feat(admin): create global platform audit trail view
8737e7b feat(admin): create security and 2FA enforcement view
d5b31b5 feat(admin): create global SMTP delivery handshake logs view
983c000 feat(admin): create global queue monitoring and inspection view
1a811fe feat(admin): create system health and environment diagnostics view
1a980ac feat(admin): create subscriptions and licensing view
924347a feat(admin): create user and role management view
3f8232e feat(admin): create executive admin dashboard view
a4ab2ff feat(admin): implement AdminController with dashboard, users, subscriptions, health, queue, logs, security, backups
3cb5545 feat(layout): create dedicated Super Admin layout with distinct master cluster theme
3b504d4 feat(middleware): create AdminMiddleware for role-based admin authorization
f9a671b feat(routes): register api-keys web routes
e4906ed feat(views): create API keys management view with one-time token reveal
47058df feat(api): create ApiKeyController for scoped REST token management
d84a0fb feat(layout): upgrade customer dashboard layout to MailFlow enterprise design
65db4c8 feat(routes): register full authentication routes suite
3e16309 feat(auth): expand AuthController with register, password reset, email verification, and 2FA
762a974 feat(views): create two-factor authentication TOTP view
9f65970 feat(views): create email verification notification view
50d8220 feat(views): create reset password form view
8326d9e feat(views): create forgot password view
ac0042a feat(views): create register view for new user signup
2d31ce9 refactor(views): modernize login view for MailFlow
c60dc1d style(layout): redesign auth layout with MailFlow branding and dark theme
4964acb feat(db): add tables for api_keys, subscriptions, password_resets, email_verifications, and two_factor_auth
36a36b5 feat(views): create terms of service page
652df43 feat(views): create privacy policy and gdpr page
ea787b2 feat(views): create faq page
dbe3f3a feat(views): create documentation guide and api reference page
26bcd9e feat(views): create contact sales and support page
e404e44 feat(views): create about page
77bc919 feat(views): create self-hosted pricing marketing page
0920234 feat(views): create solutions use-case marketing page
37974f9 feat(views): create features showcase marketing page
825bdc4 feat(views): create flagship marketing homepage for MailFlow
```
