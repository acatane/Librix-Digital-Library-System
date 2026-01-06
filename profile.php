<?php
session_start();
require_once __DIR__ . '/../content/db.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT username, email FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "User not found!";
    exit();
}

$user = $result->fetch_assoc();

// Check for session messages from update_user.php
$alert = $_SESSION['update_msg'] ?? null;
unset($_SESSION['update_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Inventory System</title>
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* Base */
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
body {
    background:#1f1f1f;
    color:#fff;
    min-height:100vh;
    display:flex;
    flex-direction:column;
    animation: fadeIn 0.8s ease;
}
@keyframes fadeIn { from{opacity:0;} to{opacity:1;} }

/* Header */
header {
    position:fixed; top:0; left:0; width:100%; height:65px;
    background:linear-gradient(90deg,#ff4500,#ff7f50);
    display:flex; justify-content:space-between; align-items:center;
    padding:0 40px; box-shadow:0 5px 15px rgba(0,0,0,0.5);
    z-index:100;
}
header h1 { font-size:24px; font-weight:700; text-shadow:1px 1px 3px rgba(0,0,0,0.5); }
header nav { display:flex; gap:20px; }
header nav a {
    display:flex; align-items:center; gap:6px;
    color:#fff; text-decoration:none; font-weight:600;
    transition:color 0.3s, transform 0.2s;
}
header nav a:hover { color:#ffe0b2; transform:translateY(-2px); }
header nav a.logout-btn {
    background:rgba(255,255,255,0.2);
    padding:6px 12px; border-radius:8px; color:#430202;
}
header nav a.logout-btn:hover { background:rgba(255,255,255,0.35); }

/* Main */
main {
    flex:1; padding-top:90px; display:flex; flex-direction:column;
    align-items:center; justify-content:flex-start;
}
main h1 {
    margin-bottom:20px;
    font-size:28px;
    background:linear-gradient(90deg,#ff7f50,#ffd280);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* Alert */
.alert {
    max-width:450px;
    padding:15px 20px;
    border-radius:8px;
    font-weight:bold; text-align:center;
    margin-bottom:20px;
    animation: slideDown 0.6s ease;
}
@keyframes slideDown {
    from{opacity:0; transform:translateY(-20px);}
    to{opacity:1; transform:translateY(0);}
}
.alert.success { background:#27ae60; }
.alert.error { background:#e74c3c; }

/* Form container */
.form-container {
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(12px);
    padding:30px; border-radius:15px;
    max-width:450px; width:100%;
    box-shadow:0 8px 32px rgba(0,0,0,0.3);
    animation: floatUp 0.7s ease;
}
@keyframes floatUp {
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}
.form-container form { display:flex; flex-direction:column; gap:15px; }
.form-container label { font-weight:600; margin-bottom:5px; }
.form-container input {
    padding:10px; border-radius:8px; border:1px solid #444;
    background:rgba(0,0,0,0.3); color:#fff; font-size:15px;
    transition:border 0.3s, box-shadow 0.3s;
}
.form-container input:focus {
    border:1px solid #ff7f50;
    box-shadow:0 0 8px #ff7f50;
    outline:none;
}

/* Password toggle */
.password-wrapper { position:relative; }
.toggle-password {
    position:absolute; right:12px; top:50%;
    transform:translateY(-50%);
    cursor:pointer; color:#bbb;
    transition:color 0.3s;
}
.toggle-password:hover { color:#ff7f50; }

/* Button */
button[type="submit"] {
    background:linear-gradient(90deg,#ff4500,#ff7f50);
    border:none; border-radius:10px;
    color:#fff; padding:12px; font-size:16px; font-weight:600;
    cursor:pointer;
    transition:transform 0.2s, background 0.3s;
}
button[type="submit"]:hover {
    transform:scale(1.05);
    background:linear-gradient(90deg,#ff6333,#ffa07a);
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
    <h1>My Profile</h1>

    <!-- Alert -->
    <?php if ($alert): ?>
        <div class="alert <?php echo $alert['success'] ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($alert['message']); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form action="update_user.php" method="post">
            <label>Username</label>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>

            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

            <label for="password">New Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter new password">
                <i class="toggle-password" data-lucide="eye" onclick="togglePassword('password', this)"></i>
            </div>

            <label for="confirm_password">Confirm New Password</label>
            <div class="password-wrapper">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
                <i class="toggle-password" data-lucide="eye" onclick="togglePassword('confirm_password', this)"></i>
            </div>

            <button type="submit">Update Password</button>
        </form>
    </div>
</main>

<script>
lucide.createIcons();

// Password toggle with Lucide icons
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        icon.setAttribute("data-lucide","eye-off");
    } else {
        input.type = "password";
        icon.setAttribute("data-lucide","eye");
    }
    lucide.createIcons();
}

// Auto-hide alert after 3s
const alertBox = document.querySelector('.alert');
if (alertBox) {
    setTimeout(() => {
        alertBox.style.opacity = '0';
        alertBox.style.transition = 'opacity 0.6s';
        setTimeout(() => alertBox.remove(), 600);
    }, 3000);
}
</script>
</body>
</html>
