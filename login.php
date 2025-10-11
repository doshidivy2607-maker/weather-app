<?php
include "./components/header.php";

if (is_session()) {
  header("Location: weather.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"] ?? "");
  $password = trim($_POST["password"] ?? "");

  if (empty($email) || empty($password)) {
    $msg = "⚠️ Please fill all fields.";
  } else {
    if (is_check_user($conn, $email, $password)) {
      header("Location: weather.php");
      exit();
    } else {
      $msg = "❌ Invalid userID or password!";
    }
  }
}
?>

<link rel="stylesheet" href="./css/register.css">

<?php
include "./components/body.php"; 
?>

<div class="container">
  <table class="header-table">
    <tr>
      <td>🌦️ LOGIN</td>
    </tr>
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

<?php
include "./components/footer.php";
?>
