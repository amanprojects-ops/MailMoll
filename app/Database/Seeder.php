<?php

namespace App\Database;

use PDO;

class Seeder
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function seed(): void
    {
        // 1. Seed Admin User
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => 'admin@mailmoll.local']);
        if ($stmt->fetchColumn() == 0) {
            $hashed = password_hash('Admin@12345', PASSWORD_BCRYPT);
            $ins = $this->db->prepare("
                INSERT INTO users (name, email, password_hash, role, status)
                VALUES (:name, :email, :password, 'admin', 'active')
            ");
            $ins->execute([
                ':name' => 'System Administrator',
                ':email' => 'admin@mailmoll.local',
                ':password' => $hashed,
            ]);
        }

        // 2. Seed Default App Settings
        $settings = [
            'app_title' => ['value' => 'MailMoll', 'type' => 'string'],
            'company_name' => ['value' => 'MailMoll Enterprise', 'type' => 'string'],
            'default_from_email' => ['value' => 'no-reply@mailmoll.local', 'type' => 'string'],
            'default_from_name' => ['value' => 'MailMoll System', 'type' => 'string'],
            'max_retry_attempts' => ['value' => '3', 'type' => 'integer'],
            'batch_size' => ['value' => '50', 'type' => 'integer'],
            'enable_dark_mode' => ['value' => '0', 'type' => 'boolean'],
            'worker_heartbeat' => ['value' => date('Y-m-d H:i:s'), 'type' => 'string'],
        ];

        foreach ($settings as $key => $info) {
            $check = $this->db->prepare("SELECT COUNT(*) FROM app_settings WHERE key = :key");
            $check->execute([':key' => $key]);
            if ($check->fetchColumn() == 0) {
                $ins = $this->db->prepare("INSERT INTO app_settings (key, value, type) VALUES (:key, :val, :type)");
                $ins->execute([
                    ':key' => $key,
                    ':val' => $info['value'],
                    ':type' => $info['type'],
                ]);
            }
        }

        // 3. Seed Default SMTP Group
        $grpCheck = $this->db->query("SELECT COUNT(*) FROM smtp_groups")->fetchColumn();
        if ($grpCheck == 0) {
            $insGrp = $this->db->prepare("
                INSERT INTO smtp_groups (name, routing_strategy, description)
                VALUES ('Default High-Priority Pool', 'round_robin', 'Standard round-robin rotation for priority broadcasts')
            ");
            $insGrp->execute();
        }

        // 4. Seed Reusable Email Templates
        $tplCheck = $this->db->query("SELECT COUNT(*) FROM email_templates")->fetchColumn();
        if ($tplCheck == 0) {
            $welcomeHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to MailMoll</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="background-color: #6366f1; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Welcome, {{first_name}}!</h1>
        </div>
        <div style="padding: 30px; color: #334155; line-height: 1.6; font-size: 15px;">
            <p>Hello {{first_name}} {{last_name}},</p>
            <p>Thank you for subscribing to our updates. We're thrilled to have you with us.</p>
            <p>This is a verified transactional broadcast powered by <strong>MailMoll Multi-SMTP Engine</strong>.</p>
            <div style="margin: 30px 0; text-align: center;">
                <a href="#" style="background-color: #6366f1; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; display: inline-block;">Explore Your Account</a>
            </div>
            <p style="font-size: 13px; color: #64748b; margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                You received this email because you opted in with <strong>{{email}}</strong>.<br>
                If you no longer wish to receive these emails, you can <a href="{{unsubscribe_url}}" style="color: #6366f1;">unsubscribe here</a>.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
            $insTpl = $this->db->prepare("
                INSERT INTO email_templates (name, subject_default, body_html, body_plain, tags)
                VALUES ('Welcome & Onboarding', 'Welcome to our platform, {{first_name}}!', :html, :plain, 'onboarding,transactional')
            ");
            $insTpl->execute([
                ':html' => $welcomeHtml,
                ':plain' => "Hello {{first_name}},\n\nWelcome to our platform. This message was sent to {{email}}.\n\nTo unsubscribe: {{unsubscribe_url}}",
            ]);
        }
    }
}
