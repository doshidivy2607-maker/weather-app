<?php include "./components/header.php"; ?>

<link rel="stylesheet" href="css/weather.css">

<?php include "./components/body.php"; ?>


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

<script src="./src/weather.js"></script>

<?php include "./components/footer.php"; ?>