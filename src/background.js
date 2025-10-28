// ✅ Handles dynamic weather background (shared by login, index, weather pages)

const weatherBgApi = "e989953360101c93cd4f8279d969a659";

// Cleanup any leftover page-transition artifacts from older versions
(function cleanupOldTransitions() {
  try {
    const oldStyle = document.getElementById('page-transition-style');
    if (oldStyle && oldStyle.parentNode) oldStyle.parentNode.removeChild(oldStyle);
    const oldOverlay = document.getElementById('page-transition-overlay');
    if (oldOverlay && oldOverlay.parentNode) oldOverlay.parentNode.removeChild(oldOverlay);
    const body = document.body || document.getElementsByTagName('body')[0];
    if (body) {
      ['rotate-in', 'rotate-out', 'active', 'fade-out'].forEach(c => body.classList.remove(c));
      body.style.transform = '';
      body.style.transition = '';
      body.style.opacity = '';
    }
  } catch (e) {
    /* ignore cleanup errors */
  }
})();

// If you want to disable all page-change visual effects, set this to true.
// We'll clear any existing particles and skip initializing animations/cursor/background.
// Set to false to enable dynamic weather visuals and interactive effects.
const DISABLE_PAGE_EFFECTS = false;

// Always set a simple time-based theme (day/night) so CSS reflects local time for any user.
(function setTimeTheme() {
  try {
    const hour = new Date().getHours();
    const body = document.body || document.getElementsByTagName('body')[0];
    if (!body) return;
    // clear previous time/day classes
    body.classList.remove('day', 'night', 'time-morning', 'time-afternoon', 'time-evening', 'time-night');

    // set simple day/night
    if (hour >= 6 && hour < 18) {
      body.classList.add('day');
    } else {
      body.classList.add('night');
    }

    // set finer time slots
    if (hour >= 6 && hour < 12) {
      body.classList.add('time-morning');
    } else if (hour >= 12 && hour < 17) {
      body.classList.add('time-afternoon');
    } else if (hour >= 17 && hour < 20) {
      body.classList.add('time-evening');
    } else {
      body.classList.add('time-night');
    }
  } catch (e) { /* ignore */ }
})();

if (DISABLE_PAGE_EFFECTS) {
  try {
    // Remove any existing weather particles/elements
    const effects = document.getElementById('weatherEffects');
    if (effects) effects.innerHTML = '';
    const sun = document.getElementById('sun'); if (sun) sun.style.display = 'none';
    const moon = document.getElementById('moon'); if (moon) moon.style.display = 'none';
    const cursor = document.querySelector('.cursor'); if (cursor && cursor.parentNode) cursor.parentNode.removeChild(cursor);
    const shadow = document.querySelector('.cursor-shadow'); if (shadow && shadow.parentNode) shadow.parentNode.removeChild(shadow);

    // Inject a small CSS override to disable animations/transitions
    if (!document.getElementById('disable-effects-style')) {
      const s = document.createElement('style');
      s.id = 'disable-effects-style';
      s.innerHTML = `
        * { animation: none !important; transition: none !important; }
        .weather-effects, .star, .rain-drop, .snow-flake, .cloud, .sun, .moon, .cursor, .cursor-shadow { display: none !important; }
      `;
      document.head.appendChild(s);
    }
  } catch (e) { /* ignore */ }
  // Stop further initialization below by exiting early
  // (wrap remaining init in a no-op)
  console.info('Page visual effects disabled by DISABLE_PAGE_EFFECTS flag.');
  // Prevent the rest of the file from running by returning from a closure
} else {
  // Continue with normal initialization

  // Auto-init background and show sun/moon only when effects enabled
  getWeatherBackground();

  // Custom cursor removed: no DOM nodes will be injected for the cursor

  // Ensure sun/moon elements and minimal CSS exist so pages without admin CSS still show them
  (function ensureSunMoonElements() {
    try {
      if (!document.getElementById('sun')) {
        const sun = document.createElement('div');
        sun.id = 'sun';
        sun.className = 'sun';
        document.body.appendChild(sun);
      }
      if (!document.getElementById('moon')) {
        const moon = document.createElement('div');
        moon.id = 'moon';
        moon.className = 'moon';
        document.body.appendChild(moon);
      }

      // inject minimal CSS for sun/moon if not present
      if (!document.getElementById('sun-moon-style')) {
        const s = document.createElement('style');
        s.id = 'sun-moon-style';
        s.innerHTML = `
        .sun, .moon { position: fixed; top: 6%; right: 6%; width: 90px; height: 90px; border-radius: 50%; z-index: 0; box-shadow: 0 8px 40px rgba(0,0,0,0.15); }
        .sun { background: radial-gradient(circle, #FFD54F 0%, #FFB300 60%); filter: drop-shadow(0 6px 18px rgba(255,180,0,0.25)); animation: sunPulse 4s ease-in-out infinite; }
        .moon { background: radial-gradient(circle, #f8f3d4 0%, #e6dd9f 70%); box-shadow: 0 6px 28px rgba(200,200,220,0.2); animation: moonGlow 5s ease-in-out infinite alternate; }
        @keyframes sunPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.06); } }
        @keyframes moonGlow { 0% { transform: scale(1); } 100% { transform: scale(1.06); } }
        `;
        document.head.appendChild(s);
      }
    } catch (e) { /* ignore DOM errors */ }
  })();

  // Custom cursor removed: no animation will run

}

