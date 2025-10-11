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

  <div class="logout"><a href="logout.php">Logout</a></div>

  <div class="container">
    <h2>🌤️ Welcome !</h2>
    <input type="text" id="cityInput" placeholder="Enter city name">
    <button onclick="getWeather()">Get Weather</button>
    <div class="weather-info" id="weatherInfo"></div>

    <!-- Recent Locations Section -->
    <div class="recent-locations" id="recentLocations" style="display:none;">
      <h3>Recent Locations</h3>
      <div class="location-cards" id="locationCards"></div>
    </div>
  </div>

  <script>
    // ✅ Replace below API key with your valid OpenWeather API key
    const apiKey = "97c9581ba8846416f35073f479a0a4a2"; 

    window.onload = function() {
      displayRecentSearches();
    };

    async function getWeather() {
      const city = document.getElementById("cityInput").value.trim();
      const weatherInfo = document.getElementById("weatherInfo");

      if (!city) {
        weatherInfo.innerHTML = "⚠️ Please enter a city name!";
        return;
      }

      const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;

      try {
        const response = await fetch(url);
        const data = await response.json();

        // 🧠 Proper error handling for invalid key or city
        if (response.status === 401) {
          throw new Error("Invalid API key. Please generate a new one from OpenWeather.");
        }
        if (response.status === 404) {
          throw new Error("City not found. Please check the city name.");
        }

        // Display & Save Weather Data
        displayWeather(data);
        saveRecentSearch(data);
        displayRecentSearches();

      } catch (error) {
        weatherInfo.innerHTML = `❌ ${error.message}`;
      }
    }

    function displayWeather(data) {
      const weatherInfo = document.getElementById("weatherInfo");
      weatherInfo.innerHTML = `
        <h3>${data.name}, ${data.sys.country}</h3>
        <img class="icon" src="http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png">
        <p>🌡️ Temperature: ${data.main.temp}°C</p>
        <p>🌥️ Condition: ${data.weather[0].description}</p>
        <p>💧 Humidity: ${data.main.humidity}%</p>
        <p>🌬️ Wind Speed: ${data.wind.speed} m/s</p>
        <p>🌡️ Feels Like: ${data.main.feels_like}°C</p>
      `;
    }

    function saveRecentSearch(data) {
      let recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];

      const searchData = {
        name: data.name,
        country: data.sys.country,
        temp: Math.round(data.main.temp),
        feelsLike: Math.round(data.main.feels_like),
        icon: data.weather[0].icon,
        description: data.weather[0].description
      };

      recentSearches = recentSearches.filter(item => item.name !== searchData.name);
      recentSearches.unshift(searchData);

      if (recentSearches.length > 4) {
        recentSearches = recentSearches.slice(0, 4);
      }

      localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
    }

    function displayRecentSearches() {
      const recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];
      const recentLocationsDiv = document.getElementById('recentLocations');
      const locationCardsDiv = document.getElementById('locationCards');

      if (recentSearches.length === 0) {
        recentLocationsDiv.style.display = 'none';
        return;
      }

      recentLocationsDiv.style.display = 'block';
      locationCardsDiv.innerHTML = '';

      recentSearches.forEach(search => {
        const card = document.createElement('div');
        card.className = 'location-card';
        card.onclick = () => searchCity(search.name);

        card.innerHTML = `
          <h4>${search.name}</h4>
          <p>${search.country}</p>
          <img src="http://openweathermap.org/img/wn/${search.icon}.png" style="width:50px;">
          <div class="temp">${search.temp}°C</div>
          <p>RealFeel® ${search.feelsLike}°</p>
        `;

        locationCardsDiv.appendChild(card);
      });
    }

    async function searchCity(cityName) {
      document.getElementById("cityInput").value = cityName;
      await getWeather();
    }
  </script>
</body>
</html>
