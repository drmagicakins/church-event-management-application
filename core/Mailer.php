<?php
/**
 * ============================================================================
 * Mailer - PHPMailer wrapper for transactional email
 * ============================================================================
 * Centralises SMTP configuration so controllers can send mail with one call.
 * Used for:
 *   - Admin notifications (new contact message, donation, prayer request)
 *   - Contact-form auto-reply
 *   - Event registration confirmation
 *   - Password reset links
 *   - Newsletter campaigns
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config['mail'] ?? [];
    }

    /**
     * Send a single email.
     * Returns true on success. On failure, logs the error and returns false.
     */
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $this->config['port'];
            $mail->CharSet    = 'UTF-8';

            // Sender / recipient
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo($this->config['from_email'], $this->config['from_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody ?: strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('[Mailer] ' . $e->getMessage());
            return false;
        }
    }

    /** Convenience: send the same message to many recipients (newsletter). */
    public function broadcast(array $recipients, string $subject, string $htmlBody): array
    {
        $report = ['sent' => 0, 'failed' => 0];
        foreach ($recipients as $r) {
            if ($this->send($r['email'], $r['name'] ?? '', $subject, $htmlBody)) {
                $report['sent']++;
            } else {
                $report['failed']++;
            }
        }
        return $report;
    }

    /** Send an SMTP test email to the admin contact address. */
    public function test(string $adminEmail): bool
    {
        return $this->send(
            $adminEmail,
            'Administrator',
            'SMTP Test - ' . ($this->config['from_name'] ?? 'Team Praise'),
            '<h2>SMTP connection is working.</h2><p>If you received this, PHPMailer SMTP is correctly configured.</p>'
        );
    }
}
