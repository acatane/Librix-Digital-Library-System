<?php
include '../content/db.php';
session_start();

// store modal data here so we can trigger it after JS loads
$modal = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $modal = [
            'message' => '❌ Passwords do not match!',
            'success' => false,
            'image'   => '../image/error.png'
        ];
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $modal = [
                'message' => ' Username or Email already taken!',
                'success' => false,
                'image'   => '../image/wrong.webp'
            ];
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $modal = [
                    'message' => ' Registration successful! <br>You can now <a href="login.php">login</a>.',
                    'success' => true,
                    'image'   => '../image/check.webp'
                ];
            } else {
                $modal = [
                    'message' => '❌ Something went wrong. Try again!',
                    'success' => false,
                    'image'   => '../image/wrong.webp'
                ];
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Register - Librix Digital Library</title>

<link rel="stylesheet" href="../css/modal.css">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Segoe UI", sans-serif;
}

/* Background with books */
body {
  height: 100vh;
  background:
    linear-gradient(rgba(10,10,10,0.9), rgba(10,10,10,0.95)),
    url("../image/books-bg.jpg") no-repeat center center/cover;
  color: #eee;
}

/* Center layout */
.page-content {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

/* Register Card */
.register-container {
  background: rgba(28, 28, 28, 0.97);
  width: 420px;
  padding: 45px 40px;
  border-radius: 16px;
  box-shadow: 0 35px 70px rgba(0,0,0,0.8);
  text-align: center;
  animation: fadeSlide 0.8s ease;
  position: relative;
}

/* Orange Accent */
.register-container::before {
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
.register-container h1 {
  font-size: 30px;
  color: #ff8c00;
  margin-bottom: 8px;
}

.register-container p.subtext {
  font-size: 14px;
  color: #aaa;
  margin-bottom: 20px;
}

/* Form */
.form-group {
  text-align: left;
  margin-bottom: 15px;
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
.register-btn {
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

.register-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(255,140,0,0.35);
}

/* Login link */
.login-link {
  margin-top: 20px;
  font-size: 14px;
  color: #aaa;
}

.login-link a {
  color: #ff8c00;
  text-decoration: none;
}

/* Animation */
@keyframes fadeSlide {
  from {
    opacity: 0;
    transform: translateY(18px);
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
    <?php if (!empty($modal['image'])): ?>
      <img src="<?= htmlspecialchars($modal['image']) ?>" alt="status icon" style="width:30vh; height:30vh;">
    <?php endif; ?>
    <button class="close-btn" aria-label="Close">&times;</button>
    <p id="modalMessage"></p>
  </div>
</div>

<!-- Register -->
<div class="page-content">
  <div class="register-container">
    <h1>Create Account</h1>
    <p class="subtext">Join Librix Digital Library</p>

    <form action="registration.php" method="post" autocomplete="off">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
      </div>

      <button type="submit" class="register-btn">Register</button>
    </form>

    <p class="login-link">
      Already have an account?
      <a href="login.php">Login here</a>
    </p>
  </div>
</div>

<script src="../js/modal.js"></script>

<script>
(function(){
  const modalData = <?php echo json_encode($modal ?? null); ?>;
  if (modalData) {
    document.addEventListener('DOMContentLoaded', function() {
      if (typeof showModal === 'function') {
        showModal(modalData.message, !!modalData.success);
      } else {
        alert(modalData.message.replace(/<[^>]*>/g,''));
      }
    });
  }
})();
</script>

</body>
</html>
