// ✅ Replace below API key with your valid OpenWeather API key
const apiKey = 'c1b76d1e7eb86aa1c1b0ce08d0627d91';

// Try to use geolocation on load to fetch realtime weather and inform background.js
function fetchWeatherByCoords(lat, lon, isMini = false) {
  const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric`;
  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      displayWeather(data, isMini);
      // If background.js exposes a global changeBackgroundByWeather function, call it
      if (window.changeBackgroundByWeather && typeof window.changeBackgroundByWeather === 'function') {
        try { window.changeBackgroundByWeather(data); } catch (e) { /* ignore */ }
      }
    })
    .catch((err) => console.error('Error fetching weather by coords', err));
}

// on load, attempt geolocation-based realtime weather fetch
function tryGeolocationRealtime() {
  if (!navigator || !navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(
    (position) => {
      const { latitude, longitude } = position.coords;
      fetchWeatherByCoords(latitude, longitude, false);
    },
    (err) => {
      // silently ignore geolocation error — user may deny permission
      console.warn('Geolocation not available or permission denied', err);
    },
    { enableHighAccuracy: false, timeout: 7000 }
  );
}

// run once on script load
tryGeolocationRealtime();

window.onload = function() {
  displayRecentSearches();
  const mainBtn = document.getElementById("searchBtn");
  if (mainBtn) mainBtn.addEventListener("click", () => getWeather(false));

  const miniBtn = document.getElementById("searchBtnMini");
  if (miniBtn) miniBtn.addEventListener("click", () => getWeather(true));
};

async function getWeather(isMini = false) {
  // Determine city from main input or mini input
  const mainInput = document.getElementById('cityInput');
  const miniInput = document.getElementById('cityInputMini');
  const city = (mainInput ? mainInput.value : (miniInput ? miniInput.value : '')).trim();

  const weatherInfo = isMini ? document.getElementById('weatherInfoMini') : document.getElementById('weatherInfo');

  if (!city) {
    if (weatherInfo) weatherInfo.innerHTML = "⚠️ Please enter a city name!";
    return;
  }

  const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;


  try {
    const response = await fetch(url);
    const data = await response.json();

    if (response.status === 401) throw new Error("Invalid API key.");
    if (response.status === 404) throw new Error("City not found.");

    displayWeather(data, isMini);
    saveRecentSearch(data);
    displayRecentSearches();
  } catch (error) {
    if (weatherInfo) weatherInfo.innerHTML = `❌ ${error.message}`;
  }
}

function displayWeather(data, isMini = false) {
  const weatherInfo = isMini ? document.getElementById('weatherInfoMini') : document.getElementById('weatherInfo');
  if (!weatherInfo) return;

  if (isMini) {
    weatherInfo.innerHTML = `
      <div class="weather-card">
        <h4>${data.name}, ${data.sys.country}</h4>
        <img class="icon" src="http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png" alt="${data.weather[0].description}">
        <div>🌡️ ${Math.round(data.main.temp)}°C • ${data.weather[0].description}</div>
      </div>
    `;
  } else {
    weatherInfo.innerHTML = `
      <div class="weather-card">
        <h3>${data.name}, ${data.sys.country}</h3>
        <img class="icon" src="http://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png">
        <p>🌡️ Temperature: ${data.main.temp}°C</p>
        <p>🌥️ Condition: ${data.weather[0].description}</p>
        <p>💧 Humidity: ${data.main.humidity}%</p>
        <p>🌬️ Wind Speed: ${data.wind.speed} m/s</p>
        <p>🌡️ Feels Like: ${data.main.feels_like}°C</p>
      </div>
    `;
  }
}

function saveRecentSearch(data) {
  let recent = JSON.parse(localStorage.getItem("recentSearches")) || [];

  const item = {
    name: data.name,
    country: data.sys.country,
    temp: Math.round(data.main.temp),
    feelsLike: Math.round(data.main.feels_like),
    icon: data.weather[0].icon,
    description: data.weather[0].description
  };

  recent = recent.filter(r => r.name !== item.name);
  recent.unshift(item);
  if (recent.length > 4) recent = recent.slice(0, 4);

  localStorage.setItem("recentSearches", JSON.stringify(recent));
}

function displayRecentSearches() {
  const recent = JSON.parse(localStorage.getItem("recentSearches")) || [];
  const container = document.getElementById("recentLocations");
  const cards = document.getElementById("locationCards");

  if (!recent.length) {
    container.style.display = "none";
    return;
  }

  container.style.display = "block";
  cards.innerHTML = "";

  recent.forEach(item => {
    const card = document.createElement("div");
    card.className = "location-card";
    card.onclick = () => searchCity(item.name);
    card.innerHTML = `
      <h4>${item.name}</h4>
      <p>${item.country}</p>
      <img src="http://openweathermap.org/img/wn/${item.icon}.png" alt="${item.description}">
      <div class="temp">${item.temp}°C</div>
      <p>RealFeel® ${item.feelsLike}°</p>
    `;
    cards.appendChild(card);
  });
}

async function searchCity(city) {
  document.getElementById("cityInput").value = city;
  await getWeather();
}