// Rest of file functions (addStars, addRain, etc.) will remain defined



async function getWeatherBackground() {
  console.log("starting fetching background");
  if (!navigator.geolocation) return setDefaultBackground();

  navigator.geolocation.getCurrentPosition(async (pos) => {
    const lat = pos.coords.latitude;
    const lon = pos.coords.longitude;
    try {
      const res = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${weatherBgApi}`);
      const data = await res.json();
      changeBackground(data.weather[0].main.toLowerCase());
    } catch {
      setDefaultBackground();
    }
  }, setDefaultBackground);
}

function changeBackground(weather) {
  const body = document.getElementById("mainBody");
  const effects = document.getElementById("weatherEffects");
  effects.innerHTML = "";
  body.className = "";

  const hour = new Date().getHours();
  const isNight = hour < 6 || hour > 18;

  if (weather === "clear") {
    body.classList.add(isNight ? "night" : "sunny");
    showSun(!isNight);
    if (isNight) addStars();
  } else if (weather === "clouds") {
    body.classList.add("cloudy");
    showSun(!isNight);
    addClouds();
  } else if (["rain", "drizzle"].includes(weather)) {
    body.classList.add("rain");
    showSun(!isNight);
    addRain();
  } else if (weather === "snow") {
    body.classList.add("snow");
    showSun(!isNight);
    addSnow();
  } else if (["mist", "fog", "haze"].includes(weather)) {
    body.classList.add("mist");
    showSun(!isNight);
    addClouds();
  } else {
    setDefaultBackground();
  }
}

// Expose a safer wrapper for other scripts to pass raw weather API responses
window.changeBackgroundByWeather = function (weatherData) {
  try {
    // some callers may pass a full OpenWeather response; adapt shape if necessary
    if (!weatherData) return;
    // If the structure is the OpenWeather current weather object, it has weather[0].main
    const primary = (weatherData.weather && weatherData.weather[0] && weatherData.weather[0].main) || weatherData.main || '';
    if (primary && typeof primary === 'string') {
      changeBackground(primary.toLowerCase());
    } else {
      // fallback: try to infer from raw object
      try { changeBackground('clear'); } catch (e) { /* ignore */ }
    }
  } catch (e) {
    console.warn('changeBackgroundByWeather failed', e);
  }
};

function setDefaultBackground() {
  console.log("Set proper default backgrond");
  const hour = new Date().getHours();
  const isNight = hour < 6 || hour > 18;
  document.getElementById("mainBody").classList.add(isNight ? "night" : "clear");
  if (isNight) addStars();
  showSun(!isNight);
}

function addStars() {
  for (let i = 0; i < 50; i++) {
    const s = document.createElement("div");
    s.className = "star";
    s.style.left = Math.random() * 100 + "vw";
    s.style.top = Math.random() * 100 + "vh";
    s.style.animationDuration = 2 + Math.random() * 3 + "s";
    document.getElementById("weatherEffects").appendChild(s);
  }
}

function addRain() {
  for (let i = 0; i < 40; i++) {
    const r = document.createElement("div");
    r.className = "rain-drop";
    r.style.left = Math.random() * 100 + "vw";
    r.style.animationDuration = 0.5 + Math.random() * 0.5 + "s";
    document.getElementById("weatherEffects").appendChild(r);
  }
}

function addSnow() {
  for (let i = 0; i < 40; i++) {
    const s = document.createElement("div");
    s.className = "snow-flake";
    s.innerHTML = "❄";
    s.style.left = Math.random() * 100 + "vw";
    s.style.fontSize = 0.5 + Math.random() * 1 + "rem";
    s.style.animationDuration = 5 + Math.random() * 5 + "s";
    document.getElementById("weatherEffects").appendChild(s);
  }
}

function addClouds() {
  for (let i = 0; i < 3; i++) {
    const c = document.createElement("div");
    c.className = "cloud";
    c.style.width = 100 + Math.random() * 100 + "px";
    c.style.height = 50 + Math.random() * 30 + "px";
    c.style.top = Math.random() * 30 + "%";
    c.style.left = "-200px";
    c.style.animationDuration = 40 + Math.random() * 40 + "s";
    document.getElementById("weatherEffects").appendChild(c);
  }
}

// Show or hide sun/moon based on boolean (true => day/sun, false => night/moon)
function showSun(isDay) {
  const sun = document.getElementById('sun');
  const moon = document.getElementById('moon');
  if (sun) sun.style.display = isDay ? 'block' : 'none';
  if (moon) moon.style.display = isDay ? 'none' : 'block';
}

// Page transitions removed: no 3D rotate or injected CSS. Navigation is immediate.