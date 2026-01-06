<?php
session_start();
require_once __DIR__ . '/../content/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove'])) {
    $book_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM reading_list WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    echo "<script>alert('Book removed from your reading list.'); window.location.href='library.php';</script>";
    exit();
}

$sql = "SELECT b.id, b.title, b.author, b.year_published, b.category, b.description, b.cover_image, b.book_file
        FROM reading_list rl
        JOIN books b ON rl.book_id = b.id
        WHERE rl.user_id = ?
        ORDER BY b.title ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Library</title>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* Reset */
* { box-sizing: border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
body { background: #1f1f1f; color:#fff; min-height:100vh; display:flex; flex-direction:column; }

/* Header */
header {
    background: linear-gradient(90deg, #ff4500, #ff7f50);
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.6);
}
header h1 { font-size: 26px; font-weight: 700; letter-spacing: 1px; }
header nav { display:flex; gap:25px; align-items:center; }
header nav a {
    display:flex; align-items:center; gap:8px;
    color:#fff; text-decoration:none; font-weight:600;
    transition: color 0.3s, transform 0.2s;
}
header nav a:hover { color:#ffe0b2; transform:translateY(-2px); }
header nav a.logout-btn {
    background: rgba(255,255,255,0.2);
    padding: 6px 14px; border-radius: 8px;
    transition: background 0.3s, transform 0.2s;
    color:#430202;
}
header nav a.logout-btn:hover {
    background: rgba(255,255,255,0.35);
    transform: translateY(-2px);
}

/* Main */
main {
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    padding:60px 20px;
}
main h2 {
    font-size: 36px;
    margin-bottom: 20px;
    background: linear-gradient(90deg, #ff7f50, #ffd280);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
main p {
    font-size: 18px;
    max-width: 600px;
    margin-bottom: 40px;
    line-height: 1.6;
    color:#ddd;
}

/* Book List */
.book-list {
    display: flex;
    flex-direction: column;
    gap: 25px;
    width:100%;
    max-width:950px;
}
.book-row {
    display:flex;
    background: rgba(44,44,44,0.6);
    border-radius:20px;
    backdrop-filter: blur(12px);
    overflow:hidden;
    box-shadow:0 8px 25px rgba(255,120,50,0.25);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(255,120,50,0.2);
    position: relative;
}
.book-row:hover { 
    transform: translateY(-6px) scale(1.02) rotateX(1deg); 
    box-shadow:0 15px 35px rgba(255,120,50,0.4); 
}

.book-cover {
    flex:0 0 180px;
    height:250px;
    overflow:hidden;
}
.book-cover img { width:100%; height:100%; object-fit:cover; }

.book-info {
    flex:1;
    padding:20px;
    text-align:left;
    display:flex;
    flex-direction:column;
    justify-content:center;
}
.book-info h3 { font-size:20px; margin-bottom:10px; color:#ffb380; }
.book-info p { color:#ffd9b3; margin:5px 0; font-size:14px; line-height:1.4; }

/* Status Badge */
.status-badge {
    position: absolute;
    top:15px;
    right:15px;
    background: linear-gradient(90deg,#ff7f50,#ffd280);
    color:#222;
    padding:5px 12px;
    font-size:12px;
    border-radius:12px;
    font-weight:600;
    box-shadow:0 3px 10px rgba(0,0,0,0.3);
}

/* Buttons */
.actions { margin-top:18px; display:flex; flex-wrap:wrap; gap:12px; }
.actions a {
    padding:10px 20px;
    border-radius:12px;
    text-decoration:none;
    font-size:15px;
    font-weight:600;
    transition: all 0.3s ease;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.read-btn {
    background: linear-gradient(135deg, #ff4500, #ff7f50);
    color:#fff;
    box-shadow:0 0 12px rgba(255,120,50,0.5);
}
.read-btn:hover { 
    transform:translateY(-2px) scale(1.05); 
    background: linear-gradient(135deg,#ff6333,#ffa07a); 
    box-shadow:0 0 20px rgba(255,120,50,0.7);
}
.remove-btn {
    background: linear-gradient(135deg, #ff5a5a, #ff8787);
    color:#fff;
    box-shadow:0 0 12px rgba(255,90,90,0.5);
}
.remove-btn:hover { 
    transform:translateY(-2px) scale(1.05); 
    background: linear-gradient(135deg,#ff6b6b,#ff9999);
    box-shadow:0 0 20px rgba(255,90,90,0.7);
}

/* Floating Action Button */
.fab {
    position:fixed;
    bottom:25px;
    right:25px;
    background:linear-gradient(135deg,#ff7f50,#ffd280);
    color:#222;
    padding:18px;
    border-radius:50%;
    font-size:22px;
    cursor:pointer;
    box-shadow:0 6px 20px rgba(0,0,0,0.5);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.fab:hover {
    transform:scale(1.1) rotate(10deg);
    box-shadow:0 8px 30px rgba(255,150,70,0.7);
}

/* Empty */
.empty-message {
    text-align:center;
    padding:80px 20px;
    color:#ffa366;
    animation: floaty 3s infinite ease-in-out;
}
.empty-message img { width:140px; margin-bottom:25px; filter: drop-shadow(0 0 12px rgba(255,150,70,0.4)); }
@keyframes floaty {
    0%,100% { transform:translateY(0); }
    50% { transform:translateY(-10px); }
}

/* Footer */
footer {
    text-align:center;
    padding:15px;
    background:#2c2c2c;
    font-size:14px;
    color:#aaa;
    box-shadow:0 -3px 12px rgba(0,0,0,0.5);
}

/* Responsive */
@media screen and (max-width:768px){
    main h2{font-size:28px;}
    main p{font-size:16px;}
    .book-row{flex-direction:column; align-items:center;}
    .book-cover{width:100%; height:200px;}
    .book-info{text-align:center;}
}
</style>
</head>
<body>

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

<main>
    <h2>📚 My Reading List</h2>
    <p>Here are the books you’ve saved. Start reading or manage your list anytime.</p>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="book-list">
            <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-row">
                    <span class="status-badge">Not Started</span>
                    <div class="book-cover">
                        <img src="../uploads/<?php echo htmlspecialchars($book['cover_image'] ?: 'default-cover.png'); ?>" alt="Book Cover">
                    </div>
                    <div class="book-info">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p>👤 <?php echo htmlspecialchars($book['author']); ?></p>
                        <p>📅 <?php echo $book['year_published']; ?></p>
                        <p>📂 <?php echo htmlspecialchars($book['category']); ?></p>
                        <p><?php echo htmlspecialchars($book['description']); ?></p>
                        <div class="actions">
                            <?php if (!empty($book['book_file'])): ?>
                                <a href="read.php?id=<?php echo $book['id']; ?>" class="read-btn"><i data-lucide="book-open"></i> Read</a>
                            <?php else: ?>
                                <span style="color:#777; font-size:14px;">No file available</span>
                            <?php endif; ?>
                            <a href="library.php?remove=<?php echo $book['id']; ?>" class="remove-btn" onclick="return confirm('Remove this book from your reading list?');"><i data-lucide="trash-2"></i> Remove</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-message">
            <img src="../assets/no-books.png" alt="No Books">
            <h2>No books in your reading list</h2>
            <p>Browse books and add them to your reading list to get started!</p>
        </div>
    <?php endif; ?>
</main>

<!-- Floating Action Button -->
<div class="fab" onclick="window.location.href='manage_books.php'">
   <i data-lucide="plus"></i>
</div>



<script> lucide.createIcons(); </script>
</body>
</html>
