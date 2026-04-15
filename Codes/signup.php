<?php
require_once 'config.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $school_id = $_POST['school_id'] ?? null;

// Match password
if ($password !== $confirm_password) {
    $error = "❌ Passwords do not match.";
}
// Minimum length
elseif (strlen($password) < 8) {
    $error = "❌ Password must be at least 8 characters long.";
}

    // Email domain
    elseif (!str_ends_with($email, '@kld.edu.ph')) {
        $error = "❌ Email must end with @kld.edu.ph";
    }
    else {
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "❌ Email is already registered.";
        }

        // Admin ID validation BEFORE register
        elseif ($role === "admin") {

            if (!$school_id) {
                $error = "❌ Admin must enter a school ID.";
            } else {
                // Check if school ID is valid
                $stmt = $db->prepare("SELECT id FROM valid_faculty_ids WHERE school_id = ?");
                $stmt->execute([$school_id]);

                if ($stmt->rowCount() === 0) {
                    $error = "❌ Invalid admin ID. You are not recognized as admin.";
                }
            }
        }

        // If no errors → insert user
        if (!isset($error)) {

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $db->prepare("
                INSERT INTO users (firstname, lastname, email, password, role, school_id, is_verified) 
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");

            if ($stmt->execute([$firstname, $lastname, $email, $passwordHash, $role, $school_id])) {
                $success = "✅ Registration successful! You can now log in.";
            } else {
                $error = "❌ Database error: could not register user.";
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
<title>KLD Library Management System - Sign Up</title>
<style>
/* Your existing CSS */
.show-password-container {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
}

.show-password-container input[type="checkbox"] {
    width: auto !important;
    height: auto !important;
    margin: 0;
    transform: scale(1.1); /* optional: slightly bigger checkbox */
}



.button-center {
    text-align: center;
    width: 100%;
}

.button-center button {
    width: 200px; /* keep original button size */
}

body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f4f7fb;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
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

.main {
  flex: 1;
  display: flex;
}

.left {
  width: 50%;
  background-color: #f5f8ff;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  padding: 40px;
}

.left img {
  width: 130px;
  margin-bottom: 15px;
}

.left h1 {
  font-size: 28px;
  margin: 10px 0;
  color: #1a2b5c;
  text-align: center;
}

.login-box {
  width: 100%;
  max-width: 380px;
  margin-top: 25px;
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.login-box h2 {
  font-size: 30px;
  margin-bottom: 20px;
  color: #1a2b5c;
  text-align: center;
}

/* Inputs */
.login-box select,
.login-box input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 14px;
}

/* Buttons */
.login-box button,
.verify-btn {
  padding: 11px 15px;
  background-color: #2e4bb1;
  color: white;
  font-size: 14px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  white-space: nowrap;
}

.login-box button:hover,
.verify-btn:hover {
  background-color: #223a91;
}

/* Row layout for inputs + verify buttons */
.input-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.input-row input {
  flex: 1;
}

.message {
  text-align: center;
  font-size: 14px;
  margin: 5px 0;
}
.message.error { color: red; }
.message.success { color: green; }

label.show-pass {
  display: flex;
  align-items: center;
  font-size: 14px;
  margin-top: 5px;
}

label.show-pass input {
  margin-right: 5px;
}

.right {
  width: 50%;
  background: url('kldbuilding.jpg') no-repeat center center / cover;
}

footer {
  background-color: #00ac0fff;
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
    <img src="KLD_LOGO.png" alt="School Logo">
    <h1>KLD Library Management System</h1>

    <div class="login-box">
      <h2>Sign Up</h2>

      <?php
      if(isset($error)) echo "<p class='message error'>$error</p>";
      if(isset($success)) echo "<p class='message success'>$success</p>";
      ?>

      <form id="signupForm" method="POST" action="">
<input type="hidden" name="role" value="<?php echo $forced_role ?? 'guest'; ?>">

        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" placeholder="Last Name" required>

<!-- FACULTY ID FIELD + VERIFY BUTTON -->
<?php if(isset($forced_role) && $forced_role === 'admin'): ?>
<div class="input-row" id="facultyRow">
    <input type="text" name="school_id" id="school_id" placeholder="Admin ID Number" required>
    <button type="button" id="verifyAdminBtn" class="verify-btn">Verify ID</button>
</div>
<p id="facultyMsg" class="message"></p>
<?php endif; ?>

<p id="facultyMsg" class="message"></p>


<!-- EMAIL FIELD + VERIFY BUTTON -->
<div class="input-row">
    <input type="email" name="email" id="email"
           placeholder="Institutional Email (@kld.edu.ph)" required>
    <button type="button" id="verifyEmailBtn" class="verify-btn">
        Verify Email
    </button>
</div>
<p id="emailMsg" class="message"></p>


        <input type="hidden" id="verifiedEmail" value="<?php echo $_SESSION['email_verified'] ?? ''; ?>">

        <input type="password" name="password" id="password" placeholder="Password" required>
        <p id="passwordLengthMsg" class="message"></p>

        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
        <p id="matchMsg" class="message"></p>
<div class="show-password-container">
    <input type="checkbox" id="showPassword" onclick="togglePassword()">
    <label for="showPassword">Show Password</label>
</div>

<div class="button-center">
    <button type="submit" name="signup">Sign Up</button>

    <?php if (!isset($forced_role) || $forced_role !== 'admin'): ?>
    <div style="margin-top: 10px;">
        <a href="faculty_signup.php">
            <button type="button" style="width:200px;background:#444;">
                Admin Signup
            </button>
        </a>
    </div>
    <?php endif; ?>
    <?php if(isset($forced_role) && $forced_role === 'admin'): ?>
<div style="margin-top: 10px; text-align: center;">
    <a href="signup.php">
        <button type="button" style="width:200px; background:#444;">
            Guest Signup
        </button>
    </a>
</div>
<?php endif; ?>
</div>


      </form>

      <div class="links">
        <p>Already have an account? <a href="login.php">Log in</a></p>
      </div>
    </div>
  </div>

  <div class="right"></div>
</div>

<footer>
  <p>KLD Library Management System © 2025 | Sign Up</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Show/hide password
    $('#showPassword').on('change', function() {
        const type = this.checked ? "text" : "password";
        $('#password, #confirm_password').attr('type', type);
    });

    // Password match check
    function checkMatch() {
        if (!$('#confirm_password').val()) { $('#matchMsg').text(''); return; }
        if ($('#password').val() === $('#confirm_password').val()) {
            $('#matchMsg').text('✔ Passwords match').css('color','green');
        } else {
            $('#matchMsg').text('✘ Passwords do not match').css('color','red');
        }
    }
    $('#password, #confirm_password').on('input', checkMatch);


    // Verify Email Button - send verification link
    $('#verifyEmailBtn').click(function(){
        const email = $('#email').val().trim();
        if(!email.endsWith("@kld.edu.ph")) {
            $('#emailMsg').text("✘ Email must end with @kld.edu.ph").css('color','red');
            return;
        }
        $.ajax({
            url: 'verify_email.php',
            type: 'POST',
            data: { email: email },
            success: function(data){
                if(data === 'sent'){
                    $('#emailMsg').html('✔ Verification link sent to your email. Check your inbox.').css('color','green');
                } else {
                    $('#emailMsg').text(data).css('color','red');
                }
            }
        });
    });

});




$(document).ready(function() {

    // Verify Admin ID Button
    $('#verifyAdminBtn').click(function(){
        const school_id = $('#school_id').val().trim();

        if (!school_id) {
            $('#facultyMsg').text("✘ Please enter your admin ID first.").css('color','red');
            return;
        }

        $.ajax({
            url: 'verify_faculty.php',
            type: 'POST',
            data: { school_id: school_id },
            success: function(data){
                if (data === 'valid') {
                    $('#facultyMsg').text("✔ Admin ID verified.").css('color','green');
                } else {
                    $('#facultyMsg').text("✘ Invalid Admin ID.").css('color','red');
                }
            }
        });
    });

});

// Enforce minimum password length
$('#password').on('input', function() {
    const pwd = $(this).val();

    if (pwd.length < 8) {
        $('#matchMsg').text('✘ Password must be at least 8 characters long').css('color','red');
    } else {
        $('#matchMsg').text('');
    }
});
// Password minimum length check (8 characters)
$('#password').on('input', function () {
    const pwd = $(this).val();

    if (pwd.length === 0) {
        $('#passwordLengthMsg').text('');
        return;
    }

    if (pwd.length < 8) {
        $('#passwordLengthMsg')
            .text('✘ Password must be at least 8 characters long')
            .css('color', 'red');
    } else {
        $('#passwordLengthMsg')
            .text('✔ Password length is valid')
            .css('color', 'green');
    }
});

</script>

</body>
</html>
