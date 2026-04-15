<?php
session_start();
include 'config.php';


$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Featured Books
$featuredBooks = $db->query("
    SELECT title, author, category
    FROM books
    WHERE archived = 0 AND featured = 1
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$stats = [];
if ($isLoggedIn && $isAdmin) {
    // Total books (not archived)
    $stats['total_books'] = $db->query("SELECT COUNT(*) FROM books WHERE archived = 0")->fetchColumn();

    // Total archived books
    $stats['archived_books'] = $db->query("SELECT COUNT(*) FROM books WHERE archived = 1")->fetchColumn();

    // Total borrowed books
    $stats['borrowed_books'] = $db->query("SELECT COUNT(*) FROM borrows WHERE status = 'borrowed'")->fetchColumn();

    // Total users
    $stats['total_users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
}


/* --------------------------
   BOOK ADDING (FACULTY ONLY)
--------------------------- */
if ($isLoggedIn && $isAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']); // new
    $quantity = (int)$_POST['quantity'];

    $check = $db->prepare("SELECT * FROM books WHERE title = :title AND author = :author AND archived = 0 LIMIT 1");
    $check->execute([':title' => $title, ':author' => $author]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $newQuantity = $existing['quantity'] + $quantity;
        $update = $db->prepare("UPDATE books SET quantity = :quantity WHERE id = :id");
        $update->execute([':quantity' => $newQuantity, ':id' => $existing['id']]);
    } else {
$insert = $db->prepare("INSERT INTO books (title, author, genre, synopsis, quantity) VALUES (:title, :author, :genre, :synopsis, :quantity)");
$insert->execute([
    ':title' => $title,
    ':author' => $author,
    ':genre' => $genre,
    ':synopsis' => $synopsis,
    ':quantity' => $quantity
        ]);
    }
}


/* --------------------------
   UPDATE BOOK (FACULTY ONLY)
--------------------------- */
if ($isLoggedIn && $isAdmin && isset($_POST['update_book'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $quantity = (int)$_POST['quantity'];
      $synopsis = trim($_POST['synopsis']);
    

$update = $db->prepare("UPDATE books SET title=?, author=?, genre=?, synopsis=?, quantity=? WHERE id=?");
$update->execute([$title, $author, $genre, $synopsis, $quantity, $id]);
}


/* --------------------------
   ARCHIVE BOOK
--------------------------- */
if ($isLoggedIn && $isAdmin && isset($_POST['archive_book'])) {
    $id = (int)$_POST['id'];
    $archive = $db->prepare("UPDATE books SET archived = 1 WHERE id=?");
    $archive->execute([$id]);
}

/* --------------------------
   UNARCHIVE BOOK
--------------------------- */
if ($isLoggedIn && $isAdmin && isset($_POST['unarchive_book'])) {
    $id = (int)$_POST['id'];
    $unarchive = $db->prepare("UPDATE books SET archived = 0 WHERE id=?");
    $unarchive->execute([$id]);
}

/* --------------------------
   SEARCH BOOKS
--------------------------- */
$books = [];
$archived_books = [];
$search = "";

if ($isLoggedIn) {
    if (isset($_GET['search'])) {
        $search = trim($_GET['search']);
        $stmt = $db->prepare("SELECT * FROM books WHERE archived = 0 AND (title LIKE :search OR author LIKE :search) ORDER BY title ASC");
        $stmt->execute([':search' => "%$search%"]);
    } else {
        $stmt = $db->query("SELECT * FROM books WHERE archived = 0 ORDER BY title ASC");
    }
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* Load archived books */
    $stmt2 = $db->query("SELECT * FROM books WHERE archived = 1 ORDER BY title ASC");
    $archived_books = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

/* --------------------------
   BORROW BOOK
--------------------------- */
/* --------------------------
   BORROW BOOK
--------------------------- */
/* --------------------------
   BORROW BOOK
--------------------------- */
if ($isLoggedIn && isset($_POST['borrow_book'])) {

    $book_id = (int)$_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    // 🔴 CHECK IF USER HAS OVERDUE BOOKS (borrowed more than 7 days)
    $overdueCheck = $db->prepare("
        SELECT borrow_date FROM borrows
        WHERE user_id = ?
        AND status = 'borrowed'
        AND borrow_date < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    $overdueCheck->execute([$user_id]);

    if ($overdueCheck->rowCount() > 0) {
        echo "
            <script>
                alert('⚠ You cannot borrow this book because you have an overdue book that has not been returned. Please return your overdue book first.');
                window.location.href = 'index.php';
            </script>
        ";
        exit();
    }

    // Check quantity
    $stmt = $db->prepare("SELECT quantity FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['quantity'] > 0) {

        // Prevent duplicate borrow
        $check = $db->prepare("SELECT id FROM borrows WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
        $check->execute([$user_id, $book_id]);

        if ($check->rowCount() == 0) {

            // Update quantity
            $update = $db->prepare("UPDATE books SET quantity = quantity - 1 WHERE id = ?");
            $update->execute([$book_id]);

            // Insert borrow record
            $insert = $db->prepare("INSERT INTO borrows (user_id, book_id, status) VALUES (?, ?, 'borrowed')");
            $insert->execute([$user_id, $book_id]);
        }
    }
}



/* --------------------------
   RETURN BOOK
--------------------------- */
if ($isLoggedIn && isset($_POST['return_book'])) {

    $borrow_id = (int)$_POST['borrow_id'];

    $stmt = $db->prepare("SELECT book_id FROM borrows WHERE id = ? AND status = 'borrowed'");
    $stmt->execute([$borrow_id]);
    $borrow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($borrow) {
        $book_id = $borrow['book_id'];

        $db->prepare("UPDATE borrows SET status = 'returned', return_date = NOW() WHERE id = ?")
           ->execute([$borrow_id]);

        $db->prepare("UPDATE books SET quantity = quantity + 1 WHERE id = ?")
           ->execute([$book_id]);
           $stats = [];
if ($isLoggedIn && $isAdmin) {
    // Total books (not archived)
    $stmt = $db->query("SELECT COUNT(*) as total_books FROM books WHERE archived = 0");
    $stats['total_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_books'];

    // Total archived books
    $stmt = $db->query("SELECT COUNT(*) as archived_books FROM books WHERE archived = 1");
    $stats['archived_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['archived_books'];

    // Total borrowed books
    $stmt = $db->query("SELECT COUNT(*) as borrowed_books FROM borrows WHERE status = 'borrowed'");
    $stats['borrowed_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['borrowed_books'];

    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total_users FROM users");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
    $stats = [];

}

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KLD Library Management System - Home</title>

<style>
/* ---------- GLOBAL STYLE ---------- */
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f4f7fb;
  padding-bottom: 80px;
}
.book-section ul li {
    padding: 8px 0;
    color: #1a2b5c;
}
.book-section strong {
    color: #2e4bb1;
}
.admin-dashboard-top {
    position: absolute;
    top: 265px; /* adjust depending on your navbar height */
    left: 25px;
    z-index: 999;
}

/* HERO */
.hero-library {
  background: url('library-bg.jpg') center/cover no-repeat;
  height: 200px;
  position: relative;
  border-radius: 12px;
  margin: 40px;
}

.hero-overlay {
  background: rgba(26, 43, 92, 0.85);
  height: 100%;
  color: white;
  text-align: center;
  padding: 80px 20px;
  border-radius: 12px;
}

.hero-overlay h1 {
  font-size: 42px;
  margin-bottom: 10px;
}

.hero-overlay p {
  font-size: 18px;
  margin-bottom: 25px;
}

.hero-btn {
  padding: 12px 30px;
  background: #00ac0f;
  color: white;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
}

/* FEATURES */
.library-features {
  padding: 60px 40px;
  text-align: center;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.feature-card {
  background: white;
  padding: 25px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.feature-card h3 {
  margin: 15px 0 10px;
  color: #2e4bb1;
}

/* STATS */
.library-stats {
  background: #f5f8ff;
  padding: 60px 40px;
  text-align: center;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.stat-box {
  background: white;
  padding: 30px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-box h3 {
  font-size: 36px;
  color: #2e4bb1;
}

/* POLICY */
.library-policy {
  padding: 60px 40px;
  max-width: 900px;
  margin: auto;
}

.library-policy ul {
  list-style: none;
  padding: 0;
  font-size: 18px;
}

.library-policy li {
  padding: 10px 0;
}

    /* ABOUT SECTION */
    .about-section {
      padding: 60px 40px;
      background: #f5f8ff;
      color: #1a2b5c;
      text-align: center;
    }
.badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
    display: inline-block;
}

.badge-green {
    background: #2ecc71;
    color: white;
}

.badge-red {
    background: #e74c3c;
    color: white;
}

.badge-gray {
    background: #7f8c8d;
    color: white;
}

    .about-section h1 {
      font-size: 32px;
      color: #2e4bb1;
      margin-bottom: 20px;
    }

    .about-section p {
      font-size: 18px;
      max-width: 800px;
      margin: auto;
      line-height: 1.6;
    }

    /* FAQ SECTION */
    .faq-section {
      background: #ffffff;
      padding: 60px 40px;
      max-width: 900px;
      margin: 50px auto;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: left;
    }

    .faq-item {
      margin-bottom: 20px;
    }

    .faq-item h3 {
      font-size: 20px;
      color: #1a2b5c;
      margin-bottom: 8px;
    }

    .faq-item p {
      font-size: 17px;
      color: #333;
      margin-left: 10px;
      line-height: 1.5;
    }
.styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 16px;
  margin-top: 15px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
/* MAIN CONTENT */
    .content {
      padding: 40px;
      text-align: center;
    }

    .content h1 {
      font-size: 36px;
      color: #1a2b5c;
    }
.styled-table thead {
  background-color: #2e4bb1;
  color: #ffffff;
}

.styled-table th, .styled-table td {
  padding: 12px 15px;
  text-align: left;
}

.styled-table tbody tr {
  border-bottom: 1px solid #ddd;
  transition: background 0.3s ease;
}

.styled-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.styled-table tbody tr:hover {
  background-color: #eef2ff;
}

/* ---------- BUTTONS ---------- */
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  color: white;
}
/* ---------- FEATURED BOOKS ---------- */
.featured-books {
    padding: 60px 40px;
    text-align: center;
    background: #f5f8ff;
}

.featured-books h2 {
    font-size: 32px;
    color: #2e4bb1;
    margin-bottom: 10px;
    padding-top: 75px;
}

.featured-books p {
    font-size: 18px;
    color: #555;
}

.featured-grid {
    margin-top: 35px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
}

.featured-card {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.featured-card:hover {
    transform: translateY(-6px);
}

.book-icon {
    font-size: 40px;
    margin-bottom: 10px;
}

.featured-card h3 {
    color: #2e4bb1;
    margin: 10px 0 5px;
    font-size: 18px;
}

.featured-card span {
    display: block;
    font-size: 15px;
    color: #333;
}

.featured-card small {
    font-size: 13px;
    color: #777;
}

/* Blue = Edit */
.btn.edit {
  background-color: #2e4bb1;
}
.btn.edit:hover {
  background-color: #1d3a8a;
}

/* Red = Archive */
.btn.archive {
  background-color: #d9534f;
}
.btn.archive:hover {
  background-color: #b52b27;
}

/* Green = Unarchive */
.btn.unarchive {
  background-color: #00ac0f;
}
.btn.unarchive:hover {
  background-color: #00ac0f;
}

/* Borrow */
.btn.borrow {
  background-color: #00ac0f;
}
.btn.borrow:hover {
  background-color: #00ac0f;
}

/* Return */
.btn.return {
  background-color: #00ac0f;
}
.btn.return:hover {
  background-color: #00910d;
}

.out-of-stock {
  color: #ff4d4d;
  font-weight: bold;
}



/* ---------- BOOK SECTION ---------- */
.book-section {
  background: #fff;
  max-width: 900px;
  margin: 40px auto;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.book-section h2 {
  color: #2e4bb1;
  text-align: center;
}
/* ----- SEARCH BAR ----- */
.search-bar {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 20px auto;
    max-width: 600px;
}

.search-bar input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #2e4bb1;
    border-radius: 8px;
    font-size: 16px;
}

.search-bar button {
    padding: 12px 20px;
    background: #2e4bb1;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}
.search-bar button:hover {
    background: #1d3a8a;
}


/* ----- ADD BOOK FORM ----- */
.add-book-form {
    margin: 25px auto;
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    max-width: 900px;
}

.add-book-form input {
    padding: 10px 14px;
    border: 2px solid #2e4bb1;
    border-radius: 8px;
    font-size: 16px;
    width: 200px;
}

.add-book-form button {
    padding: 10px 20px;
    background: #00ac0f;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}
.add-book-form button:hover {
    background: #00910d;
}


</style>
</head>

<body>
<?php include 'header.php'; ?>
<?php if ($isLoggedIn && $isAdmin): ?>
<div class="admin-dashboard-top">
    <a href="admin_dashboard.php">
        <button style="padding:10px 20px; background:#2e4bb1; color:white; border:none; border-radius:6px; cursor:pointer;">
            Admin Dashboard
        </button>
    </a>
</div>
<?php endif; ?>

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

<!-- HERO SECTION --> 
<section class="hero-library">
  <div class="hero-overlay">
    <h1>KLD Digital Library</h1>
    <p>Your gateway to knowledge, learning, and academic excellence.</p>

    <?php if (!$isLoggedIn): ?>
      <a href="login.php" class="hero-btn">Get Started</a>
    <?php endif; ?>
  </div>
</section>

<section class="featured-books">
    <h2>Featured Books</h2>
    <p>Recommended reads from the KLD Library</p>

    <div class="featured-grid">
        <!-- Book 1 -->
        <div class="featured-card">
            <img src="TheGreatGatsby.jpg" alt="Book 1" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>The Great Gatsby</h3>
            <span>by F. Scott Fitzgerald</span>
            <p>A classic novel exploring themes of wealth, love, and the American Dream in the 1920s.</p>
        </div>

        <!-- Book 2 -->
        <div class="featured-card">
            <img src="pedagogy.jpg" alt="Book 2" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>Pedagogy of Freedom</h3>
            <span>by Paulo Freire</span>
            <p>Discusses education as a path to liberation, emphasizing critical thinking, creativity, and the development of autonomous, reflective learners.</p>
        </div>

        <!-- Book 3 -->
        <div class="featured-card">
            <img src="sapiens.jpg" alt="Book 3" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>Sapiens: A Brief History of Humankind</h3>
            <span>by Yuval Noah Harari</span>
            <p>Explores the history of humankind from the emergence of Homo sapiens to the present, examining how biology, culture, and technology shaped societies.</p>
        </div>

        <!-- Book 4 -->
        <div class="featured-card">
            <img src="wuthering-heights-124.jpg" alt="Book 4" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>Wuthering Heights</h3>
            <span>by Emily Brontë</span>
            <p>A gothic novel about intense passion, revenge, and the destructive power of love, set on the Yorkshire moors between two feuding families.</p>
        </div>

        <!-- Book 5 -->
        <div class="featured-card">
            <img src="frankens.jpg" alt="Book 5" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>Frankenstein</h3>
            <span>by Mary Shelley</span>
            <p>Tells the story of Victor Frankenstein, a scientist who creates a sentient creature, exploring themes of ambition, responsibility, and humanity’s limits.</p>
        </div>

        <!-- Book 6 -->
        <div class="featured-card">
            <img src="bookwhisperer.jpg" alt="Book 6" style="width:100%; border-radius:8px; margin-bottom:10px;">
            <h3>The Book Whisperer</h3>
            <span>by Donalyn Miller</span>
            <p>Offers strategies for fostering a love of reading in students, inspiring teachers to create lifelong readers through passion and guidance rather than obligation.</p>
        </div>
    </div>
</section>




<section class="library-features">

  <h2 style="margin: px;">Library Services</h2>

  <div class="feature-grid">
    <div class="feature-card">
      📚
      <h3>Book Catalog</h3>
      <p>Browse and search available books by title or author.</p>
    </div>

    <div class="feature-card">
      ⏳
      <h3>Borrow & Return</h3>
      <p>Borrow books online and track return deadlines easily.</p>
    </div>

    <div class="feature-card">
      🔐
      <h3>Secure Accounts</h3>
      <p>Role-based access for students, faculty, and administrators.</p>
    </div>

    <div class="feature-card">
      📊
      <h3>Admin Management</h3>
      <p>Manage books, users, and borrowing records efficiently.</p>
    </div>
  </div>
</section>



<section class="library-policy">
  <h2>Borrowing Policy</h2>

  <ul>
    <li>📌 Borrowed books must be returned within <strong>7 days</strong>.</li>
    <li>📌 Users with overdue books cannot borrow new items.</li>
    <li>📌 Lost books must be reported immediately.</li>
    <li>📌 Physical return is required even after online return.</li>
  </ul>
</section>


<div class="about-section">
  <h1>Welcome to the KLD Library Management System</h1>
  <p>Browse library information, learn about our system, and explore FAQs.</p>
</div>
<?php if ($isLoggedIn && $isAdmin): ?>


<?php endif; ?>


<?php if ($isLoggedIn): ?>
<div class="book-section">
  <h2>Library Books</h2>

<form method="GET" class="search-bar">
    <input type="text" name="search" placeholder="Search books by title or author" 
           value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>


<?php if ($isAdmin): ?>
<form method="POST" class="add-book-form">
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="text" name="author" placeholder="Author" required>
    <input type="text" name="genre" placeholder="Genre" required>
     <textarea name="synopsis" placeholder="Synopsis" required style="width:100%; height:60px; padding:8px; border:2px solid #2e4bb1; border-radius:6px;"></textarea>
    <input type="number" name="quantity" placeholder="Quantity" min="1" required>
    <button type="submit" name="add_book">Add Book</button>
</form>
<?php endif; ?>


  <!-- BOOKS TABLE -->
<table class="styled-table">
  <thead>
    <tr>
      <th>Title</th>
      <th>Author</th>
      <th>Genre</th>
      <th>Quantity</th>
      <th>Action</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($books as $book): ?> 
    <tr class="book-row" data-id="<?= $book['id']; ?>" style="cursor:pointer;">
      <?php if ($isAdmin && isset($_GET['edit']) && $_GET['edit'] == $book['id']): ?>
      <!-- Editable row -->
      <form method="POST">
        <td><input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>"></td>
        <td><input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>"></td>
        <td><input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>"></td>
        <td colspan="5">
  <textarea name="synopsis" required style="width:100%; height:60px; padding:8px; border:2px solid #2e4bb1; border-radius:6px;">
<?= htmlspecialchars($book['synopsis']) ?>
  </textarea>
</td>
        <td><input type="number" name="quantity" value="<?= htmlspecialchars($book['quantity']) ?>"></td>
        <td>
          <input type="hidden" name="id" value="<?= $book['id'] ?>">
          <button type="submit" name="update_book" class="btn edit">Save</button>
          <a href="index.php" class="btn edit">Cancel</a>
        </td>
      </form>
      <?php else: ?>
      <!-- Normal row -->
      <td><?= htmlspecialchars($book['title']); ?></td>
      <td><?= htmlspecialchars($book['author']); ?></td>
      <td><?= htmlspecialchars($book['genre']); ?></td>
      <td><?= htmlspecialchars($book['quantity']); ?></td>
      <td>
        <?php if ($isAdmin): ?>
          <a href="index.php?edit=<?= $book['id'] ?>" class="btn edit">Edit</a>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $book['id']; ?>">
             <button type="submit" name="archive_book" class="btn archive"
            onclick="return confirm('Are you sure you want to archive this book?');">
        Archive</button>
          </form>
        <?php endif; ?>
        <?php if ($book['quantity'] > 0): ?>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="book_id" value="<?= $book['id']; ?>">
           <form method="POST" style="display:inline;">
    <input type="hidden" name="book_id" value="<?= $book['id']; ?>">
    <button type="submit" name="borrow_book" class="btn borrow"
            onclick="return confirm('Are you sure you want to borrow this book?');">
        Borrow
    </button>
</form>

            
          </form>
        <?php else: ?>
          <span class="out-of-stock">Out of stock</span>
        <?php endif; ?>
      </td>
      <?php endif; ?>
    </tr>
    <!-- Hidden details row -->
<tr class="book-details-row" id="details-<?= $book['id']; ?>" style="display:none; background:#f9f9f9;">
    <td colspan="5" style="padding:15px;">
        <strong>Synopsis:</strong>
        <p><?= nl2br(htmlspecialchars($book['synopsis'])); ?></p>
    </td>
</tr>
    <?php endforeach; ?>
  </tbody>
</table>


  <!-- ARCHIVED BOOKS TABLE -->
  <?php if ($isAdmin && count($archived_books) > 0): ?>
  <h2 style="margin-top:40px;">Archived Books</h2>

  <table class="styled-table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Quantity</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($archived_books as $abook): ?>
      <tr>
        <td><?= htmlspecialchars($abook['title']); ?></td>
        <td><?= htmlspecialchars($abook['author']); ?></td>
        <td><?= htmlspecialchars($abook['quantity']); ?></td>

        <td>
<form method="POST" style="display:inline;">
    <input type="hidden" name="id" value="<?= $abook['id']; ?>">
    <button type="submit" name="unarchive_book" class="btn unarchive"
            onclick="return confirm('Are you sure you want to unarchive this book?');">
        Unarchive
    </button>
</form>

        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>


  <!-- BORROWED BOOKS TABLE -->
  <h2>Your Borrowed Books</h2>

  <table class="styled-table">
    <thead>
      <tr>
<th>Title</th>
<th>Borrowed On</th>
<th>Return Date</th>
<th>Status</th>
<th>Remaining Time</th>
<th>Action</th>

      </tr>
    </thead>
    <tbody>
      <?php
      $user_id = $_SESSION['user_id'];
     $stmt = $db->prepare("
SELECT 
    br.id AS borrow_id, 
    bk.title, 
    br.borrow_date, 
    br.return_date,
    br.status,
    DATEDIFF(DATE_ADD(br.borrow_date, INTERVAL 7 DAY), NOW()) AS remaining_days
FROM borrows br
JOIN books bk ON br.book_id = bk.id
WHERE br.user_id = ?
");
      $stmt->execute([$user_id]);
      $borrowed = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($borrowed as $b):
            $formattedDate = date("F j, Y - h:i A", strtotime($b['borrow_date']));
            $formattedReturnDate = $b['return_date'] 
    ? date("F j, Y - h:i A", strtotime($b['return_date'])) 
    : "—";

      ?>  
      <tr>
        <td><?= htmlspecialchars($b['title']); ?></td>
        <td><?= htmlspecialchars($formattedDate); ?></td>
        <td><?= htmlspecialchars($formattedReturnDate); ?></td>

<td><?= ucfirst($b['status']); ?></td>

<td>
    <?php
        if ($b['status'] === 'returned') {
            echo "<span class='badge badge-gray'>Completed</span>";
        } elseif ($b['remaining_days'] < 0) {
            echo "<span class='badge badge-red'>Overdue</span>";
        } elseif ($b['remaining_days'] == 0) {
            echo "<span class='badge badge-red'>Due Today</span>";
        } else {
            echo "<span class='badge badge-green'>{$b['remaining_days']} day(s) left</span>";
        }
    ?>
</td>


        <td>
          <?php if ($b['status'] === 'borrowed'): ?>
<form method="POST" style="display:inline;">
    <input type="hidden" name="borrow_id" value="<?= $b['borrow_id']; ?>">
    <button type="submit" name="return_book" class="btn return"
            onclick="return confirm('Are you sure you want to return this book?');">
        Return
    </button>
</form>

          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>


<?php endif; ?>
   <div class="about-section">
    <h1>Frequently Asked Questions</h1>
    <div class="faq-section">
      <div class="faq-item">
        <h3>1. What is the KLD Library Management System?</h3>
        <p>The KLD Library Management System is an online platform that helps students, teachers, and staff easily access and manage library services.</p>
      </div>
      <div class="faq-item">
        <h3>2. How do I create an account?</h3>
        <p>Click on the “Sign Up” button on the login page and fill out the required information using your KLD email address.</p>
      </div>
      <div class="faq-item">
        <h3>3. Can I log in using a non-KLD email?</h3>
        <p>No. For security reasons, only KLD emails (e.g., @kld.edu.ph) are allowed to register and log in.</p>
      </div>
      <div class="faq-item">
        <h3>4. What should I do if I forget my password?</h3>
        <p>Click the “Forgot Password” link on the login page and follow the instructions to reset your password.</p>
      </div>
      <div class="faq-item">
        <h3>5. How do I borrow a book online?</h3>
        <p>Once logged in, go to the “Books” section, find your desired book, and click “Borrow” to send a request.</p>
      </div>
      <div class="faq-item">
        <h3>6. Can I return a book through the website?</h3>
        <p>Yes, you can mark a book as returned under your borrowed books list, but you must also return the physical copy to the library.</p>
      </div>
      <div class="faq-item">
        <h3>7. Is there a limit to how many books I can borrow?</h3>
        <p>You can borrow books as many as you want.</p>
      </div>
      <div class="faq-item">
        <h3>8. How do I update my account information?</h3>
        <p>Go to “Profile Settings” after logging in, and you can update your name, password, or contact information.</p>
      </div>
      <div class="faq-item">
        <h3>9. What happens if I lose a borrowed book?</h3>
        <p>You must report it immediately to the librarian. A replacement or penalty may apply based on library policy.</p>
      </div>
      <div class="faq-item">
        <h3>10. Who can I contact for technical support?</h3>
        <p>If you experience any issues, email us at <strong>kldlibrarymanagement@kld.edu.ph</strong> for assistance.</p>
      </div>
    </div>
  </div>
  <script>
    // Save scroll position before form submits
    document.addEventListener("submit", function () {
        localStorage.setItem("scrollPosition", window.scrollY);
    });

    // Restore scroll position after reload
    window.addEventListener("load", function () {
        if (localStorage.getItem("scrollPosition") !== null) {
            window.scrollTo(0, localStorage.getItem("scrollPosition"));
        }
        // Clear after restoring
        localStorage.removeItem("scrollPosition");
    });
    document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll(".book-row");

    rows.forEach(row => {
        row.addEventListener("click", function() {
            const bookId = row.dataset.id;
            const detailsRow = document.getElementById("details-" + bookId);

            // Toggle the visibility
            if(detailsRow.style.display === "none") {
                detailsRow.style.display = "table-row";
            } else {
                detailsRow.style.display = "none";
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
