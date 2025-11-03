<?php
session_start();
include "db.php";

$msg = "";

// Check if user is already logged in
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
        $check = mysqli_prepare($conn, "SELECT id FROM user_master WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $msg = "⚠️ This email is already registered!";
        } else {
            mysqli_stmt_close($check);
            $stmt = mysqli_prepare($conn, "INSERT INTO user_master (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);

            if (mysqli_stmt_execute($stmt)) {
                $msg = "✅ Registration successful! You can now login.";
            } else {
                $msg = "❌ Registration failed. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <style>
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
        .message {
            margin: 15px 0;
            padding: 12px;
            border-radius: 15px;
            font-weight: bold;
            animation: bounceIn 0.5s ease-out;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body id="mainBody">

    <!-- Weather Effects Container -->
    <div class="weather-effects" id="weatherEffects"></div>

    <div class="container">
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

    <script src="./src/background.js"></script>
</body>
</html>
