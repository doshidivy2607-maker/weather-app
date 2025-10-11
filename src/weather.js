// ✅ Replace below API key with your valid OpenWeather API key
const apiKey = "97c9581ba8846416f35073f479a0a4a2";

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
      <img src="http://openweathermap.org/img/wn/${search.icon}.png" alt="${search.description}">
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
