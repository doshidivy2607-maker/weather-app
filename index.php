<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Weather Forecast - Dynamic Background</title>
<link rel="stylesheet" href="./css/index.css">
</head>
<body id="mainBody">

  <!-- Weather Effects Container -->
  <div class="weather-effects" id="weatherEffects"></div>

  <!-- Visual Elements (sun / moon) -->
  <div id="sceneExtras">
    <div class="sun" id="sun" aria-hidden="true" style="display:none"></div>
    <div class="moon" id="moon" aria-hidden="true" style="display:none"></div>
  </div>

  <!-- Page Content -->
  <div class="content">
    <h1>Welcome to Weather Forecast</h1>
    <div class="sub-text">Dynamic weather-themed experience 🌦️</div>

    <?php if (isset($_SESSION['email']) && isset($_SESSION['name'])): ?>
      <div class="welcome-user">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</div>
      <a href="weather.php" class="btn weather-btn">🌤️ View Weather</a>
      <a href="logout.php" class="btn logout-btn">🚪 Logout</a>
    <?php else: ?>
      <div class="welcome-user">Hello, Guest 👋</div>
      <div class="sub-text" id="guestHint">Register to save searches and view personalized weather.</div>
      <div style="margin-top:12px;">
        <a href="register.php" class="btn">📝 Register</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Custom Cursor -->
  <div class="cursor"></div>
  <div class="cursor-shadow"></div>

  <!-- Footer -->
  <div class="footer-name">Made with by Divy Doshi and Mishri Panchal </div>

  <script src="./src/background.js"></script>
  <!-- weather.js is only loaded on the weather page now -->
</body>
</html>
