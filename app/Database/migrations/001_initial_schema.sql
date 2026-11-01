-- MailMoll Database Schema
-- SQLite 3 with Foreign Keys Enabled

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'admin', -- 'admin', 'operator', 'viewer'
    status TEXT NOT NULL DEFAULT 'active', -- 'active', 'inactive'
    last_login_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. SMTP Accounts Table
CREATE TABLE IF NOT EXISTS smtp_accounts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    provider TEXT NOT NULL DEFAULT 'generic', -- 'generic', 'sendgrid', 'ses', 'mailgun', 'google', 'custom'
    host TEXT NOT NULL,
    port INTEGER NOT NULL DEFAULT 587,
    encryption TEXT NOT NULL DEFAULT 'tls', -- 'tls', 'ssl', 'starttls', 'none'
    username TEXT NOT NULL,
    password_encrypted TEXT NOT NULL,
    from_email TEXT NOT NULL,
    from_name TEXT NOT NULL,
    reply_to TEXT NULL,
    daily_limit INTEGER NOT NULL DEFAULT 500,
    hourly_limit INTEGER NOT NULL DEFAULT 50,
    priority INTEGER NOT NULL DEFAULT 10,
    weight INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'active', -- 'active', 'inactive', 'paused', 'failed'
    last_tested_at DATETIME NULL,
    last_test_result TEXT NULL,
    consecutive_failures INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. SMTP Groups Table
CREATE TABLE IF NOT EXISTS smtp_groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    routing_strategy TEXT NOT NULL DEFAULT 'round_robin', -- 'round_robin', 'weighted', 'least_used', 'priority'
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. SMTP Group Members (Many-to-Many)
CREATE TABLE IF NOT EXISTS smtp_group_members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL REFERENCES smtp_groups(id) ON DELETE CASCADE,
    smtp_account_id INTEGER NOT NULL REFERENCES smtp_accounts(id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(group_id, smtp_account_id)
);

-- 5. SMTP Usage (Rolling Quota Tracker)
CREATE TABLE IF NOT EXISTS smtp_usage (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    smtp_account_id INTEGER NOT NULL REFERENCES smtp_accounts(id) ON DELETE CASCADE,
    year_month_day TEXT NOT NULL, -- Format: YYYY-MM-DD
    hour_slot INTEGER NOT NULL, -- 0 to 23
    sent_count INTEGER NOT NULL DEFAULT 0,
    failed_count INTEGER NOT NULL DEFAULT 0,
    UNIQUE(smtp_account_id, year_month_day, hour_slot)
);

-- 6. Contact Lists Table
CREATE TABLE IF NOT EXISTS contact_lists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 7. Contacts Table (Opt-in only)
CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    first_name TEXT NULL,
    last_name TEXT NULL,
    metadata_json TEXT NULL,
    status TEXT NOT NULL DEFAULT 'subscribed', -- 'subscribed', 'unsubscribed', 'bounced', 'complaint'
    opt_in_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    opt_in_ip TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 8. Contact List Members (Many-to-Many)
CREATE TABLE IF NOT EXISTS contact_list_members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    list_id INTEGER NOT NULL REFERENCES contact_lists(id) ON DELETE CASCADE,
    contact_id INTEGER NOT NULL REFERENCES contacts(id) ON DELETE CASCADE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(list_id, contact_id)
);

-- 9. Email Templates Table
CREATE TABLE IF NOT EXISTS email_templates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    subject_default TEXT NULL,
    body_html TEXT NOT NULL,
    body_plain TEXT NULL,
    tags TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 10. Campaigns Table
CREATE TABLE IF NOT EXISTS campaigns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    subject TEXT NOT NULL,
    sender_name TEXT NOT NULL,
    sender_email TEXT NOT NULL,
    reply_to TEXT NULL,
    template_id INTEGER NULL REFERENCES email_templates(id) ON DELETE SET NULL,
    routing_type TEXT NOT NULL DEFAULT 'group', -- 'group', 'single_smtp'
    smtp_group_id INTEGER NULL REFERENCES smtp_groups(id) ON DELETE SET NULL,
    smtp_account_id INTEGER NULL REFERENCES smtp_accounts(id) ON DELETE SET NULL,
    target_type TEXT NOT NULL DEFAULT 'lists', -- 'lists', 'all'
    target_list_ids TEXT NULL, -- JSON array of list IDs e.g. [1, 2]
    body_html TEXT NOT NULL,
    body_plain TEXT NULL,
    status TEXT NOT NULL DEFAULT 'draft', -- 'draft', 'scheduled', 'queued', 'processing', 'paused', 'completed', 'cancelled'
    scheduled_at DATETIME NULL,
    total_recipients INTEGER NOT NULL DEFAULT 0,
    sent_count INTEGER NOT NULL DEFAULT 0,
    failed_count INTEGER NOT NULL DEFAULT 0,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 11. Email Queue Table (Atomic High-Throughput Queue)
