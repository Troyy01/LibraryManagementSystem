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
<title>KLD Library Management System - About Us</title>
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
  .section {
    background-color: #fff;
    margin: 40px auto;
    max-width: 900px;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    text-align: left;
  }
  .section h2 {
    color: #2e4bb1;
    font-size: 28px;
    margin-bottom: 15px;
    text-align: center;
  }
  .developer-card {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    background-color: #f5f8ff;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
  }
  .developer-card img {
    width: 120px;
    height: 120px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 20px;
    border: 2px solid #2e4bb1;
  }
  .developer-info {
    text-align: left;
  }
  .developer-info h3 {
    color: #1a2b5c;
    font-size: 22px;
    margin-bottom: 8px;
  }
  .developer-info p {
    font-size: 16px;
    color: #333;
    margin: 5px 0;
  }
  .developer-info a {
    color: #00ac0fff;
    text-decoration: none;
  }
  .developer-info a:hover {
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
  <h1>About Us</h1>

  <div class="section">
    <h2>Company Profile</h2>
    <p>
      The <strong>KLD Library Management System</strong> is a digital platform developed for the
      <strong>Kolehiyo ng Lungsod ng Dasmariñas</strong> to enhance and modernize library services.
      This system helps students, faculty, and staff easily search for books, check availability,
      and manage borrowing records online. Our goal is to make library operations faster,
      more organized, and accessible to all users, supporting the institution's commitment to
      innovation and quality education.
    </p>
  </div>

  <div class="section">
    <h2>Developer Profile</h2>
    <div class="developer-card">
      <img src="DSC_2775.jpg" alt="Montaril">
      <div class="developer-info">
        <h3>Allan Troy Montaril</h3>
        <p><strong>Contact No:</strong> #09161519732</p>
        <p><strong>Email:</strong> <a href="mailto:atmontaril@kld.edu.ph">atmontaril@kld.edu.ph</a></p>
        <p><strong>Role:</strong> Front End Developer</p>
      </div>
    </div>    

    <div class="developer-card">
      <img src="sk.jpg" alt="Nicodemus">
      <div class="developer-info">
        <h3>Danielle Nicodemus </h3>
        <p><strong>Contact No:</strong> #09972207264</p>
        <p><strong>Email:</strong> <a href="mailto:dnicodemus@kld.edu.ph">dnicodemus@kld.edu.ph</a></p>
        <p><strong>Role:</strong> Admin Developer</p>
      </div>
    </div> 
    
    <div class="developer-card">
      <img src="kc.jpg" alt="Nabayra">
      <div class="developer-info">
        <h3>KC Nabayra</h3>
        <p><strong>Contact No:</strong> #09772608467</p>
        <p><strong>Email:</strong> <a href="mailto:knabayra@kld.edu.ph">knabayra@kld.edu.ph</a></p>
        <p><strong>Role:</strong> Business Process Manager</p>
      </div>
    </div>    

    <div class="developer-card">
      <img src="jm.jpg" alt="Mateo">
      <div class="developer-info">
        <h3>Johnrommel Mateo</h3>
        <p><strong>Contact No:</strong> #</p>
        <p><strong>Email:</strong> <a href="mailto:jmateo@kld.edu.ph">jmateo@kld.edu.ph</a></p>
        <p><strong>Role:</strong> System Analyst</p>
      </div>
    </div>    
  </div>
</div>

<footer>
  <p>KLD Library Management System © 2025 | About Us</p>
</footer>

</body>
</html>
