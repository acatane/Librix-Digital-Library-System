<?php
session_start();
require_once __DIR__ . '/../content/db.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle filters
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
$yearFilter     = isset($_GET['year']) ? $_GET['year'] : '';
$keyword        = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Base query
$sql = "SELECT id, title, author, year_published, description, category, cover_image, book_file 
        FROM books WHERE 1=1";

// Apply category filter
if ($categoryFilter !== '') {
    $sql .= " AND category = '" . $conn->real_escape_string($categoryFilter) . "'";
}

// Apply year filter
if ($yearFilter !== '' && is_numeric($yearFilter)) {
    $sql .= " AND year_published = " . intval($yearFilter);
}

// Apply keyword search
if ($keyword !== '') {
    $kw = $conn->real_escape_string($keyword);
    $sql .= " AND (LOWER(title) LIKE LOWER('%$kw%') OR LOWER(author) LIKE LOWER('%$kw%'))";
}

$sql .= " ORDER BY title ASC";
$result = $conn->query($sql);

// Fetch categories
$categories = [];
$catResult = $conn->query("SELECT * FROM categories ORDER BY name ASC");
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse Books - Inventory System</title>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* Base */
* { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
body { background:#1f1f1f; color:#fff; }

/* Header */
header { 
    background: linear-gradient(90deg, #ff4500, #ff7f50);
    padding:15px 40px; 
    display:flex; justify-content:space-between; align-items:center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.6);
}
header h1 { font-size:26px; font-weight:700; letter-spacing:1px; }

header nav { display:flex; gap:25px; align-items:center; }
header nav a {
    display:flex; align-items:center; gap:8px;
    color:#fff; text-decoration:none; font-weight:600;
    transition: color 0.3s, transform 0.2s;
}
header nav a:hover { color:#ffe0b2; transform:translateY(-2px); }

header nav a.logout-btn {
    background: rgba(255,255,255,0.2);
    padding:6px 14px;
    border-radius:8px;
    transition: background 0.3s, transform 0.2s;
    color:#430202;
}
header nav a.logout-btn:hover {
    background: rgba(255,255,255,0.35);
    transform:translateY(-2px);
}

/* Page Title */
main h1 {
    font-size:34px;
    margin-bottom:20px;
    text-align:center;
    background: linear-gradient(90deg, #ff7f50, #ffd280);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Filters */
.filters { margin: 25px 0; display:flex; justify-content:center; }
.filter-form {
    display:flex; flex-wrap:wrap; gap:15px; justify-content:center;
    background:rgba(44,44,44,0.6); padding:15px 20px; border-radius:15px;
    backdrop-filter: blur(10px);
    box-shadow: 0 6px 20px rgba(255,120,50,0.25);
    border:1px solid rgba(255,120,50,0.2);
}
.filter-select, .filter-input, .filter-keyword {
    padding:10px 12px; border-radius:8px; border:none;
    font-size:14px; outline:none; background:#3a3a3a; color:#fff;
    transition: 0.2s;
}
.filter-select:focus, .filter-input:focus, .filter-keyword:focus {
    box-shadow: 0 0 10px #ff7f50;
}
.filter-input { width:100px; }
.filter-keyword { width:200px; }

.filter-btn {
    background:linear-gradient(90deg, #ff4500, #ff7f50);
    color:#fff; border:none; padding:10px 18px;
    border-radius:8px; cursor:pointer; font-weight:600;
    transition: all 0.2s;
}
.filter-btn:hover { background:linear-gradient(90deg, #ff6333, #ffa07a); transform:scale(1.05); }

/* Books Grid */
.book-grid {
    display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:25px; justify-items:center;
}
.book-card {
    perspective:1000px; width:220px; height:300px;
}
.book-inner {
    position:relative; width:100%; height:100%;
    transform-style:preserve-3d;
    transition:transform 0.6s;
}
.book-card:hover .book-inner { transform:rotateY(180deg); }

.book-front, .book-back {
    position:absolute; width:100%; height:100%;
    backface-visibility:hidden;
    border-radius:15px; overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.6);
}
.book-front {
    background:linear-gradient(145deg, #ff4500, #ff7f50);
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:15px;
}
.book-front img {
    width:90%; height:180px; object-fit:cover; border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,0.5); margin-bottom:10px;
}
.book-front h3 {
    font-size:16px; font-weight:700; margin:5px 0;
    text-overflow:ellipsis; overflow:hidden; white-space:nowrap; color:#fff;
}
.book-front p { font-size:13px; color:#ffe0c0; }

.book-back {
    background:#2c2c2c; color:#fff;
    transform:rotateY(180deg);
    padding:15px; display:flex; flex-direction:column; justify-content:space-between;
}
.book-back h4 { font-size:15px; color:#ffb380; margin-bottom:8px; }
.book-back p { font-size:13px; color:#ddd; line-height:1.4; max-height:140px; overflow:auto; }
.book-back a {
    align-self:center; margin-top:10px;
    background:linear-gradient(135deg,#ff4500,#ff7f50);
    padding:8px 16px; border-radius:8px; text-decoration:none; color:#fff; font-weight:600;
    box-shadow:0 4px 12px rgba(0,0,0,0.4);
    transition:transform 0.2s;
}
.book-back a:hover { transform:scale(1.05); background:linear-gradient(135deg,#ff6333,#ffa07a); }

/* Empty */
.no-books {
    text-align:center; padding:60px; color:#ffa07a;
    animation:floaty 3s infinite ease-in-out;
}
.no-books h2 { font-size:22px; margin-bottom:10px; }
.no-books p { color:#ffb48f; font-size:16px; }
@keyframes floaty {
    0%,100% { transform:translateY(0); }
    50% { transform:translateY(-10px); }
}

/* Floating Button */
.fab {
    position:fixed;
    bottom:25px; right:25px;
    background:linear-gradient(135deg,#ff7f50,#ffd280);
    color:#222; padding:16px;
    border-radius:50%; font-size:22px;
    cursor:pointer; box-shadow:0 6px 20px rgba(0,0,0,0.5);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.fab:hover {
    transform:scale(1.1) rotate(12deg);
    box-shadow:0 8px 30px rgba(255,150,70,0.7);
}
</style>
</head>
<body>

<!-- Header -->
<header>
    <h1>Librix</h1>
    <nav>
      <a href="home.php"><i data-lucide="home"></i> Home</a>
      <a href="manage_books.php"><i data-lucide="book"></i> Browse</a>
      <a href="library.php"><i data-lucide="library"></i> Library</a>
      <a href="profile.php"><i data-lucide="user"></i> Profile</a>
      <a href="logout.php" class="logout-btn"><i data-lucide="log-out"></i> Logout</a>
    </nav>
</header>

<!-- Main Content -->
<main style="padding:20px;">
    <h1>📚 Browse Books</h1>

    <!-- Filters + Search -->
    <div class="filters">
        <form method="get" action="" class="filter-form">
            <input type="text" name="keyword" class="filter-keyword" placeholder="Search title or author..." value="<?php echo htmlspecialchars($keyword); ?>">
            <select name="category" class="filter-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($cat == $categoryFilter) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="year" placeholder="Year" min="0" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($yearFilter); ?>" class="filter-input">
            <button type="submit" class="filter-btn"><i data-lucide="search"></i> Search</button>
        </form>
    </div>

    <!-- Books Grid -->
    <?php if ($result->num_rows > 0): ?>
    <div class="book-grid">
        <?php while($book = $result->fetch_assoc()): ?>
        <div class="book-card">
            <div class="book-inner">
                <!-- Front -->
                <div class="book-front">
                    <img src="../uploads/<?php echo htmlspecialchars($book['cover_image'] ?: 'default-cover.png'); ?>" alt="Book Cover">
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p><?php echo htmlspecialchars($book['author']); ?></p>
                </div>
                <!-- Back -->
                <div class="book-back">
                    <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($book['description'],0,120)); ?>...</p>
                    <a href="add_to_reading_list.php?id=<?php echo $book['id']; ?>"><i data-lucide="plus"></i> Add</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="no-books">
        <h2>No books available</h2>
        <p>Try adjusting your filters or check back later.</p>
    </div>
    <?php endif; ?>
</main>

<!-- Floating Action Button -->
<div class="fab" onclick="window.location.href='add_book.php'">
   <i data-lucide="plus"></i>
</div>

<!-- Load Lucide Icons -->
<script>lucide.createIcons();</script>
</body>
</html>
