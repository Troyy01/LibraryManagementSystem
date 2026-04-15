<?php
session_start();
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (!isset($_POST['email'])) {
    echo "Invalid request";
    exit;
}

$email = trim($_POST['email']);

if (!str_ends_with($email, '@kld.edu.ph')) {
    echo "Invalid institutional email";
    exit;
}

// Generate token
$token = bin2hex(random_bytes(32));

// Save or update token
$stmt = $db->prepare("
    INSERT INTO email_verification (email, token)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE token = VALUES(token)
");
$stmt->execute([$email, $token]);

// Build verification link
$verifyLink = "http://{$_SERVER['HTTP_HOST']}/verify.php?token=$token";

/* ----------------------------
   #1 — Return response FAST
----------------------------- */

function fast_exit($msg = "sent") {
    // Send response immediately
    echo $msg;

    // Flush buffer
    if (ob_get_length()) ob_end_flush();
    flush();

    // Continue running script while browser disconnects
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    // DO NOT exit, we want code after this to run in background
}

fast_exit("sent");

/* ----------------------------------------------------
   #2 — PHPMailer continues executing in background
----------------------------------------------------- */

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'troymontaril23@gmail.com';
    $mail->Password   = 'rcmv kufe vqbl abfr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // SPEED UP SENDING
    $mail->SMTPKeepAlive = true; // does not reconnect every time
    $mail->Timeout = 10;         // prevent long delays

    $mail->setFrom('troymontaril23@gmail.com', 'KLD Email Verification');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "KLD Email Verification";
    $mail->Body    = "
        Hello!<br><br>
        Click the link below to verify your email:<br><br>
        <a href='$verifyLink'>$verifyLink</a><br><br>
        If you did not request this, ignore this message.
    ";

    $mail->send();

} catch (Exception $e) {
    error_log("Email send failure: {$mail->ErrorInfo}");
}
