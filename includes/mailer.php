<?php
// includes/mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// Load .env (safe: .env should be at project root)
$dotenvPath = dirname(__DIR__); // adjust if your .env is in another place
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

// Fallback to getenv() if you prefer system env
$smtpHost = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com';
$smtpPort = $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587;
$smtpEnc  = $_ENV['SMTP_ENCRYPTION'] ?? getenv('SMTP_ENCRYPTION') ?? 'tls';
$smtpUser = $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME') ?? '';
$smtpPass = $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?? '';
$from     = $_ENV['SMTP_FROM'] ?? getenv('SMTP_FROM') ?? $smtpUser;
$fromName = $_ENV['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME') ?? 'SUNSWEEP';

function send_mail($to, $subject, $bodyHtml, $bodyText = '') {
    global $smtpHost, $smtpPort, $smtpEnc, $smtpUser, $smtpPass, $from, $fromName;

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = $smtpEnc; // 'tls' or 'ssl'
        $mail->Port       = (int)$smtpPort;

        // Recipients
        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = $bodyText ?: strip_tags($bodyHtml);

        $mail->send();
        return ['ok' => true, 'msg' => 'Message sent'];
    } catch (Exception $e) {
        return ['ok' => false, 'msg' => $mail->ErrorInfo];
    }
}
