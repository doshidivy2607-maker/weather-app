<?php
include "includes/header.php";
$apiKey = "YOUR_API_KEY";
$weatherData = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $city = sanitizeInput($_POST['city']);
    $weatherData = fetchWeather($city, $apiKey);
}
?>

<h1>Check Weather</h1>
<form method="POST">
    <input type="text" name="city" placeholder="Enter city" required>
    <button type="submit">Check</button>
</form>

<?php if($weatherData): ?>
<div class="weather-result">
    <h2><?= $weatherData['name'] ?>, <?= $weatherData['sys']['country'] ?></h2>
    <p>Temperature: <?= $weatherData['main']['temp'] ?>°C</p>
    <p>Condition: <?= $weatherData['weather'][0]['description'] ?></p>
</div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
