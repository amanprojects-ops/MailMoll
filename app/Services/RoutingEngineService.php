<?php

namespace App\Services;

use App\Models\SmtpAccount;
use App\Models\SmtpGroup;

class RoutingEngineService
{
    /**
     * Select eligible SMTP account based on campaign routing configuration
     */
    public function selectSmtpAccount(array $campaign): ?array
    {
        $routingType = $campaign['routing_type'] ?? 'single_smtp';

        if ($routingType === 'single_smtp' && !empty($campaign['smtp_account_id'])) {
            $account = SmtpAccount::find((int)$campaign['smtp_account_id']);
            if ($account && $account['status'] === 'active' && $this->hasAvailableQuota($account)) {
                return $account;
            }
            return null;
        }

        // Routing via SMTP Group
        $groupId = (int)($campaign['smtp_group_id'] ?? 0);
        $group = SmtpGroup::find($groupId);
        if (!$group) {
            return null;
        }

        $members = SmtpGroup::getMembers($groupId);
        // Filter to only active accounts that have available quota
        $eligible = array_filter($members, function ($acc) {
            return $acc['status'] === 'active' && $this->hasAvailableQuota($acc);
        });

        if (empty($eligible)) {
            return null; // All accounts at quota or inactive
        }

        $strategy = $group['routing_strategy'] ?? 'round_robin';

        return match ($strategy) {
            'weighted' => $this->selectWeighted($eligible),
            'least_used' => $this->selectLeastUsed($eligible),
            'priority' => $this->selectPriority($eligible),
            default => $this->selectRoundRobin($eligible),
        };
    }

    /**
     * Check whether account is within hourly and daily limits
     */
    public function hasAvailableQuota(array $account): bool
    {
        $hourlyUsage = SmtpAccount::getHourlyUsage((int)$account['id']);
        if ($account['hourly_limit'] > 0 && $hourlyUsage >= $account['hourly_limit']) {
            return false;
        }

        $dailyUsage = SmtpAccount::getDailyUsage((int)$account['id']);
        if ($account['daily_limit'] > 0 && $dailyUsage >= $account['daily_limit']) {
            return false;
        }

        return true;
    }

    private function selectRoundRobin(array $accounts): array
    {
        $accounts = array_values($accounts);
        static $counter = 0;
        $selected = $accounts[$counter % count($accounts)];
        $counter++;
        return $selected;
    }

    private function selectWeighted(array $accounts): array
    {
        $totalWeight = array_sum(array_column($accounts, 'weight'));
        if ($totalWeight <= 0) {
            return reset($accounts);
        }

        $rand = mt_rand(1, $totalWeight);
        $current = 0;

        foreach ($accounts as $acc) {
            $current += (int)($acc['weight'] ?: 1);
            if ($rand <= $current) {
                return $acc;
            }
        }

        return reset($accounts);
    }

    private function selectLeastUsed(array $accounts): array
    {
        usort($accounts, function ($a, $b) {
            $usedA = SmtpAccount::getDailyUsage((int)$a['id']);
            $usedB = SmtpAccount::getDailyUsage((int)$b['id']);
            return $usedA <=> $usedB;
        });

        return reset($accounts);
    }

    private function selectPriority(array $accounts): array
    {
        usort($accounts, function ($a, $b) {
            // Higher priority first
            return (int)$b['priority'] <=> (int)$a['priority'];
        });

        return reset($accounts);
    }
}
