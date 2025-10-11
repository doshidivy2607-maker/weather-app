<?php

include "./components/header.php";


// Check if user is admin
if (is_session()) {
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
        if (of_create_user($conn, $name, $email, $password)) {
            $msg = "✅ Account created successfully. You can now log in.";

            header("location: login.php?email=" . $email);
            exit();

        } else {
            $msg = "❌ Error creating account. Please try again.";
        }
    }
}
?>

<link rel="stylesheet" href="./css/register.css">

<?php
include "./components/body.php";
?>


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

<?php
include "./components/footer.php";
?>