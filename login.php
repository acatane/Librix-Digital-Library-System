<?php
include '../content/db.php';
session_start();

$error = ""; // make sure error is always defined

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $dbUsername, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $dbUsername;
                header("Location: home.php");
                exit();
            } else {
                $error = "Invalid password.";
                $errorImg = "../image/wrong2.webp";
            }
        } else {
            $error = "No user found.";
            $errorImg = "../image/noresult.webp";
        }
        $stmt->close();
    } else {
        $error = "Database query failed.";
        $errorImg = "../image/noresult.webp";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - </title>

<link rel="stylesheet" href="../css/modal2.css">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", sans-serif;
}

/* Book Background */
body {
  height: 100vh;
  background:
    linear-gradient(rgba(10,10,10,0.85), rgba(10,10,10,0.9)),
    url("../image/books-bg.jpg") center/cover no-repeat;
  color: #eee;
}

/* Center layout */
.page-content {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

/* Login Card */
.login-container {
  background: rgba(30,30,30,0.95);
  width: 400px;
  padding: 45px 40px;
  border-radius: 16px;
  box-shadow: 0 30px 60px rgba(0,0,0,0.8);
  text-align: center;
  animation: fadeSlide 0.8s ease;
  position: relative;
  backdrop-filter: blur(4px);
}

/* Orange Accent Line */
.login-container::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(to right, #ff8c00, #ffb347);
  border-radius: 16px 16px 0 0;
}

/* Header */
.library-header img {
  width: 70px;
  margin-bottom: 12px;
  filter: brightness(0) saturate(100%) invert(56%) sepia(78%) saturate(2437%) hue-rotate(360deg);
}

.library-header h1 {
  font-size: 30px;
  color: #ff8c00;
  letter-spacing: 1px;
}

.library-header p {
  font-size: 14px;
  color: #b0b0b0;
  margin-bottom: 25px;
}

/* Divider */
.divider {
  height: 1px;
  background: linear-gradient(to right, transparent, #444, transparent);
  margin: 20px 0;
}

/* Form */
.form-group {
  text-align: left;
  margin-bottom: 16px;
}

.form-group label {
  font-size: 13px;
  color: #ccc;
}

.form-group input {
  width: 100%;
  padding: 11px;
  margin-top: 6px;
  border-radius: 6px;
  border: 1px solid #333;
  background: #2a2a2a;
  color: #fff;
  outline: none;
  transition: all 0.2s ease;
}

.form-group input::placeholder {
  color: #888;
}

.form-group input:focus {
  border-color: #ff8c00;
  box-shadow: 0 0 0 2px rgba(255,140,0,0.15);
}

/* Button */
.login-btn {
  width: 100%;
  padding: 13px;
  margin-top: 18px;
  background: linear-gradient(135deg, #ff8c00, #ffb347);
  color: #111;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.login-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(255,140,0,0.35);
}

/* Register */
.register-link {
  margin-top: 20px;
  font-size: 14px;
  color: #aaa;
}

.register-link a {
  color: #ff8c00;
  text-decoration: none;
}

/* Footer */
.library-footer {
  margin-top: 25px;
  font-size: 12px;
  color: #777;
}

/* Animation */
@keyframes fadeSlide {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
</head>

<body>

<!-- Modal -->
<div id="alertModal" class="modal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalMessage">
    <button class="close-btn" aria-label="Close">&times;</button>
    <div class="modal-body">
      <img id="modalImage" src="" alt="status">
      <p id="modalMessage"></p>
    </div>
  </div>
</div>

<!-- Login Form -->
<div class="page-content">
  <div class="login-container">

    <div class="library-header">
      <img src="icon.png" alt="Library Icon">
      <h1>Librix</h1>
      <p>Smart access to books and learning</p>
    </div>

    <div class="divider"></div>

    <form action="login.php" method="post" autocomplete="off">
      <div class="form-group">
        <label for="username"><b>Username</b></label>
        <input type="text" id="username" name="username" placeholder="Enter username" required>
      </div>

      <div class="form-group">
        <label for="password"><b>Password</b></label>
        <input type="password" id="password" name="password" placeholder="Enter password" required>
      </div>

      <button type="submit" class="login-btn">Login</button>
    </form>

    <p class="register-link">
      New reader?
      <a href="registration.php"><b>Create an account</b></a>
    </p>

   

  </div>
</div>

<script src="../js/modal2.js"></script>

<?php if (!empty($error)) : ?>
<script>
  showModal("<?= addslashes($error) ?>", "<?= $errorImg ?? '' ?>");
</script>
<?php endif; ?>

</body>
</html>
