<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Weather Forecast - Dynamic Background</title>
<link rel="stylesheet" href="css/index.css">
</head>
<body id="mainBody">

  <!-- Weather Effects Container -->
  <div class="weather-effects" id="weatherEffects"></div>

  <!-- Page Content -->
  <div class="content">
    <h1>Welcome to Weather Forecast</h1>
    <div class="sub-text">Dynamic weather-themed experience 🌦️</div>

    <?php if (isset($_SESSION['email']) && isset($_SESSION['name'])): ?>
      <div class="welcome-user">Hello, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</div>
      <a href="weather.php" class="btn weather-btn">🌤️ View Weather</a>
      <a href="logout.php" class="btn logout-btn">🚪 Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn">🔑 Login</a>
      <a href="register.php" class="btn">📝 Register</a>
    <?php endif; ?>
  </div>

  <!-- Custom Cursor -->
  <div class="cursor"></div>
  <div class="cursor-shadow"></div>

  <!-- Footer -->
  <div class="footer-name">Made with ❤️ by Divy Doshi</div>

  <script>
    // Get user's location and fetch weather
    async function getWeatherBackground() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (position) => {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;
          const apiKey = 'ca68ddbdc543058f30aa435cecc45f2f';
          
          try {
            const response = await fetch(
              `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}`
            );
            const data = await response.json();
            
            // Change background based on weather
            changeBackground(data.weather[0].main.toLowerCase(), data.weather[0].id);
          } catch (error) {
            console.log('Using default background');
            setDefaultBackground();
          }
        }, () => {
          setDefaultBackground();
        });
      } else {
        setDefaultBackground();
      }
    }

    function changeBackground(weather, weatherId) {
      const body = document.getElementById('mainBody');
      const effects = document.getElementById('weatherEffects');
      
      // Clear existing effects
      effects.innerHTML = '';
      
      // Determine time of day
      const hour = new Date().getHours();
      const isNight = hour < 6 || hour > 18;
      
      // Remove all weather classes
      body.className = '';
      
      // Apply weather-specific background and effects
      if (weather === 'clear') {
        body.classList.add(isNight ? 'night' : 'sunny');
        if (isNight) {
          addStars();
          addMoon();
        } else {
          addSun();
        }
      } else if (weather === 'clouds') {
        body.classList.add('cloudy');
        addClouds();
      } else if (weather === 'rain' || weather === 'drizzle') {
        body.classList.add('rain');
        addRain();
        addClouds();
      } else if (weather === 'thunderstorm') {
        body.classList.add('thunderstorm');
        addRain();
        addLightning();
      } else if (weather === 'snow') {
        body.classList.add('snow');
        addSnow();
      } else if (weather === 'mist' || weather === 'fog' || weather === 'haze') {
        body.classList.add('mist');
        addClouds();
      } else {
        setDefaultBackground();
      }
    }

    function setDefaultBackground() {
      const hour = new Date().getHours();
      const isNight = hour < 6 || hour > 18;
      document.getElementById('mainBody').classList.add(isNight ? 'night' : 'sunny');
      if (isNight) {
        addStars();
        addMoon();
      } else {
        addSun();
      }
    }

    function addSun() {
      const sun = document.createElement('div');
      sun.className = 'sun';
      document.body.appendChild(sun);
    }

    function addMoon() {
      const moon = document.createElement('div');
      moon.className = 'moon';
      document.body.appendChild(moon);
    }

    function addStars() {
      for (let i = 0; i < 100; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + 'vw';
        star.style.top = Math.random() * 100 + 'vh';
        star.style.animationDuration = 2 + Math.random() * 3 + 's';
        document.getElementById('weatherEffects').appendChild(star);
      }
    }

    function addRain() {
      for (let i = 0; i < 50; i++) {
        const rain = document.createElement('div');
        rain.className = 'rain-drop';
        rain.style.left = Math.random() * 100 + 'vw';
        rain.style.animationDuration = 0.5 + Math.random() * 0.5 + 's';
        rain.style.animationDelay = Math.random() * 2 + 's';
        document.getElementById('weatherEffects').appendChild(rain);
      }
    }

    function addSnow() {
      for (let i = 0; i < 50; i++) {
        const snow = document.createElement('div');
        snow.className = 'snow-flake';
        snow.innerHTML = '❄';
        snow.style.left = Math.random() * 100 + 'vw';
        snow.style.fontSize = 0.5 + Math.random() * 1 + 'rem';
        snow.style.animationDuration = 5 + Math.random() * 5 + 's';
        snow.style.animationDelay = Math.random() * 5 + 's';
        document.getElementById('weatherEffects').appendChild(snow);
      }
    }

    function addClouds() {
      for (let i = 0; i < 3; i++) {
        const cloud = document.createElement('div');
        cloud.className = 'cloud';
        cloud.style.width = 100 + Math.random() * 100 + 'px';
        cloud.style.height = 50 + Math.random() * 30 + 'px';
        cloud.style.top = Math.random() * 30 + '%';
        cloud.style.left = -200 + 'px';
        cloud.style.animationDuration = 40 + Math.random() * 40 + 's';
        cloud.style.animationDelay = Math.random() * 5 + 's';
        document.getElementById('weatherEffects').appendChild(cloud);
      }
    }

    function addLightning() {
      setInterval(() => {
        document.getElementById('mainBody').style.background = '#FFFFFF';
        setTimeout(() => {
          document.getElementById('mainBody').style.background = '';
          document.getElementById('mainBody').classList.add('thunderstorm');
        }, 100);
      }, 3000 + Math.random() * 5000);
    }

    // Custom cursor
    const cursor = document.querySelector('.cursor');
    const shadow = document.querySelector('.cursor-shadow');
    document.addEventListener('mousemove', e => {
      cursor.style.top = e.clientY + 'px';
      cursor.style.left = e.clientX + 'px';
      shadow.style.top = e.clientY + 'px';
      shadow.style.left = e.clientX + 'px';
    });

    // Initialize weather background
    getWeatherBackground();
  </script>
</body>
</html>
