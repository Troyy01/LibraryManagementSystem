<?php
require_once('config.php');
session_start();

// If user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists & password correct
    if ($user && password_verify($password, $user['password'])) {

        // OPTIONAL: block accounts until email verification
        // if ($user['is_verified'] == 0) {
        //     $message = "Please verify your email before logging in.";
        // } else {

        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit();

        // }
        
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KLD Library Management System - Login</title>
<style>
/* ---------- GLOBAL ---------- */
/* ---------- GLOBAL ---------- */
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f4f7fb;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ---------- NAVBAR ---------- */
nav {
  background: #00ac0f;
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

/* ---------- MAIN CONTENT ---------- */
.main {
  flex: 1;
  display: flex;
}

.left {
  width: 50%;
  background-color: #f5f8ff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 40px;
}
.left h1 {
  font-size: 28px;
  margin: 5px 0;
  color: #1a2b5c;
}

/* ---------- LOGIN BOX ---------- */
.login-box {
  width: 100%;
  max-width: 350px;
  margin-top: 20px;
}
.login-box h2 {
  font-size: 32px;
  margin-bottom: 20px;
  color: #1a2b5c;
}

.input-group input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}

.show-password-container {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: -5px;
  margin-bottom: 10px;
}

/* ---------- BUTTON ---------- */
.login-box button {
  width: 100%;
  padding: 12px;
  background-color: #2e4bb1;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}
.login-box button:hover {
  background-color: #1d3a8a;
}

/* ---------- LINKS ---------- */
.links {
  margin-top: 15px;
  font-size: 14px;
  text-align: center;
}
.links a {
  color: #2e4bb1;
  text-decoration: none;
  margin: 0 5px;
}
.links a:hover {
  text-decoration: underline;
}

/* ---------- RIGHT SIDE IMAGE ---------- */
.right {
  width: 50%;
  background: url('kldbuilding.jpg') no-repeat center center / cover;
}

/* ---------- MESSAGE ---------- */
.message {
  color: red;
  font-size: 14px;
  margin-top: 10px;
  text-align: center;
}

/* ---------- FOOTER ---------- */
footer {
  background-color: #00ac0f;
  color: white;
  text-align: center;
  padding: 15px;
}

</style>
</head>
<body>

<nav>
  <a href="index.php">Home</a>
  <a href="about_us.php">About Us</a>
  <a href="dataprivacy.php">Data Privacy</a>
  <a href="contact_us.php">Contact Us</a>
</nav>

<div class="main">
  <div class="left">
    <h1>KLD Library Management System</h1>
<div class="login-box">
    <h2>Log in.</h2>

    <?php if (!empty($message)): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <input type="password" id="login_password" name="password" placeholder="Password" required>
        </div>

        <div class="show-password-container">
            <input type="checkbox" id="showLoginPassword" onclick="toggleLoginPassword()">
            <label for="showLoginPassword">Show Password</label>
        </div>

        <button type="submit">Log in</button>

    </form>

    <div class="links">
        <p>Don't have an account? <a href="signup.php">Sign up.</a></p>
        <p><a href="forgotpassword.php">Forgot password?</a></p>
    </div>
</div>


  </div>

  <div class="right"></div>
</div>

<footer>
  <p>KLD Library Management System © 2025 | Login</p>
</footer>
<script>
function toggleLoginPassword() {
    const pass = document.getElementById("login_password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>


</body>
</html>
