// ✅ Handles dynamic weather background (shared by login, index, weather pages)
const weatherBgApi = "ca68ddbdc543058f30aa435cecc45f2f";

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
    if (isNight) addStars();
  } else if (weather === "clouds") {
    body.classList.add("cloudy");
    addClouds();
  } else if (["rain", "drizzle"].includes(weather)) {
    body.classList.add("rain");
    addRain();
  } else if (weather === "snow") {
    body.classList.add("snow");
    addSnow();
  } else if (["mist", "fog", "haze"].includes(weather)) {
    body.classList.add("mist");
    addClouds();
  } else {
    setDefaultBackground();
  }
}

function setDefaultBackground() {
  console.log("Set proper default backgrond");
  
  const hour = new Date().getHours();
  const isNight = hour < 6 || hour > 18;
  document.getElementById("mainBody").classList.add(isNight ? "night" : "clear");
  if (isNight) addStars();
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

// Auto-init
getWeatherBackground();
