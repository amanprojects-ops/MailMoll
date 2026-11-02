# MailFlow v2.0.0 Release Notes & Production Deployment Checklist

## 🚀 Release Overview
MailFlow v2.0.0 is a milestone transformation providing a self-hosted multi-SMTP email management SaaS platform built with pure PHP 8.2+ and SQLite 3 WAL journaling.

---

## 📋 Production Deployment Checklist

### 1. Server Environment
- [x] PHP 8.2+ installed with extensions: `pdo_sqlite`, `curl`, `openssl`, `mbstring`, `intl`.
- [x] Web server DocumentRoot configured to `/path/to/mailflow/public`.
- [x] `mod_rewrite` enabled for Apache or standard try_files for Nginx.

### 2. Filesystem & Permissions
- [x] `storage/database/` has 0775 permissions for web server user (`www-data` or Apache).
- [x] `storage/logs/` has 0775 permissions.
- [x] `storage/backups/` has 0775 permissions.

### 3. Background Cron Automation
- [x] Queue Worker crontab configured:
  ```bash
  * * * * * php /var/www/mailflow/cron/email-worker.php >> /var/www/mailflow/storage/logs/cron.log 2>&1
  ```
- [x] Daily maintenance crontab configured:
  ```bash
  0 3 * * * php /var/www/mailflow/cron/cleanup.php >> /var/www/mailflow/storage/logs/cleanup.log 2>&1
  ```

### 4. Security Verification
- [x] Application key generated in `.env` for AES-256-GCM authenticated SMTP password encryption.
- [x] CSRF protection enabled on all mutating HTTP routes (`POST`, `PUT`, `DELETE`).
- [x] Session regeneration verified on user sign-in to prevent session fixation.
- [x] Super Admin access restricted to users with `role = 'admin'` via `AdminMiddleware`.

---

## 🔗 Related Issues Resolved
- **#1**: Session token expired error due to CSRF token key mismatch (`edcc44b`, `93fb102`, `b6f2d95`).
- **#2**: Uncaught Exception: Call to undefined function `auth_check()` (`365de5a`, `2510a50`, `69992ee`).
- **#3**: Queue flush button unresponsive due to stuck processing locks & missing GET fallback (`183878d`, `a742981`, `e663ba6`, `f4a53a0`).
- **#4**: MailFlow 2.0 Enterprise SaaS Transformation Parts 1 to 12.
- **#5**: Multi-format bulk import for contacts and SMTP accounts with sample downloads.
- **#6**: Draft campaign edit mode and contact list renaming.
