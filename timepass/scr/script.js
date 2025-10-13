// src/script.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('searchForm');
  const cityInput = document.getElementById('cityInput');
  const forecastList = document.getElementById('forecastList');
  const nowMain = document.getElementById('now-main');
  const windSpeedEl = document.getElementById('wind-speed');
  const windDescEl = document.getElementById('wind-desc');
  const windNeedle = document.getElementById('wind-needle');
  const humidityFill = document.getElementById('humidity-fill');
  const humidityValue = document.getElementById('humidity-value');
  const dewpointEl = document.getElementById('dewpoint');

  form.addEventListener('submit', e => {
    e.preventDefault();
    const city = cityInput.value.trim();
    if (!city) return;
    fetchWeather(city);
  });

  // initial load (example city)
  fetchWeather('Ahmedabad');

  function fetchWeather(city) {
    forecastList.innerHTML = '<div class="loading">Loading...</div>';
    fetch(`weather.php?q=${encodeURIComponent(city)}`)
      .then(r => r.json())
      .then(render)
      .catch(err => {
        forecastList.innerHTML = `<div class="error">Failed to fetch: ${err.message}</div>`;
      });
  }

  function iconFromWeather(w) {
    // simple mapping using OpenWeather icon code if present, else fallback emoji
    if (w && w.icon) return `http://openweathermap.org/img/wn/${w.icon}@2x.png`;
    // fallback
    return null;
  }

  function render(payload) {
    if (!payload || payload.error) {
      forecastList.innerHTML = `<div class="error">${payload?.error || 'No data'}</div>`;
      return;
    }
    const place = payload.place || '';
    const d = payload.data;
    document.querySelector('.brand').textContent = `🌦️ Weather — ${place}`;

    // CURRENT
    const curr = d.current;
    nowMain.innerHTML = `
      <div class="now-temp">${Math.round(curr.temp)}°</div>
      <div class="now-desc">${curr.weather?.[0]?.description || ''}</div>
    `;

    // WIND
    const windKph = (curr.wind_speed * 3.6).toFixed(0); // m/s -> km/h
    windSpeedEl.textContent = `${windKph} kph`;
    windDescEl.textContent = `${windKph > 25 ? 'Strong' : 'Light'} · From ${windDir(curr.wind_deg)}`;
    // rotate needle (wind_deg is degrees coming from; to point the wind coming FROM we'd rotate accordingly)
    windNeedle.style.transform = `rotate(${curr.wind_deg}deg)`;

    // HUMIDITY
    humidityValue.textContent = `${curr.humidity}%`;
    humidityFill.style.height = `${curr.humidity}%`;
    dewpointEl.textContent = `Dew point ${Math.round(curr.dew_point)}°`;

    // FORECAST (daily) - show up to 10 days
    forecastList.innerHTML = '';
    const days = d.daily.slice(0, 10);
    days.forEach((day, idx) => {
      const date = new Date(day.dt * 1000);
      const dayLabel = idx === 0 ? 'Today' : date.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'short' });
      const iconUrl = iconFromWeather(day.weather?.[0]) || '';
      const pop = Math.round((day.pop ?? 0) * 100); // probability of precipitation
      const min = Math.round(day.temp.min);
      const max = Math.round(day.temp.max);

      const item = document.createElement('div');
      item.className = 'forecast-item';
      item.innerHTML = `
        <div class="f-left">
          <div class="f-day">${dayLabel}</div>
          <div class="f-pop">${pop > 0 ? `<span class="pop">${pop}%</span>` : ''}${iconUrl ? `<img src="${iconUrl}" alt="" class="wicon">` : ''}</div>
        </div>
        <div class="f-right">
          <div class="f-temp">${max}°/${min}°</div>
        </div>
      `;
      forecastList.appendChild(item);
    });
  }

  function windDir(deg) {
    const dirs = ['N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW'];
    const ix = Math.round(deg / 22.5) % 16;
    return dirs[ix];
  }
});
