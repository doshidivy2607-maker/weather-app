<?php
session_start();
include "db.php";

$msg = "";

// Check if user is admin
if (isset($_SESSION['email'])) {
    header("Location: weather.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($name) || empty($email) || empty($password)) {
        $msg = "⚠️ Please fill out all fields.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM weather_app WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $msg = "⚠️ This email is already registered!";
        } else {
            mysqli_stmt_close($check);
            
            try {
                $stmt = mysqli_prepare($conn, "INSERT INTO weather_app (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
                mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
                
                if (mysqli_stmt_execute($stmt)) {
                    $msg = "✅ Registration successful! You can now login.";
                    $_SESSION['registration_success'] = true;

                    header("Location: login.php");
                    exit();
                } else {
                    $msg = "❌ Error: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $msg = "⚠️ This email is already registered!";
                } else {
                    $msg = "❌ Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Weather App</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Arial', sans-serif;
        overflow: hidden;
        transition: background 1s ease;
        position: relative;
    }

    /* Dynamic Weather Backgrounds */
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    body.clear { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }
    body.sunny { background: linear-gradient(135deg, #FDB813 0%, #FF6B6B 100%); }
    body.cloudy { background: linear-gradient(135deg, #B0BEC5 0%, #546E7A 100%); }
    body.rain { background: linear-gradient(135deg, #2C3E50 0%, #4CA1AF 100%); }
    body.thunderstorm { background: linear-gradient(135deg, #1A237E 0%, #4A148C 100%); }
    body.snow { background: linear-gradient(135deg, #E0EAFC 0%, #CFDEF3 100%); }
    body.mist, body.fog, body.haze { background: linear-gradient(135deg, #757F9A 0%, #D7DDE8 100%); }
    body.night { background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }

    /* Weather Effects */
    .weather-effects {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
        overflow: hidden;
    }

    .rain-drop {
        position: absolute;
        bottom: 100%;
        width: 2px;
        height: 50px;
        background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.6));
        animation: fall linear infinite;
    }

    @keyframes fall {
        to { transform: translateY(100vh); }
    }

    .snow-flake {
        position: absolute;
        top: -10px;
        color: white;
        font-size: 1rem;
        animation: snowfall linear infinite;
    }

    @keyframes snowfall {
        to { transform: translateY(100vh) rotate(360deg); }
    }

    .star {
        position: absolute;
        width: 2px;
        height: 2px;
        background: #fff;
        border-radius: 50%;
        animation: twinkle linear infinite;
    }

    @keyframes twinkle {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 1; }
    }

    .cloud {
        position: absolute;
        background: rgba(255,255,255,0.7);
        border-radius: 50%;
        animation: moveClouds 60s linear infinite;
    }

    .cloud::before, .cloud::after {
        content: '';
        position: absolute;
        background: rgba(255,255,255,0.7);
        border-radius: 50%;
    }

    @keyframes moveClouds {
        0% { transform: translateX(0); }
        100% { transform: translateX(120vw); }
    }

    /* Animated Container */
    .container {
        background: rgba(255, 255, 255, 0.1);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        width: 90%;
        max-width: 450px;
        text-align: center;
        position: relative;
        z-index: 10;
        animation: slideUp 0.8s ease-out, float 3s ease-in-out infinite;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    /* Animated Table Header */
    .header-table {
        width: 100%;
        margin-bottom: 25px;
    }

    .header-table td {
        padding: 8px;
        font-size: 2.5rem;
        font-weight: bold;
        color: #fff;
        text-shadow: 3px 3px 15px rgba(0,0,0,0.5);
        letter-spacing: 4px;
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from {
            text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #667eea;
        }
        to {
            text-shadow: 0 0 20px #fff, 0 0 30px #ff4081, 0 0 40px #ff4081;
        }
    }

    /* Animated Input Fields */
    .input-group {
        margin: 18px 0;
        position: relative;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .input-group:nth-child(1) { animation-delay: 0.2s; }
    .input-group:nth-child(2) { animation-delay: 0.4s; }
    .input-group:nth-child(3) { animation-delay: 0.6s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        outline: none;
        font-size: 15px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        color: #fff;
        font-weight: 500;
    }

    input[type="text"]::placeholder,
    input[type="email"]::placeholder,
    input[type="password"]::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: #fff;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        transform: scale(1.02);
    }

    /* Animated Submit Button */
    input[type="submit"] {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 15px 20px;
        border-radius: 50px;
        cursor: pointer;
        font-size: 17px;
        width: 100%;
        font-weight: bold;
        transition: all 0.4s ease;
        margin-top: 20px;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        animation: fadeInUp 0.6s ease-out 0.8s backwards, pulse 2s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }

    input[type="submit"]::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    input[type="submit"]:hover::before {
        width: 300px;
        height: 300px;
    }

    input[type="submit"]:hover {
        transform: scale(1.08);
        box-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
        }
        50% {
            box-shadow: 0 0 25px rgba(118, 75, 162, 0.8);
        }
    }

    /* Message Animation */
    .message {
        margin: 20px 0;
        padding: 15px;
        border-radius: 15px;
        font-weight: bold;
        font-size: 14px;
        backdrop-filter: blur(5px);
        animation: bounceIn 0.5s ease-out;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .success {
        background: rgba(212, 237, 218, 0.9);
        color: #155724;
        border: 2px solid rgba(195, 230, 203, 1);
    }

    .error {
        background: rgba(248, 215, 218, 0.9);
        color: #721c24;
        border: 2px solid rgba(245, 198, 203, 1);
    }

    /* Links Animation */
    p {
        margin-top: 25px;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);
        animation: fadeInUp 0.6s ease-out 1s backwards;
    }

    a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        padding: 8px 18px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        transition: all 0.3s ease;
        display: inline-block;
    }

    a:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(1.1) rotate(-2deg);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .container {
            width: 95%;
            padding: 30px 20px;
        }
        .header-table td {
            font-size: 2rem;
        }
    }
</style>
</head>
<body id="mainBody">

    <!-- Weather Effects Container -->
    <div class="weather-effects" id="weatherEffects"></div>

    <div class="container">
        <!-- Animated Table Header -->
        <table class="header-table">
            <tr>
                <td>🌤️ REGISTER</td>
            </tr>
        </table>
        
        <?php if (!empty($msg)): ?>
            <div class="message <?php echo strpos($msg, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="input-group">
                <input type="text" name="name" placeholder="👤 Enter Name" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="📧 Enter Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="🔒 Enter Password" required>
            </div>
            <input type="submit" value="✨ CREATE ACCOUNT">
        </form>
        
        <p>Already have account? <a href="login.php">Login Here</a></p>
    </div>

    <script>
        // Weather background system (same as before)
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
                        changeBackground(data.weather[0].main.toLowerCase());
                    } catch (error) {
                        setDefaultBackground();
                    }
                }, () => {
                    setDefaultBackground();
                });
            } else {
                setDefaultBackground();
            }
        }

        function changeBackground(weather) {
            const body = document.getElementById('mainBody');
            const effects = document.getElementById('weatherEffects');
            effects.innerHTML = '';
            body.className = '';
            
            const hour = new Date().getHours();
            const isNight = hour < 6 || hour > 18;
            
            if (weather === 'clear') {
                body.classList.add(isNight ? 'night' : 'sunny');
                if (isNight) addStars();
            } else if (weather === 'clouds') {
                body.classList.add('cloudy');
                addClouds();
            } else if (weather === 'rain' || weather === 'drizzle') {
                body.classList.add('rain');
                addRain();
            } else if (weather === 'thunderstorm') {
                body.classList.add('thunderstorm');
                addRain();
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
            document.getElementById('mainBody').classList.add(isNight ? 'night' : 'clear');
            if (isNight) addStars();
        }

        function addStars() {
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + 'vw';
                star.style.top = Math.random() * 100 + 'vh';
                star.style.animationDuration = 2 + Math.random() * 3 + 's';
                document.getElementById('weatherEffects').appendChild(star);
            }
        }

        function addRain() {
            for (let i = 0; i < 40; i++) {
                const rain = document.createElement('div');
                rain.className = 'rain-drop';
                rain.style.left = Math.random() * 100 + 'vw';
                rain.style.animationDuration = 0.5 + Math.random() * 0.5 + 's';
                rain.style.animationDelay = Math.random() * 2 + 's';
                document.getElementById('weatherEffects').appendChild(rain);
            }
        }

        function addSnow() {
            for (let i = 0; i < 40; i++) {
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
                document.getElementById('weatherEffects').appendChild(cloud);
            }
        }

        getWeatherBackground();
    </script>
</body>
</html>
