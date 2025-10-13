// ✅ Replace below API key with your valid OpenWeather API key
const apiKey = "9395a30196f7ea25c4527ea666ca6886";

window.onload = function() {
  displayRecentSearches();
  document.getElementById("searchBtn").addEventListener("click", getWeather);
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

    if (response.status === 401) throw new Error("Invalid API key.");
    if (response.status === 404) throw new Error("City not found.");

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
