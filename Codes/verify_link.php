<?php
session_start();

if(isset($_GET['token'], $_GET['email'])){
    $token = $_GET['token'];
    $email = $_GET['email'];

    if(isset($_SESSION['email_token'], $_SESSION['email_to_verify']) &&
       $token === $_SESSION['email_token'] &&
       $email === $_SESSION['email_to_verify']) {

        // Email verified
        $_SESSION['email_verified'] = $email;

        // Remove token so it can't be reused
        unset($_SESSION['email_token']);
        unset($_SESSION['email_to_verify']);

        echo "<p style='color:green; text-align:center;'>✔ Email verified! You can now go back to Sign Up.</p>";
        echo "<p style='text-align:center;'><a href='signup.php'>Back to Sign Up</a></p>";
    } else {
        echo "<p style='color:red; text-align:center;'>✘ Invalid or expired link.</p>";
    }
}
?>
