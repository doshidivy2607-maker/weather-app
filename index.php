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

  <!-- Page Content -->
  <div class="content">
    <h1>Welcome to Weather Forecast</h1>
    <div class="sub-text">Dynamic weather-themed experience 🌦️</div>

    <?php if (isset($_SESSION['email']) && isset($_SESSION['name'])): ?>
      <div class="welcome-user">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</div>
      <a href="weather.php" class="btn weather-btn">🌤️ View Weather</a>
      <a href="logout.php" class="btn logout-btn">🚪 Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn">🔑 Login</a>
      <a href="register.php" class="btn">📝 Register</a>
    <?php endif; ?>
  </div>

  <!-- Custom Cursor -->
  <div class="cursor"></div>
  <div class="cursor-shadow"></div>

  <!-- Footer -->
  <div class="footer-name">Made with ❤️ by Divy Doshi</div>

  <script src="./src/background.js"></script>
</body>
</html>
