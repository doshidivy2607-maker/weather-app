<?php
session_start();

include "db.php";

function is_session()
{
    return isset($_SESSION['email']);
}

function is_admin()
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
