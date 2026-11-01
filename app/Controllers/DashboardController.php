<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Models\SmtpAccount;
use App\Models\QueueJob;
use App\Models\Campaign;
use App\Models\DeliveryLog;
use App\Models\Suppression;
use App\Models\AppSetting;
use App\Database\Connection;

class DashboardController
{
    public function index(): void
    {
        $db = Connection::getInstance();

        $totalSmtp = SmtpAccount::count();
        $activeSmtp = SmtpAccount::count("status = 'active'");

        $queueStats = $db->query("
            SELECT 
                SUM(CASE WHEN status IN ('pending', 'processing', 'retry') THEN 1 ELSE 0 END) as queued,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM email_queue
        ")->fetch();

        $queued = (int)($queueStats['queued'] ?? 0);
        $sent = (int)($queueStats['sent'] ?? 0);
        $failed = (int)($queueStats['failed'] ?? 0);

        $totalProcessed = $sent + $failed;
        $deliveryRate = $totalProcessed > 0 ? round(($sent / $totalProcessed) * 100, 1) : 100.0;
        $bounces = Suppression::count("reason = 'hard_bounce'");

        // Today's stats
        $today = gmdate('Y-m-d');
        $todayUsage = (int)$db->query("
            SELECT SUM(sent_count) FROM smtp_usage WHERE year_month_day = '{$today}'
        ")->fetchColumn();

        // Recent Delivery Events
        $recentEvents = $db->query("
            SELECT e.*, c.title as campaign_title, s.name as smtp_name
            FROM delivery_events e
            LEFT JOIN campaigns c ON e.campaign_id = c.id
            LEFT JOIN smtp_accounts s ON e.smtp_account_id = s.id
            ORDER BY e.id DESC
            LIMIT 8
        ")->fetchAll();

        // Active SMTP Accounts with Quota info
        $smtpAccounts = SmtpAccount::all('priority DESC, id ASC', 5);
        foreach ($smtpAccounts as &$acc) {
            $acc['daily_used'] = SmtpAccount::getDailyUsage((int)$acc['id']);
            $acc['hourly_used'] = SmtpAccount::getHourlyUsage((int)$acc['id']);
            $acc['daily_percent'] = $acc['daily_limit'] > 0 ? min(100, round(($acc['daily_used'] / $acc['daily_limit']) * 100)) : 0;
        }

        // Active Campaigns
        $activeCampaigns = Campaign::all('id DESC', 5);

        // Worker status
        $workerHeartbeat = AppSetting::get('worker_heartbeat');
        $workerActive = false;
        if ($workerHeartbeat) {
            $diffSeconds = time() - strtotime($workerHeartbeat);
            $workerActive = ($diffSeconds <= 120);
        }

        View::render('dashboard/index', [
            'title' => 'Dashboard Overview',
            'activeNav' => 'overview',
            'totalSmtp' => $totalSmtp,
            'activeSmtp' => $activeSmtp,
            'queued' => $queued,
            'sent' => $sent,
            'failed' => $failed,
            'bounces' => $bounces,
            'deliveryRate' => $deliveryRate,
            'todayUsage' => $todayUsage,
            'recentEvents' => $recentEvents,
            'smtpAccounts' => $smtpAccounts,
            'activeCampaigns' => $activeCampaigns,
            'workerActive' => $workerActive,
            'workerHeartbeat' => $workerHeartbeat,
        ]);
    }
}
