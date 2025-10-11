<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dynamic Weather App</title>
  <link rel="stylesheet" href="css/weather.css">
</head>
<body id="mainBody">
  <div id="weatherEffects"></div>

  <div class="logout"><a href="logout.php">Logout</a></div>

  <div class="container">
    <h2>🌤️ Welcome!</h2>
    <div class="search-section">
      <input type="text" id="cityInput" placeholder="Enter city name">
      <button id="searchBtn">Get Weather</button>
    </div>

    <div class="weather-info" id="weatherInfo"></div>
    <div class="recent-locations" id="recentLocations" style="display:none;">
      <h3>Recent Locations</h3>
      <div class="location-cards" id="locationCards"></div>
    </div>
  </div>

  <!-- Shared background logic -->
  <script src="./src/background.js"></script>
  <!-- Weather logic -->
  <script src="./src/weather.js"></script>
</body>
</html>
