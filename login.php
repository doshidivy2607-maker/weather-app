<?php
session_start();
include "db.php";

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $login_type = $_POST['login_type'] ?? 'user';

    if (empty($email) || empty($password)) {
        $error = "⚠️ Please fill all fields!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM weather_app WHERE email = ? AND password = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['is_admin'] = $row['is_admin'];

            if ($login_type == 'admin') {
                if ($row['is_admin'] == 1) {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $error = "❌ You are not authorized to access admin panel!";
                }
            } else {
                header("Location: weather.php");
                exit();
            }
        } else {
            $error = "❌ Invalid email or password!";
        }
        mysqli_stmt_close($stmt);
    }
}

// Check for registration success
$reg_success = false;
if (isset($_SESSION['registration_success'])) {
    $reg_success = true;
    unset($_SESSION['registration_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Weather App</title>
<style>
    body {
        margin: 0;
        font-family: Arial;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(to bottom, #f0f8ff, #87ceeb);
    }
    .admin-btn-container {
        position: fixed;
        top: 20px;
        right: 25px;
    }
    .admin-btn {
        background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(238, 90, 111, 0.4);
        transition: 0.3s;
    }
    .admin-btn:hover {
        transform: scale(1.05);
    }
    .container {
        background: rgba(255, 255, 255, 0.9);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        width: 350px;
        text-align: center;
    }
    input[type="email"], input[type="password"] {
        width: 90%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
        outline: none;
    }
    button {
        background: linear-gradient(45deg, #00bfff, #1e90ff);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
    button:hover {
        transform: scale(1.05);
    }
    .message {
        margin: 15px 0;
        padding: 10px;
        border-radius: 8px;
        font-weight: bold;
    }
    .success {
        background: #d4edda;
        color: #155724;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
    }
</style>
</head>
<body>
    <?php if (!isset($_GET['admin'])): ?>
    <div class="admin-btn-container">
        <a href="?admin=1" class="admin-btn">🔐 Admin Login</a>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>🔑 <?php echo isset($_GET['admin']) ? 'Admin' : 'User'; ?> Login</h2>
        
        <?php if ($reg_success): ?>
            <div class="message success">✅ Registration successful! Please login.</div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Enter Email" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <input type="hidden" name="login_type" value="<?php echo isset($_GET['admin']) ? 'admin' : 'user'; ?>">
            <button type="submit" name="login">
                <?php echo isset($_GET['admin']) ? '🔐 Admin Login' : '👤 Login'; ?>
            </button>
        </form>
        
        <?php if (!isset($_GET['admin'])): ?>
            <p>Don't have account? <a href="register.php">Register</a></p>
        <?php else: ?>
            <p><a href="login.php">← Back to User Login</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
