<?php
// weather.php
// Usage: weather.php?q=cityname
header('Content-Type: application/json; charset=utf-8');

// Put your OpenWeather API key here:
$API_KEY = '9395a30196f7ea25c4527ea666ca6886';

if (empty($API_KEY)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not set in weather.php']);
    exit;
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing query parameter q']);
    exit;
}

function curl_get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$res, $err, $code];
}

// 1) Geocoding: get lat/lon
$geo_url = "http://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($q) . "&limit=1&appid={$API_KEY}";
list($geo_res, $geo_err, $geo_code) = curl_get($geo_url);
if ($geo_err || $geo_code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach geocoding API', 'detail' => $geo_err]);
    exit;
}
$geo = json_decode($geo_res, true);
if (empty($geo) || !isset($geo[0]['lat'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Location not found']);
    exit;
}

$lat = $geo[0]['lat'];
$lon = $geo[0]['lon'];
$place_name = ($geo[0]['name'] ?? $q) . (isset($geo[0]['state']) ? (', ' . $geo[0]['state']) : '') . (isset($geo[0]['country']) ? (', ' . $geo[0]['country']) : '');

// 2) One Call for current + daily (10 days)
$onecall_url = "https://api.openweathermap.org/data/2.5/onecall?lat={$lat}&lon={$lon}&units=metric&exclude=minutely,alerts&appid={$API_KEY}";
list($one_res, $one_err, $one_code) = curl_get($onecall_url);
if ($one_err || $one_code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach onecall API', 'detail' => $one_err]);
    exit;
}
$one = json_decode($one_res, true);

// attach place info and return
$response = [
    'place' => $place_name,
    'lat' => $lat,
    'lon' => $lon,
    'data' => $one
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
