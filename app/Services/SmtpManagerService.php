<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use App\Models\SmtpAccount;

class SmtpManagerService
{
    private CryptoService $crypto;

    public function __construct()
    {
        $this->crypto = new CryptoService();
    }

    /**
     * Test connection to SMTP server and return diagnostic result
     */
    public function testConnection(array $account): array
    {
        $mail = new PHPMailer(true);
        $debugOutput = '';

        try {
            $mail->isSMTP();
            $mail->Host = $account['host'];
            $mail->Port = (int)$account['port'];
            $mail->Timeout = 15;
            $mail->SMTPAuth = true;
            $mail->Username = $account['username'];

            $password = $account['password'] ?? $this->crypto->decrypt($account['password_encrypted'] ?? '');
            $mail->Password = $password ?? '';

            $encryption = strtolower($account['encryption'] ?? 'tls');
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $encryption === 'starttls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            // Capture debug stream
            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $mail->Debugoutput = function ($str, $level) use (&$debugOutput) {
                $debugOutput .= "[" . date('H:i:s') . "] " . trim($str) . "\n";
            };

            // Test connection using PHPMailer's smtpConnect
            $connected = $mail->smtpConnect();

            if ($connected) {
                $mail->smtpClose();
                return [
                    'success' => true,
                    'message' => 'SMTP Handshake and Authentication successful!',
                    'debug' => $debugOutput,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'SMTP connection failed to initialize.',
                    'debug' => $debugOutput,
                ];
            }

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'SMTP Error: ' . $e->getMessage(),
                'debug' => $debugOutput ?: $e->getMessage(),
            ];
        }
    }

    /**
     * Send email via specific SMTP account
     */
    public function send(
        array $account,
        string $toEmail,
        ?string $toName,
        string $subject,
        string $bodyHtml,
        ?string $bodyPlain = null,
        array $customHeaders = []
    ): array {
        $mail = new PHPMailer(true);
        $startTime = microtime(true);

        try {
            $mail->isSMTP();
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Host = $account['host'];
            $mail->Port = (int)$account['port'];
            $mail->Timeout = 25;
            $mail->SMTPAuth = true;
            $mail->Username = $account['username'];

            $password = $this->crypto->decrypt($account['password_encrypted'] ?? '');
            $mail->Password = $password ?? '';

            $encryption = strtolower($account['encryption'] ?? 'tls');
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $encryption === 'starttls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }

            // Sender details
            $mail->setFrom($account['from_email'], $account['from_name']);
            if (!empty($account['reply_to'])) {
                $mail->addReplyTo($account['reply_to'], $account['from_name']);
            }

            // Recipient
            $mail->addAddress($toEmail, $toName ?? '');

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $bodyHtml;
            $mail->AltBody = $bodyPlain ?: strip_tags($bodyHtml);

            // Custom Headers (RFC-8058 List-Unsubscribe, etc.)
            foreach ($customHeaders as $hKey => $hVal) {
                $mail->addCustomHeader($hKey, $hVal);
            }

            $mail->send();
            $duration = (int)((microtime(true) - $startTime) * 1000);

            return [
                'success' => true,
                'duration_ms' => $duration,
                'message' => '250 2.0.0 OK Message accepted',
            ];

        } catch (PHPMailerException $e) {
            $duration = (int)((microtime(true) - $startTime) * 1000);
            $err = $mail->ErrorInfo ?: $e->getMessage();

            // Detect permanent vs transient error
            $isPermanent = (bool)preg_match('/5\d{2}|invalid address|mailbox unavailable|user unknown|recipient rejected/i', $err);

            return [
                'success' => false,
                'is_permanent' => $isPermanent,
                'duration_ms' => $duration,
                'message' => $err,
            ];
        } catch (\Throwable $e) {
            $duration = (int)((microtime(true) - $startTime) * 1000);
            return [
                'success' => false,
                'is_permanent' => false,
                'duration_ms' => $duration,
                'message' => $e->getMessage(),
            ];
        }
    }
}
