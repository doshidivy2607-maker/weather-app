<?php
session_start();
include_once "db.php"; // database connection

// Check if user is logged in
$userLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather App</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <?php if($userLoggedIn): ?>
            <a href="weather.php">Weather</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main>
