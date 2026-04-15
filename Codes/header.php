<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safely check login and role

?>
<!DOCTYPE html>
<html>
<head>
<style>
/* ---------- HEADER ---------- */
header {
  background-color: #fff;
  color: black;
  padding: 15px 30px;
  text-align: center;
}

.logo {
  width: 100px;
}

/* ---------- NAVBAR ---------- */
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


</style>
</head>
<body>

<header>
  <img src="KLD_LOGO.png" class="logo">
  <h1>Kolehiyo ng Lungsod ng Dasmariñas</h1>
</header>



<nav>
  <a href="index.php">Home</a>
  <a href="about_us.php">About Us</a>
  <a href="dataprivacy.php">Data Privacy</a>
  <a href="contact_us.php">Contact Us</a>
</nav>
