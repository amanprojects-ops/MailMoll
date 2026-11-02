# MailFlow – Enterprise Multi-SMTP Email Management SaaS

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-indigo.svg)](https://php.net)
[![Database](https://img.shields.io/badge/Database-SQLite%203%20WAL-success.svg)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Architecture](https://img.shields.io/badge/Runtime-Zero%20Node.js-emerald.svg)](#)

MailFlow is a self-hosted, multi-SMTP email management SaaS application engineered in **PHP 8.2+**, **SQLite 3 (PDO)**, and **Bootstrap 5.3**. Inspired by the design standards of **Stripe, Resend, Linear, Brevo, and Postmark**, it provides complete data sovereignty, high-deliverability routing pools, campaign orchestration, and a Super Admin fleet control center.

---

## 🏛️ Application Architecture

```
/MailFlow
├── /public                     # Web Root (DocumentRoot)
│   ├── index.php               # Front controller & router dispatch
│   └── /assets                 # Static compiled assets
│       ├── /css                # marketing.css, dashboard.css
│       └── /js                 # app.js, datatables, charts
├── /app
│   ├── /Controllers            # Public, Auth, Dashboard, Smtp, Campaign, Queue, Admin
│   ├── /Database               # Migrations, Connection (PDO WAL), Seeder
│   ├── /Helpers                # Env, View, Router, Session, Validator
│   ├── /Middleware             # AuthMiddleware, AdminMiddleware, GuestMiddleware, CsrfMiddleware
│   ├── /Models                 # Active-record models with PDO transactions
│   └── /Services               # QueueWorkerService, MailerService, RoutingService
├── /config                     # app.php, database.php, security.php
├── /cron                       # email-worker.php, cleanup.php
├── /routes                     # web.php, api.php
├── /storage                    # database (SQLite), logs, backups, uploads
├── /templates                  # PHP layouts (marketing, auth, main, admin) & views
└── composer.json
```

---

## 🚀 Key Modules & Capabilities

### 1. Public Marketing Website
* **Marketing Layout**: High-contrast dark theme, glassmorphic navigation, hero glow, and animated typography.
* **Dedicated Sub-pages**:
  * `/` – Flagship homepage with interactive dashboard preview, trust pillars, and 12-feature showcase.
  * `/features` – Deep dive into multi-SMTP failover, quota guards, and queue throughput.
  * `/solutions` – Tailored solutions for SaaS engineering, digital marketing agencies, and enterprise compliance.
  * `/pricing` – Transparent self-hosted licensing (Community $0, Pro $49, Enterprise $199) with ROI calculator.
  * `/about` – Engineering principles, privacy-first mission, and open-source heritage.
  * `/contact` – Interactive inquiry form with support SLA guidelines.
  * `/docs` – Architecture guide, installation tutorial, and REST API reference.
  * `/faq` – Searchable knowledge base covering relay configurations and rate limits.
  * `/privacy-policy` & `/terms` – Strict GDPR compliance, data residency, and anti-spam terms.
  * `/sitemap.xml` & `/robots.txt` – Native SEO routing.

### 2. Full Authentication Suite
* `/login` & `/register` – Argon2id / BCRYPT hashing, CSRF synchronizer tokens, and session regeneration.
* `/forgot-password` & `/reset-password` – Expiring cryptographically secure one-time tokens.
* `/verify-email` – Email verification flow.
* `/two-factor` – Optional TOTP authenticator validation.

### 3. Customer Application Dashboard
* **Operational Overview**: Live SMTP health, queued messages, delivery rates, and Chart.js activity telemetry.
* **Multi-SMTP Infrastructure**: Connect Amazon SES, Mailgun, Postmark, Google Workspace, or custom Postfix relays.
* **Bulk SMTP & Contact Import**: Multi-format parser (`.csv`, `.xlsx`, `.json`, `.txt`) with sample downloads.
* **Routing Algorithms**: Round-Robin, Weighted Distribution, Least-Used, and Priority-Based Failover.
* **Campaign Manager**: Multi-step wizard with visual WYSIWYG, CodeMirror HTML editor, dynamic tags, and mobile preview.
* **REST API Keys**: Bearer token generation with one-time secret display.

### 4. Super Admin Control Center (`/admin/*`)
* **Executive Dashboard**: Total users, active tenants, new registrations, database size, and disk space.
* **Role-Based Permissions**: Super Admin, Operator, and Viewer role enforcement via `AdminMiddleware`.
* **System Telemetry**: SQLite WAL journaling verification, PHP runtime parameters, and filesystem write audits.
* **Global Queue & Delivery Handshakes**: Real-time socket error inspection and batch retry tools.
* **Point-in-Time Backups**: Instant SQLite snapshot generation with single-click restore instructions.

---

## 📥 Installation & Setup

### Requirements
* PHP 8.2 or higher
* PHP Extensions: `pdo`, `pdo_sqlite`, `openssl`, `mbstring`, `curl`, `json`
* Apache with `mod_rewrite` enabled (or Nginx)
* Composer

### Quick Start
```bash
# 1. Clone repository
git clone https://github.com/your-org/mailflow.git
cd mailflow

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Configure environment
cp .env.example .env

# 4. Set directory permissions
chmod -R 775 storage
```

Default credentials:
* **Email:** `admin@mailmoll.local`
* **Password:** `Admin@12345`

---

## ⚙️ Cron Configuration (Production)

Add these entries to your server crontab (`crontab -e`):

```bash
# 1. Process email queue every minute
* * * * * php /var/www/mailflow/cron/email-worker.php >> /var/www/mailflow/storage/logs/cron.log 2>&1

# 2. Daily maintenance (prune logs, optimize SQLite database)
0 3 * * * php /var/www/mailflow/cron/cleanup.php >> /var/www/mailflow/storage/logs/cleanup.log 2>&1
```

---

## 🌐 cPanel & Apache Deployment Guide

1. Upload the project folder outside the `public_html` root (e.g., `/home/username/mailflow`).
2. Symlink or set DocumentRoot to `/home/username/mailflow/public`.
3. In cPanel, verify PHP version is 8.2+ with `pdo_sqlite` enabled in **Select PHP Version**.
4. In cPanel **Cron Jobs**, add the command:
   ```bash
   /usr/local/bin/php /home/username/mailflow/cron/email-worker.php >/dev/null 2>&1
   ```

---

## 🔒 Security Architecture

* **SMTP Passwords**: Encrypted at rest via **AES-256-GCM** using authenticated ciphers.
* **CSRF Protection**: Synchronizer tokens validated on all mutating POST requests.
* **Session Fixation**: Sessions regenerated on login and privilege escalation.
* **Anti-Spam Compliance**: RFC-8058 `List-Unsubscribe` headers and automated suppression tables.

---

## 📄 License
This project is licensed under the MIT License.
