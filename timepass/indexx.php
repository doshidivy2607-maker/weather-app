<?php
// index.php
// Simple page to show forecast UI. JS will call weather.php?q={city}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Weather App — 10-day Forecast</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="page">
    <header class="topbar">
      <div class="brand">🌦️ Weather</div>
      <form id="searchForm" class="search">
        <input id="cityInput" placeholder="Enter city (e.g., Ahmedabad)" required>
        <button type="submit">Search</button>
      </form>
    </header>

    <main class="content">
      <section class="forecast-section">
        <h2>10-day forecast</h2>
        <div id="forecastList" class="forecast-list">
          <!-- Forecast items injected by JS -->
        </div>
      </section>

      <section class="current-section">
        <h2>Current conditions</h2>
        <div class="current-cards">
          <div class="card big" id="card-temp">
            <div class="card-title">Now</div>
            <div class="card-main" id="now-main">
              <!-- temp + main -->
            </div>
          </div>

          <div class="card" id="card-wind">
            <div class="card-title">Wind</div>
            <div class="card-body">
              <div id="wind-rose" class="wind-rose">
                <div class="wind-needle" id="wind-needle"></div>
              </div>
              <div id="wind-speed" class="card-value"></div>
              <div id="wind-desc" class="card-sub">—</div>
            </div>
          </div>

          <div class="card" id="card-humidity">
            <div class="card-title">Humidity</div>
            <div class="card-body vertical-center">
              <div class="humidity-pill">
                <div id="humidity-fill" class="humidity-fill"></div>
              </div>
              <div id="humidity-value" class="card-value"></div>
              <div id="dewpoint" class="card-sub">Dew point —</div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer">Powered by OpenWeather · Data is live</footer>
  </div>

  <script src="src/script.js"></script>
</body>
</html>
