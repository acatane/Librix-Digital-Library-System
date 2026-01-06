<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Inventory System</title>
  <link rel="stylesheet" href="../css/home.css">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    /* Base & Reset */
    * { box-sizing: border-box; margin:0; padding:0; font-family:'Segoe UI', sans-serif; }
    body { background: #1f1f1f; color:#fff; min-height:100vh; display:flex; flex-direction:column; position: relative; overflow:hidden; }

    /* Header */
    header {
        background: linear-gradient(90deg, #ff4500, #ff7f50);
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 6px 20px rgba(0,0,0,0.6);
        z-index: 10;
    }
    header h1 { font-size: 26px; font-weight: 700; letter-spacing: 1px; }

    /* Navbar */
    header nav {
      display: flex;
      gap: 25px;
      align-items: center;
    }
    header nav a {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s, transform 0.2s;
    }
    header nav a:hover { color: #ffe0b2; transform: translateY(-2px); }

    header nav a.logout-btn {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 8px;
        transition: background 0.3s, transform 0.2s;
        color:#430202;
    }
    header nav a.logout-btn:hover {
        background: rgba(255,255,255,0.35);
        transform: translateY(-2px);
    }

    /* Hero Section */
    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 50px 20px;
      z-index: 5;
      position: relative;
    }
    main h2 {
      font-size: 36px;
      margin-bottom: 15px;
      background: linear-gradient(90deg, #ff7f50, #ffd280);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      opacity: 0;
      transform: translateY(-20px);
      animation: fadeSlide 1.5s ease forwards;
    }
    @keyframes fadeSlide {
      to { opacity: 1; transform: translateY(0); }
    }

    main p {
      font-size: 18px;
      max-width: 600px;
      margin-bottom: 30px;
      line-height: 1.6;
      color: #ddd;
      min-height: 24px; /* for typing effect */
    }

    .buttons {
      display: flex;
      gap: 20px;
    }
    .manage-btn {
      background: linear-gradient(90deg, #ff4500, #ff7f50);
      color: #fff;
      border: none;
      padding: 12px 28px;
      font-size: 16px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
      transition: transform 0.2s, background 0.3s;
      animation: pulseGlow 2s infinite;
    }
    .manage-btn:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #ff6333, #ffa07a);
    }
    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 10px #ff7f50, 0 0 20px #ff4500; }
      50% { box-shadow: 0 0 20px #ffd280, 0 0 40px #ff6333; }
    }

    /* Quote Box */
    .quote-box {
      margin-top: 40px;
      background: rgba(255,255,255,0.05);
      padding: 20px 30px;
      border-radius: 12px;
      font-style: italic;
      color: #ffdab9;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.4);
      opacity: 0;
      animation: fadeIn 2s ease-in forwards;
      animation-delay: 1s;
    }
    @keyframes fadeIn {
      to { opacity:1; }
    }

    /* Floating background icons */
    .floating {
      position: absolute;
      font-size: 28px;
      color: rgba(255,255,255,0.08);
      animation: drift 15s infinite linear;
      z-index: 1;
    }
    @keyframes drift {
      0% { transform: translate(0,0) rotate(0); }
      25% { transform: translate(20px,-30px) rotate(15deg); }
      50% { transform: translate(-15px,-60px) rotate(-10deg); }
      75% { transform: translate(25px,-20px) rotate(5deg); }
      100% { transform: translate(0,0) rotate(0); }
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 15px;
      background: #2c2c2c;
      font-size: 14px;
      color: #aaa;
      box-shadow: 0 -3px 12px rgba(0,0,0,0.5);
      z-index: 10;
    }

    /* Responsive */
    @media screen and (max-width:768px) {
        main h2 { font-size:28px; }
        main p { font-size:16px; }
        .manage-btn { font-size:14px; padding:10px 20px; }
        header nav { gap: 15px; }
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

  <!-- Hero Section -->
  <main>
    <h2 id="greeting">Welcome!</h2>
    <p id="subtitle"></p>

    <div class="buttons">
      <a href="manage_books.php">
        <button class="manage-btn">📚 Browse Books</button>
      </a>
    </div>

    <!-- Creative Add-on: Quote of the Day -->
    <section class="quote-box">
      <i data-lucide="sparkles"></i>
      <p id="quote">“Loading your daily inspiration...”</p>
    </section>

    <!-- Floating icons background -->
    <div class="floating" style="top:15%; left:10%;">📚</div>
    <div class="floating" style="top:40%; left:80%;">✨</div>
    <div class="floating" style="top:70%; left:25%;">📖</div>
    <div class="floating" style="top:55%; left:60%;">🌙</div>
  </main>

  <!-- Footer -->
  <footer>
    © 2025 Inventory System. All rights reserved.
  </footer>

  <!-- Load Lucide Icons & Script -->
  <script>
    lucide.createIcons();

    // Random Greetings
    const greetings = [
      "Your Reading Journey Continues 🚀"
    ];
    const subtitles = [
      "Because every great story deserves to be remembered."
    ];
    const greeting = greetings[Math.floor(Math.random() * greetings.length)];
    const subtitle = subtitles[Math.floor(Math.random() * subtitles.length)];
    document.getElementById("greeting").textContent = greeting;

    // Typing effect for subtitle
    let i = 0;
    function typeEffect() {
      if (i < subtitle.length) {
        document.getElementById("subtitle").textContent += subtitle.charAt(i);
        i++;
        setTimeout(typeEffect, 40);
      }
    }
    typeEffect();

    // Daily quotes
    const quotes = [
      "“A room without books is like a body without a soul.” – Cicero",
      "“Knowledge is power.” – Francis Bacon",
      "“So many books, so little time.” – Frank Zappa",
      "“Libraries are the treasure chests of ideas.”",
      "“Reading is dreaming with open eyes.”"
    ];
    document.getElementById("quote").textContent = 
      quotes[Math.floor(Math.random() * quotes.length)];
  </script>
</body>
</html>
