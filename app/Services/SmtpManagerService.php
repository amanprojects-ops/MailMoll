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
     * Configure common SMTP properties and authentication options
     */
    private function configureMailer(PHPMailer $mail, array $account, ?string $authTypeOverride = null): void
    {
        $mail->isSMTP();
        $mail->Host = $account['host'];
        $mail->Port = (int)$account['port'];
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

        // Set AuthType: Default to LOGIN (standard for cPanel, Exim, Postfix, Gmail, SES, Sendgrid)
        $authType = $authTypeOverride ?? (!empty($account['auth_type']) ? strtoupper($account['auth_type']) : 'LOGIN');
        $mail->AuthType = $authType;

        // Permissive SSL options for self-hosted and shared hosting certificates
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    /**
     * Test connection to SMTP server and return diagnostic result
     */
    public function testConnection(array $account): array
    {
        $debugOutput = '';
        $configuredAuthType = !empty($account['auth_type']) ? strtoupper($account['auth_type']) : 'LOGIN';
        $authCandidates = array_unique([$configuredAuthType, 'LOGIN', 'PLAIN', 'CRAM-MD5']);
        $lastError = '';

        foreach ($authCandidates as $authMethod) {
            $mail = new PHPMailer(true);
            $debugOutput = '';

            try {
                $mail->Timeout = 15;
                $this->configureMailer($mail, $account, $authMethod);

                // Capture debug stream
                $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
                $mail->Debugoutput = function ($str, $level) use (&$debugOutput) {
                    $debugOutput .= "[" . date('H:i:s') . "] " . trim($str) . "\n";
                };

                $connected = $mail->smtpConnect();

                if ($connected) {
                    $mail->smtpClose();

                    // If a fallback auth type succeeded, update account in database if it has an ID
                    if (!empty($account['id'])) {
                        SmtpAccount::update((int)$account['id'], ['auth_type' => $authMethod]);
                    }

                    return [
                        'success' => true,
                        'message' => "SMTP Handshake and Authentication successful (Auth: {$authMethod})!",
                        'debug' => $debugOutput,
                        'auth_type' => $authMethod,
                    ];
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'SMTP Error: ' . ($lastError ?: 'Authentication failed with candidate methods.'),
            'debug' => $debugOutput ?: $lastError,
        ];
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
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Timeout = 10;
            $this->configureMailer($mail, $account);

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
