<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['is_admin'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Weather App</title>
  <link rel="stylesheet" href="css/weather.css">
</head>

<body>
  <video autoplay loop muted playsinline class="background-video">
    <source src="weather.mp4" type="video/mp4">
  </video>

  <div class="logout">
    <a href="logout.php">Logout</a>
  </div>

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

  <script src="./src/weather.js"></script>
</body>

</html>