<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isFaculty  = ($isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'faculty');
?>
<style>
footer {
  background-color: #00ac0fff;
  color: white;
  text-align: center;
  padding: 15px;
  width: 100%;
  position: fixed;   /* keep it fixed if you insist */
  bottom: 0;
}

body {
  padding-bottom: 80px; /* prevents footer from covering buttons */
}
</style>


<footer>
    <p>KLD Library Management System © 2025 | Index </p>
</footer>
<!-- LOGIN / LOGOUT BUTTON -->
<?php if (!$isLoggedIn): ?>
  <div class="top-btn">
    <a href="login.php">Log In</a>
  </div>
<?php endif; ?>

<?php if ($isLoggedIn): ?>
  <div class="logout-btn">
    <span style="margin-right: 10px; font-weight: bold;">
      <?= ucfirst($_SESSION['role']); ?>
    </span>
    <a href="logout.php">Logout</a>
  </div>
<?php endif; ?>

