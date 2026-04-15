<?php
session_start();
require_once 'config.php';

if (!isset($_GET['token'])) {
    die("Invalid verification link");
}

$token = $_GET['token'];

try {
    // Check if token exists
    $stmt = $db->prepare("SELECT email FROM email_verification WHERE token = ?");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Invalid or expired token.");
    }

    $email = $row['email'];

    // Save verified email in session
    $_SESSION['email_verified'] = $email;

    // Delete token (optional)
    $stmt = $db->prepare("DELETE FROM email_verification WHERE email = ?");
    $stmt->execute([$email]);

    // Redirect to signup with success
    header("Location: signup.php?verified=1");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
