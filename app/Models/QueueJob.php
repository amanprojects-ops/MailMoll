<?php

namespace App\Models;

use PDO;

class QueueJob extends Model
{
    protected static string $table = 'email_queue';

    /**
     * Atomically claim pending jobs using SQLite IMMEDIATE transaction
     */
    public static function claimBatch(int $limit = 50, string $workerId = 'worker-cli'): array
    {
        $db = self::getDb();

        // Release stale locked jobs (locked for more than 10 minutes)
        $staleThreshold = gmdate('Y-m-d H:i:s', time() - 600);
        $db->exec("
            UPDATE email_queue 
            SET status = 'pending', locked_at = NULL, locked_by = NULL 
            WHERE status = 'processing' AND locked_at < '{$staleThreshold}'
        ");

        $db->beginTransaction();
        try {
            $now = gmdate('Y-m-d H:i:s');
            $sql = "
                SELECT id FROM email_queue
                WHERE (status = 'pending' OR status = 'retry')
                  AND scheduled_at <= :now
                  AND attempts < max_attempts
                ORDER BY priority DESC, id ASC
                LIMIT {$limit}
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([':now' => $now]);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($ids)) {
                $db->commit();
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $lockSql = "
                UPDATE email_queue
                SET status = 'processing', locked_at = ?, locked_by = ?, attempts = attempts + 1
                WHERE id IN ({$placeholders})
            ";
            $lockStmt = $db->prepare($lockSql);
            $lockParams = array_merge([$now, $workerId], $ids);
            $lockStmt->execute($lockParams);

            // Fetch the fully locked jobs
            $fetchSql = "SELECT * FROM email_queue WHERE id IN ({$placeholders})";
            $fetchStmt = $db->prepare($fetchSql);
            $fetchStmt->execute($ids);
            $jobs = $fetchStmt->fetchAll();

            $db->commit();
            return $jobs;

        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function markSent(int $jobId, int $smtpAccountId): void
    {
        self::update($jobId, [
            'status' => 'sent',
            'selected_smtp_id' => $smtpAccountId,
            'locked_at' => null,
            'locked_by' => null,
            'last_error' => null,
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);

        // Record attempt
        $db = self::getDb();
        $ins = $db->prepare("
            INSERT INTO email_attempts (queue_id, smtp_account_id, attempt_number, status_code, response_text, attempted_at)
            VALUES (:qid, :smtp, (SELECT attempts FROM email_queue WHERE id = :qid2), '250', '250 OK - Message accepted for delivery', CURRENT_TIMESTAMP)
        ");
        $ins->execute([':qid' => $jobId, ':smtp' => $smtpAccountId, ':qid2' => $jobId]);
    }

    public static function markRetry(int $jobId, ?int $smtpAccountId, string $error, int $delaySeconds = 300): void
    {
        $nextSchedule = gmdate('Y-m-d H:i:s', time() + $delaySeconds);
        self::update($jobId, [
            'status' => 'retry',
            'selected_smtp_id' => $smtpAccountId,
            'scheduled_at' => $nextSchedule,
            'locked_at' => null,
            'locked_by' => null,
            'last_error' => substr($error, 0, 500),
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);

        if ($smtpAccountId) {
            $db = self::getDb();
            $ins = $db->prepare("
                INSERT INTO email_attempts (queue_id, smtp_account_id, attempt_number, status_code, response_text, attempted_at)
                VALUES (:qid, :smtp, (SELECT attempts FROM email_queue WHERE id = :qid2), '4XX', :err, CURRENT_TIMESTAMP)
            ");
            $ins->execute([':qid' => $jobId, ':smtp' => $smtpAccountId, ':qid2' => $jobId, ':err' => substr($error, 0, 255)]);
        }
    }

    public static function markFailed(int $jobId, ?int $smtpAccountId, string $error): void
    {
        self::update($jobId, [
            'status' => 'failed',
            'selected_smtp_id' => $smtpAccountId,
            'locked_at' => null,
            'locked_by' => null,
            'last_error' => substr($error, 0, 500),
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);

        if ($smtpAccountId) {
            $db = self::getDb();
            $ins = $db->prepare("
                INSERT INTO email_attempts (queue_id, smtp_account_id, attempt_number, status_code, response_text, attempted_at)
                VALUES (:qid, :smtp, (SELECT attempts FROM email_queue WHERE id = :qid2), '5XX', :err, CURRENT_TIMESTAMP)
            ");
            $ins->execute([':qid' => $jobId, ':smtp' => $smtpAccountId, ':qid2' => $jobId, ':err' => substr($error, 0, 255)]);
        }
    }
}
