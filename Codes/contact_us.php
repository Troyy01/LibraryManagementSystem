<?php
session_start();
include 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KLD Library Management System - Contact Us</title>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f7fb;
    padding-bottom: 80px;
    position: relative;
  }
  header {
    background-color: #ffffff;
    color: black;
    padding: 15px 30px;
    text-align: center;
  }
  .logo {
    width: 100px;
    height: auto;
  }
  nav {
    background: #00ac0fff;
    display: flex;
    justify-content: center;
    padding: 10px;
  }
  nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-weight: bold;
  }
  nav a:hover {
    text-decoration: underline;
  }
  .content {
    padding: 40px;
    text-align: center;
  }
  .content h1 {
    font-size: 36px;
    color: #1a2b5c;
  }
  .content p {
    font-size: 18px;
    color: #333;
    max-width: 800px;
    margin: 20px auto;
    line-height: 1.6;
  }
  footer {
    background-color: #00ac0fff;
    color: white;
    text-align: center;
    padding: 15px;
    position: fixed;
    bottom: 0;
    width: 100%;
  }
  .contact-box {
    background-color: white;
    max-width: 600px;
    margin: 40px auto;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    text-align: left;
  }
  .contact-box h2 {
    color: #2e4bb1;
    font-size: 24px;
    margin-bottom: 15px;
  }
  .contact-box p {
    font-size: 18px;
    color: #333;
    margin: 8px 0;
  }
  .contact-box a {
    color: #00ac0fff;
    text-decoration: none;
  }
  .contact-box a:hover {
    text-decoration: underline;
  }

  /* ---------- LOGIN/LOGOUT BUTTON ---------- */
  .top-btn, .logout-btn {
    position: absolute;
    top: 20px;
    right: 20px;
  }
  .top-btn a, .logout-btn a {
    padding: 10px 20px;
    background: #2e4bb1;
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 6px;
  }
  .top-btn a:hover, .logout-btn a:hover {
    background: #1d3a8a;
  }
</style>
</head>
<body>

<header>
  <img src="KLD_LOGO.png" alt="KLD Logo" class="logo">
  <h1>Kolehiyo ng Lungsod ng Dasmariñas</h1>
</header>

<nav>
  <a href="index.php">Home</a>
  <a href="about_us.php">About Us</a>
  <a href="dataprivacy.php">Data Privacy</a>
  <a href="contact_us.php">Contact Us</a>
</nav>

<?php if ($isLoggedIn): ?>
<div style="text-align:right; margin:20px; padding:10px;">
    <strong style="margin-right:15px;">
        <?= htmlspecialchars(ucfirst($_SESSION['role'])); ?>
    </strong>

    <a href="logout_confirm.php"
       style="padding:10px 20px; background:#2e4bb1; color:white; border-radius:6px; font-weight:bold; text-decoration:none;">
       Logout
    </a>
</div>
<?php else: ?>
  <div style="text-align:right; margin:20px; padding:10px;">
    <a href="login.php"
       style="padding:10px 20px; background:#2e4bb1; color:white; border-radius:6px; font-weight:bold; text-decoration:none;">
       Login
    </a>
  </div>
<?php endif; ?>

<div class="content">
  <h1>Contact Us</h1>
  <p>
    Have questions, concerns, or suggestions? Feel free to reach out to the KLD Library Management Team 
    through the contact details below.
  </p>

  <div class="contact-box">
    <h2>Contact Information</h2>
    <p><strong>Email:</strong> <a href="mailto:kldlibrarymanagement@kld.edu.ph">kldlibrarymanagement@kld.edu.ph</a></p>
    <p><strong>Institution:</strong> Kolehiyo ng Lungsod ng Dasmariñas</p>
    <p><strong>Address:</strong> Congressional Road, Brgy. Burol Main, City of Dasmariñas, Cavite</p>
  </div>
</div>

<footer>
  <p>KLD Library Management System © 2025 | Contact Us</p>
</footer>

</body>
</html>
