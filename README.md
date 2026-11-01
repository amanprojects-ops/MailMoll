# MailMoll - Multi-SMTP Email Management System

MailMoll is a production-grade, self-hosted Multi-SMTP email management and dispatch dashboard developed in **PHP 8.2+**, **SQLite 3 (PDO)**, and **Vanilla ES6+ JavaScript**.

It enables companies and developers to coordinate opt-in subscriber broadcasts, automated notifications, and transactional emails across multiple SMTP providers (SendGrid, Amazon SES, Mailgun, Postmark, Google Workspace, custom Postfix) with quota protection, load-balancing routing strategies, and atomic queue management.

---

## 🚀 Key Features

* **Zero Node.js Runtime**: Fully self-contained PHP & SQLite application; runs on standard shared hosting (cPanel), VPS, Docker, or local XAMPP.
* **Multi-SMTP Routing Engine**:
  * Round Robin (circular rotation)
  * Weighted Distribution (probabilistic based on account weight)
  * Least Used (daily load balancing)
  * Priority Based (primary with automated fallback)
  * Dedicated single SMTP selection
* **Hourly & Daily Quota Guard**: Sliding-window tracking prevents hitting provider sending caps.
* **Atomic SQLite Queue**: `BEGIN IMMEDIATE` transaction job claiming prevents race conditions and duplicate sends across parallel worker processes.
* **Transient & Permanent Error Handling**: Exponential backoff for temporary errors (4xx / timeouts); immediate suppression for hard bounces (5xx).
* **Cryptographic Security**: SMTP passwords encrypted at rest via **AES-256-GCM** with OpenSSL. Synchronizer token CSRF protection on all state-changing HTTP verbs.
* **RFC-8058 Compliance**: Native `List-Unsubscribe` headers with one-click and web unsubscribe handling.
* **Dual Template Editor**: Toggle seamlessly between raw HTML (CodeMirror) and visual rich-text (Quill.js) with real-time responsive preview.
* **Interactive 6-Step Campaign Wizard**: Info → Sender & Routing → Audience Selection → Content & Merge Tags → Schedule → Review & Test Dispatch.
* **Real-time Analytics**: Chart.js delivery trends, SMTP provider usage distribution, and campaign comparison graphs.

---

## 🛠️ Technology Stack

* **Backend**: PHP 8.2+, SQLite 3 (PDO in WAL Mode), Composer, PHPMailer.
* **Frontend**: HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, Chart.js, DataTables, SweetAlert2, Toastify.js, Flatpickr, CodeMirror, Quill.js.

---

## 📥 Installation & Setup

### 1. Requirements
* PHP 8.2 or higher
* PHP Extensions: `pdo`, `pdo_sqlite`, `openssl`, `mbstring`, `curl`
* Apache with `mod_rewrite` enabled
* Composer

### 2. Quick Install
```bash
# Navigate to web root
cd /path/to/webroot/MailMoll

# Install PHP dependencies
composer install

# Create environment configuration
cp .env.example .env

# Run database migrations and seed default administrator
php app/Database/init.php
```

### 3. Default Admin Credentials
* **URL**: `http://localhost/MailMoll/public` (or `http://localhost/MailMoll/` if Apache mod_rewrite is enabled)
* **Email**: `admin@mailmoll.local`
* **Password**: `Admin@12345`

*(Be sure to update password in **User Profile** after initial sign-in).*

---

## ⏰ Cron & Background Worker Setup

MailMoll uses an asynchronous queue processor. Do not run bulk email dispatches inside web requests.

### Standard Cron (Recommended for Shared Hosting / cPanel)
Add this crontab entry to run every minute:
```bash
* * * * * /usr/bin/php /path/to/MailMoll/cron/email-worker.php --max-jobs=50 --max-time=55 >> /dev/null 2>&1
```

### Nightly Database Maintenance Cron
Add this entry to run once nightly at 2:00 AM:
```bash
0 2 * * * /usr/bin/php /path/to/MailMoll/cron/cleanup.php >> /dev/null 2>&1
```

### Daemon Mode (For VPS / Systemd / Supervisor)
For high-volume continuous processing:
```bash
php cron/email-worker.php --daemon --sleep=3
```

---

## 🌐 Apache & cPanel Deployment

1. **Document Root**: For optimal security in production, point your Apache virtual host document root directly to `/MailMoll/public`.
2. **Directory Permissions**:
   Ensure write permissions for the web server on the `/storage` directory:
   ```bash
   chmod -R 775 storage
   ```
3. **Protected Files**: Root `.htaccess` and `storage/.htaccess` automatically prevent public HTTP downloads of `.env`, SQLite databases, logs, and private scripts.

---

## 📜 Compliance & Ethical Usage Policy

MailMoll is engineered exclusively for authorized business communication, transactional system notices, and opt-in recipient broadcasts. It includes strict safeguards against unsolicited bulk mailing (spam) and respects unsubscribe requests in compliance with CAN-SPAM, GDPR, and CASL regulations.
