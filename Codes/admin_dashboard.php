<?php
session_start();
include 'config.php';

// Check if user is logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch statistics
$stats = [];

$stats['total_books'] = $db->query("SELECT COUNT(*) FROM books WHERE archived = 0")->fetchColumn();
$stats['archived_books'] = $db->query("SELECT COUNT(*) FROM books WHERE archived = 1")->fetchColumn();
$stats['borrowed_books'] = $db->query("SELECT COUNT(*) FROM borrows WHERE status = 'borrowed'")->fetchColumn();
$stats['total_users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();

/* -------- GRAPH DATA -------- */

// Monthly borrow trend (last 6 months)
$monthlyBorrow = $db->query("
    SELECT DATE_FORMAT(borrow_date, '%b %Y') AS month, COUNT(*) AS total
    FROM borrows
    WHERE borrow_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY MIN(borrow_date)
")->fetchAll(PDO::FETCH_ASSOC);

// Top 5 borrowed books
$topBooks = $db->query("
    SELECT b.title, COUNT(br.id) AS total
    FROM borrows br
    JOIN books b ON br.book_id = b.id
    GROUP BY b.title
    ORDER BY total DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { margin:0; font-family:Arial,sans-serif; padding-bottom:120px; }
header { background:#fff; text-align:center; padding:15px; }
.logo { width:100px; }

nav { background:#00ac0f; padding:10px; text-align:center; }
nav a { color:#fff; margin:0 15px; text-decoration:none; font-weight:bold; }

.dashboard-cards {
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:20px;
    margin:30px;
}

.card {
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
    width:240px;
    text-align:center;
}

.card.large {
    width:520px;
}

canvas {
    height:280px !important;
}

.library-stats {
    background:#f5f8ff;
    padding:50px;
    text-align:center;
}

.stats-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:25px;
    margin-top:30px;
}

.stat-box {
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.1);
}
.stat-box h3 { font-size:36px; color:#2e4bb1; }
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

<div style="text-align:right;margin:20px;">
    <strong><?= ucfirst($_SESSION['role']); ?></strong>
    <a href="logout_confirm.php" style="margin-left:15px;padding:10px 20px;background:#2e4bb1;color:#fff;border-radius:6px;text-decoration:none;">Logout</a>
</div>

<h1 style="text-align:center;">Admin Dashboard</h1>

<!-- SMALL DOUGHNUT CHARTS -->
<div class="dashboard-cards">
    <div class="card"><h3>Total Books</h3><canvas id="totalBooksChart"></canvas></div>
    <div class="card"><h3>Archived Books</h3><canvas id="archivedBooksChart"></canvas></div>
    <div class="card"><h3>Borrowed vs Available</h3><canvas id="borrowedBooksChart"></canvas></div>
    <div class="card"><h3>Total Users</h3><canvas id="totalUsersChart"></canvas></div>
</div>

<!-- BIG GRAPHS -->
<div class="dashboard-cards">
    <div class="card large">
        <h3>Monthly Borrowing Trend</h3>
        <canvas id="monthlyBorrowChart"></canvas>
    </div>

    <div class="card large">
        <h3>Top 5 Most Borrowed Books</h3>
        <canvas id="topBooksChart"></canvas>
    </div>
</div>

<section class="library-stats">
<h2>Library Overview</h2>
<div class="stats-grid">
    <div class="stat-box"><h3><?= $stats['total_books'] ?></h3><p>Available Books</p></div>
    <div class="stat-box"><h3><?= $stats['borrowed_books'] ?></h3><p>Borrowed Books</p></div>
    <div class="stat-box"><h3><?= $stats['archived_books'] ?></h3><p>Archived Books</p></div>
    <div class="stat-box"><h3><?= $stats['total_users'] ?></h3><p>Users</p></div>
</div>
</section>
<!-- Back Button -->
<div style="text-align:center; margin-top:40px;">
    <a href="index.php">
        <button style="
            padding: 12px 25px; 
            background:#2e4bb1; 
            color:white; 
            border:none; 
            border-radius:8px; 
            font-weight:bold; 
            cursor:pointer;
            font-size:16px;
            transition:0.3s;
        " 
        onmouseover="this.style.background='#1d3a8a';" 
        onmouseout="this.style.background='#2e4bb1';">
            ← Back to Home
        </button>
    </a>
</div>
<?php include 'footer.php'; ?>

<script>
const totalBooks = <?= $stats['total_books'] ?>;
const archivedBooks = <?= $stats['archived_books'] ?>;
const borrowedBooks = <?= $stats['borrowed_books'] ?>;
const availableBooks = totalBooks - borrowedBooks;
const totalUsers = <?= $stats['total_users'] ?>;

new Chart(totalBooksChart,{type:'doughnut',data:{datasets:[{data:[totalBooks]}]},options:{plugins:{legend:{display:false}}}});
new Chart(archivedBooksChart,{type:'doughnut',data:{labels:['Archived','Active'],datasets:[{data:[archivedBooks,totalBooks-archivedBooks]}]}});
new Chart(borrowedBooksChart,{type:'doughnut',data:{labels:['Borrowed','Available'],datasets:[{data:[borrowedBooks,availableBooks]}]}});
new Chart(totalUsersChart,{type:'doughnut',data:{datasets:[{data:[totalUsers]}]},options:{plugins:{legend:{display:false}}}});

// LINE GRAPH
new Chart(monthlyBorrowChart,{
    type:'line',
    data:{
        labels:<?= json_encode(array_column($monthlyBorrow,'month')) ?>,
        datasets:[{data:<?= json_encode(array_column($monthlyBorrow,'total')) ?>,fill:true,tension:.4}]
    },
    options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

// BAR GRAPH
new Chart(topBooksChart,{
    type:'bar',
    data:{
        labels:<?= json_encode(array_column($topBooks,'title')) ?>,
        datasets:[{data:<?= json_encode(array_column($topBooks,'total')) ?>}]
    },
    options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>

</body>
</html>
