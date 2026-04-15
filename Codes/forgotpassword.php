<?php
require 'config.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = '';
$show_reset = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // STEP 1: Send verification code
    if (isset($_POST['action']) && $_POST['action'] === 'send_code') {
        $email = trim($_POST['email']);

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = "❌ Email not found.";
        } else {
            // Delete old codes
            $db->prepare("DELETE FROM password_resets WHERE user_id=?")->execute([$user['id']]);

            $code = str_pad(rand(0, 9999), 4, "0", STR_PAD_LEFT);
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $stmt = $db->prepare("INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $code, $expires]);

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'troymontaril23@gmail.com'; // your email
                $mail->Password = 'rcmv kufe vqbl abfr'; // app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('troymontaril23@gmail.com', 'KLD Library');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your Password Reset Code';
                $mail->Body = "Your password reset code is <b>$code</b>. It expires in 15 minutes.";
                $mail->send();

                $_SESSION['reset_email'] = $email;
                $message = "✔ Verification code sent. Enter the code below with your new password.";
                $show_reset = true;

            } catch (Exception $e) {
                $message = "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }

    // STEP 2: Verify code & reset password
    if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
        $email = $_SESSION['reset_email'] ?? null;
        $code = trim($_POST['code']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if (!$email) {
            $message = "❌ Session expired. Start again.";
        } elseif ($password !== $confirm) {
            $message = "❌ Passwords do not match.";
            $show_reset = true;
        } else {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $message = "❌ Invalid email.";
            } else {
                $stmt = $db->prepare("SELECT * FROM password_resets WHERE user_id=? AND code=? AND expires_at>=NOW() ORDER BY created_at DESC LIMIT 1");
                $stmt->execute([$user['id'], $code]);
                $reset = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$reset) {
                    $message = "❌ Invalid or expired code.";
                    $show_reset = true;
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $user['id']]);
                    $db->prepare("DELETE FROM password_resets WHERE user_id=?")->execute([$user['id']]); // Remove all codes
                    unset($_SESSION['reset_email']);
                    $message = "✔ Password reset successfully! <a href='login.php'>Log in</a>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - KLD Library</title>
<style>
body { font-family:Arial,sans-serif; background:#f4f7fb; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:#fff; padding:30px; border-radius:10px; width:350px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
input { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px; }
button { width:100%; padding:10px; background:#2e4bb1; color:white; border:none; border-radius:6px; cursor:pointer; margin-top:10px; }
button:hover { background:#223a91; }
.message { font-size:14px; margin:5px 0; text-align:center; }
.message.error { color:red; }
.message.success { color:green; }
</style>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <?php if($message) echo "<p class='message ".($show_reset?'success':'error')."'>$message</p>"; ?>

    <?php if(!$show_reset): ?>
    <form method="POST">
        <input type="hidden" name="action" value="send_code">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Verification Code</button>
    </form>
    <?php else: ?>
    <form method="POST">
        <input type="hidden" name="action" value="reset_password">
        <input type="text" name="code" placeholder="Enter 4-digit code" maxlength="4" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>

    <form action="login.php" method="get" style="margin-top:10px;">
        <button type="submit" style="background:#555;">← Back to Login</button>
    </form>
</div>

</body>
</html>
