<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/landingpage.css">
  <title>Inventory System</title>
</head>
<body>
  <!-- Header -->
  <header>
    <!-- Logo -->
    <img src="image/Pink Cute Simple Flower Shop Circle Logo.jpg" alt="Inventory Logo" class="logo">

 
    <nav>
      <!-- Link to Get Started Page -->
      <a href="get-started.html">
        <button class="login-btn">Get Started</button>
      </a>
      
      <!-- Link to Learn More Page -->
      <a href="learn-more.html">
        <button class="register-btn">Learn More</button>
      </a>
    </nav>
  </header>

  <!-- Hero Section -->
  <main>
    <h2>VALM Scents Inventory Management </h2>
    <p>Keeping your fragrances in order, so your business always stays fresh.</p>
    <div class="buttons">
      <!-- Link to Register Page -->
      <a href="login.php">
        <button class="get-started">Login</button>
      </a>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    © 2025 VALM Scents. All rights reserved.
  </footer>
</body>
</html>