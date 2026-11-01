<?php

namespace App\Controllers;

use App\Helpers\View;
use App\Helpers\Response;
use App\Database\Connection;
use App\Models\SmtpAccount;
use App\Models\Campaign;

class AnalyticsController
{
    public function index(): void
    {
        View::render('analytics/index', [
            'title' => 'Analytics & Reporting',
            'activeNav' => 'analytics',
        ]);
    }

    public function apiData(): void
    {
        $db = Connection::getInstance();

        // 1. Last 14 days daily delivery statistics
        $dailySql = "
            SELECT 
                DATE(created_at) as date_val,
                SUM(CASE WHEN event_type = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN event_type = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM delivery_events
            WHERE created_at >= datetime('now', '-14 days')
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ";
        $dailyRows = $db->query($dailySql)->fetchAll();

        // Generate full 14 day sequence
        $dailyLabels = [];
        $dailyAccepted = [];
        $dailyRejected = [];

        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $dailyLabels[] = date('M d', strtotime($d));

            $match = null;
            foreach ($dailyRows as $r) {
                if ($r['date_val'] === $d) {
                    $match = $r;
                    break;
                }
            }

            $dailyAccepted[] = $match ? (int)$match['accepted'] : 0;
            $dailyRejected[] = $match ? (int)$match['rejected'] : 0;
        }

        // 2. SMTP Account Usage Distribution
        $smtpSql = "
            SELECT s.name, SUM(u.sent_count) as total_sent
            FROM smtp_accounts s
            LEFT JOIN smtp_usage u ON s.id = u.smtp_account_id
            GROUP BY s.id
            ORDER BY total_sent DESC
        ";
        $smtpStats = $db->query($smtpSql)->fetchAll();
        $smtpLabels = [];
        $smtpCounts = [];
        foreach ($smtpStats as $st) {
            $smtpLabels[] = $st['name'];
            $smtpCounts[] = (int)($st['total_sent'] ?? 0);
        }

        // 3. Campaign Comparison
        $campSql = "
            SELECT title, sent_count, failed_count
            FROM campaigns
            WHERE total_recipients > 0
            ORDER BY id DESC
            LIMIT 5
        ";
        $campaigns = $db->query($campSql)->fetchAll();

        Response::json([
            'daily' => [
                'labels' => $dailyLabels,
                'accepted' => $dailyAccepted,
                'rejected' => $dailyRejected,
            ],
            'smtp' => [
                'labels' => $smtpLabels,
                'counts' => $smtpCounts,
            ],
            'campaigns' => $campaigns,
        ]);
    }
}
