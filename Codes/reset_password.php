<?php
require 'config.php';
session_start();

$message = '';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "❌ Passwords do not match.";
    } else {
        // Get user ID
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = "❌ Invalid email.";
        } else {
            // Verify code
            $stmt = $db->prepare("SELECT * FROM password_resets WHERE user_id=? AND code=? AND expires_at>=NOW() ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$user['id'], $code]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$reset) {
                $message = "❌ Invalid or expired code.";
            } else {
                // Update password
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $user['id']]);

                // Mark code as used
                $db->prepare("UPDATE password_resets SET is_verified=1 WHERE id=?")->execute([$reset['id']]);

                unset($_SESSION['reset_email']);
                $message = "✔ Password successfully reset! <a href='login.php'>Log in</a>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
<h2>Reset Password</h2>
<?php if($message) echo "<p>$message</p>"; ?>
<form method="POST">
    <input type="text" name="code" placeholder="Enter 4-digit code" required><br><br>
    <input type="password" name="password" placeholder="New Password" required><br><br>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br><br>
    <button type="submit">Reset Password</button>
</form>
</body>
</html>