CREATE TABLE IF NOT EXISTS email_queue (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NULL REFERENCES campaigns(id) ON DELETE SET NULL,
    contact_id INTEGER NULL REFERENCES contacts(id) ON DELETE SET NULL,
    recipient_email TEXT NOT NULL,
    recipient_name TEXT NULL,
    subject TEXT NOT NULL,
    body_html TEXT NOT NULL,
    body_plain TEXT NULL,
    headers_json TEXT NULL,
    selected_smtp_id INTEGER NULL REFERENCES smtp_accounts(id) ON DELETE SET NULL,
    status TEXT NOT NULL DEFAULT 'pending', -- 'pending', 'processing', 'sent', 'retry', 'failed', 'cancelled'
    priority INTEGER NOT NULL DEFAULT 5,
    attempts INTEGER NOT NULL DEFAULT 0,
    max_attempts INTEGER NOT NULL DEFAULT 3,
    scheduled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    locked_at DATETIME NULL,
    locked_by TEXT NULL,
    last_error TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 12. Email Attempts Table (Audit trace of each SMTP try)
CREATE TABLE IF NOT EXISTS email_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    queue_id INTEGER NOT NULL REFERENCES email_queue(id) ON DELETE CASCADE,
    smtp_account_id INTEGER NULL REFERENCES smtp_accounts(id) ON DELETE SET NULL,
    attempt_number INTEGER NOT NULL,
    status_code TEXT NULL,
    response_text TEXT NULL,
    duration_ms INTEGER NULL,
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 13. Delivery Events Table
CREATE TABLE IF NOT EXISTS delivery_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    campaign_id INTEGER NULL REFERENCES campaigns(id) ON DELETE SET NULL,
    smtp_account_id INTEGER NULL REFERENCES smtp_accounts(id) ON DELETE SET NULL,
    recipient_email TEXT NOT NULL,
    event_type TEXT NOT NULL, -- 'accepted', 'rejected', 'bounced', 'unsubscribed'
    meta_json TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 14. Suppressions Table (Blocks known bounces & unsubscribes)
CREATE TABLE IF NOT EXISTS suppressions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    reason TEXT NOT NULL DEFAULT 'unsubscribe', -- 'hard_bounce', 'complaint', 'unsubscribe', 'manual'
    source_campaign_id INTEGER NULL REFERENCES campaigns(id) ON DELETE SET NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 15. Unsubscribe Tokens Table (RFC 8058 One-Click & Web Unsubscribe)
CREATE TABLE IF NOT EXISTS unsubscribe_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token TEXT UNIQUE NOT NULL,
    email TEXT NOT NULL,
    campaign_id INTEGER NULL REFERENCES campaigns(id) ON DELETE SET NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    used_at DATETIME NULL
);

-- 16. Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
    action TEXT NOT NULL,
    resource_type TEXT NOT NULL,
    resource_id INTEGER NULL,
    ip_address TEXT NULL,
    user_agent TEXT NULL,
    details_json TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 17. App Settings Table
CREATE TABLE IF NOT EXISTS app_settings (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL,
    type TEXT NOT NULL DEFAULT 'string', -- 'string', 'integer', 'boolean', 'json'
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- PERFORMANCE & QUERY INDEXES
CREATE INDEX IF NOT EXISTS idx_email_queue_claim ON email_queue(status, scheduled_at, priority);
CREATE INDEX IF NOT EXISTS idx_email_queue_campaign ON email_queue(campaign_id);
CREATE INDEX IF NOT EXISTS idx_email_queue_recipient ON email_queue(recipient_email);
CREATE INDEX IF NOT EXISTS idx_contacts_email ON contacts(email);
CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status);
CREATE INDEX IF NOT EXISTS idx_smtp_usage_lookup ON smtp_usage(smtp_account_id, year_month_day, hour_slot);
CREATE INDEX IF NOT EXISTS idx_suppressions_email ON suppressions(email);
CREATE INDEX IF NOT EXISTS idx_delivery_events_campaign ON delivery_events(campaign_id);
CREATE INDEX IF NOT EXISTS idx_delivery_events_smtp ON delivery_events(smtp_account_id);
CREATE INDEX IF NOT EXISTS idx_delivery_events_created ON delivery_events(created_at);
