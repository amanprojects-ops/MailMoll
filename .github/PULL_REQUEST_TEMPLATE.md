## 📌 Overview
<!-- Summary of the changes introduced in this pull request -->

## 🛠️ Key Changes
- [x] Marketing Website: Added 9 dedicated sub-pages (`/`, `/features`, `/solutions`, `/pricing`, `/about`, `/contact`, `/docs`, `/faq`, `/privacy-policy`, `/terms`).
- [x] Authentication: Added `/login`, `/register`, `/forgot-password`, `/reset-password`, `/verify-email`, `/two-factor`.
- [x] Customer Dashboard: Upgraded navigation, quick search (Ctrl+K), real-time notifications, theme switcher, and REST API key management.
- [x] Super Admin: Implemented master cluster control center with 10 management views (`/admin/*`).
- [x] Queue & Routing: Added multi-format bulk imports, exponential backoff, stuck job flush controls, and atomic SQLite row locking.
- [x] Security: Synchronized CSRF token verification across all mutating routes, AES-256-GCM credential encryption.

## 🔗 Related Issues
- Closes #1
- Closes #2
- Closes #3
- Closes #4
- Closes #5
- Closes #6

## ✅ Verification & Testing
- [x] PHP syntax linting (`php -l`) passed across all controllers, helpers, models, and views.
- [x] SQLite database migration `003_auth_subscriptions_api_keys.sql` applied cleanly.
- [x] Route dispatch and template rendering verified for public, customer, and admin interfaces.
- [x] Queue worker exception handling verified with zero orphan locks.
