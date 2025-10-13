<?php
session_start();
include "db.php";

$msg = "";

// If user already logged in, redirect to weather
if (isset($_SESSION['email'])) {
    header("Location: weather.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email) || empty($password)) {
        $msg = "⚠️ Please fill all fields.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM user_master WHERE email = ?");

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $id, $name, $db_pass);
            mysqli_stmt_fetch($stmt);

            if ($password === $db_pass) {
                  $_SESSION["email"] = $email;
                  $_SESSION["name"] = $name;
                  $_SESSION["user_id"] = $id;
                  $_SESSION["is_admin"] = $is_admin; // ✅ Add this line
                  if ($is_admin == 1) {
                      header("Location: admin_dashboard.php");
                  } else {
                      header("Location: weather.php");
                  }
                  exit();
              }
            else {
                $msg = "❌ Invalid password!";
            }
        } else {
            $msg = "⚠️ Email not registered!";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Weather App</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body id="mainBody">

  <!-- Weather Effects Container -->
  <div class="weather-effects" id="weatherEffects"></div>

  <div class="container">
    <table class="header-table">
      <tr><td>🌦️ LOGIN</td></tr>
    </table>

    <?php if (!empty($msg)): ?>
      <div class="message <?php echo strpos($msg, '✅') !== false ? 'success' : 'error'; ?>">
        <?php echo $msg; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="input-group">
        <input type="email" name="email" placeholder="📧 Enter Email" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="🔒 Enter Password" required>
      </div>
      <input type="submit" value="☁️ LOGIN NOW">
    </form>

    <p>Don't have an account? <a href="register.php">Register Here</a></p>
  </div>

  <script src="./src/background.js"></script>
</body>
</html>
