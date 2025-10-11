<?php
function fetchWeather($city, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
