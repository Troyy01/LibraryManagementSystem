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
<title>KLD Library Management System - Data Privacy</title>
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
  .content {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    line-height: 1.6;
  }
  .content h1 {
    text-align: center;
    font-size: 36px;
    color: #1a2b5c;
    margin-bottom: 20px;
  }
  .content h2 {
    color: #2e4bb1;
    margin-top: 25px;
    margin-bottom: 10px;
  }
  .content p, .content li {
    font-size: 16px;
    color: #333;
    margin-bottom: 10px;
  }
  .content ol {
    padding-left: 20px;
    margin-bottom: 20px;
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
  <h1>Data Privacy Policy</h1>

  <p>
    At KLD Library Management System, we are committed to protecting your privacy and ensuring the security of your personal information. This Data Privacy Policy explains how we collect, use, store, and safeguard the information you provide when using our library services and online system. By accessing or using our services, you consent to the practices described in this policy.
  </p>

  <h2>1. Information We Collect</h2>
  <p>We may collect the following types of information from users:</p>
  <ul>
    <li><strong>Personal Identification Information:</strong> Full name, email address, phone number, school ID, and other details necessary for library registration.</li>
    <li><strong>Account Information:</strong> Username, password, and account type (student or faculty).</li>
    <li><strong>Library Transactions:</strong> Borrowing history, due dates, reservations, and fines.</li>
    <li><strong>Technical Information:</strong> IP address, browser type, device information, and activity logs to improve system performance and security.</li>
  </ul>

  <h2>2. Purpose of Data Collection</h2>
  <ul>
    <li>To create and manage user accounts.</li>
    <li>To facilitate book borrowing, reservations, and returns.</li>
    <li>To send notifications regarding overdue items, announcements, or library updates.</li>
    <li>To provide a secure, personalized, and efficient library experience.</li>
    <li>To monitor system usage and improve services.</li>
  </ul>

  <h2>3. Legal Basis for Processing</h2>
  <p>We process your personal information in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173). The collection and use of data are necessary for the performance of library services and for compliance with legal obligations.</p>

  <h2>4. Storage and Security of Data</h2>
  <p>We implement strict technical, administrative, and physical measures to protect your personal data against unauthorized access, loss, misuse, or disclosure. Personal information is stored in secure servers and is only accessible to authorized personnel. We regularly review our security practices to ensure compliance with data protection standards.</p>

  <h2>5. Sharing and Disclosure of Information</h2>
  <p>We do not sell, trade, or rent your personal information to third parties. Information may only be shared in the following circumstances:</p>
  <ul>
    <li>When required by law or legal processes.</li>
    <li>With authorized library staff for operational purposes.</li>
    <li>To protect the rights, property, or safety of our users or the library.</li>
  </ul>

  <h2>6. User Rights</h2>
  <p>Under the Data Privacy Act, users have the following rights:</p>
  <ul>
    <li>Right to Access: You may request access to the personal data we hold about you.</li>
    <li>Right to Rectification: You may request corrections to any inaccurate or incomplete data.</li>
    <li>Right to Erasure: You may request the deletion of your personal data, subject to legal obligations.</li>
    <li>Right to Data Portability: You may request a copy of your personal data in a structured and commonly used format.</li>
    <li>Right to Object or Restrict Processing: You may object to certain processing activities or request restrictions.</li>
  </ul>
  <p>Requests to exercise these rights can be submitted to our library administrator via the contact page. We will respond to your requests in accordance with applicable laws.</p>

  <h2>7. Cookies and Tracking</h2>
  <p>Our system may use cookies or similar technologies to enhance your user experience. These are used for session management, personalization, and analytics. You may choose to disable cookies in your browser; however, this may affect certain functionalities of the system.</p>

  <h2>8. Policy Updates</h2>
  <p>We may update this privacy policy from time to time to reflect changes in our practices, technology, or legal requirements. Users will be notified of significant updates, and continued use of the library system constitutes acceptance of the updated policy.</p>

  <h2>9. Contact Us</h2>
  <p>If you have any questions, concerns, or complaints regarding your personal data or this policy, please contact our library administrator through the <a href="contact_us.php">Contact Page</a> or at <a href="mailto:admin@kld.edu.ph">admin@kld.edu.ph</a>. We are committed to addressing your concerns promptly and transparently.</p>

  <h2>10. Commitment to Privacy</h2>
  <p>Your privacy is of utmost importance to us. We pledge to handle your personal information responsibly and in full compliance with the Data Privacy Act of 2012. By using our library services, you trust us with your information, and we are dedicated to maintaining that trust through secure, fair, and lawful data handling practices.</p>

</div>

<footer>
  <p>KLD Library Management System © 2025 | Data Privacy Policy</p>
</footer>

</body>
</html>
