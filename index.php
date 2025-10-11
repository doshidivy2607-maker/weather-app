<?php
include "./components/header.php";
?>

<link rel="stylesheet" href="./css/index.css">

<?php
include "./components/body.php";
?>

<!-- Page Content -->
<div class="content">
  <h1>Welcome to Weather Forecast</h1>
  <div class="sub-text">Dynamic weather-themed experience 🌦️</div>

  <?php if (isset($_SESSION['email']) && isset($_SESSION['name'])): ?>
    <div class="welcome-user">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋 </div>
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

<?php
include "./components/footer.php";
?>