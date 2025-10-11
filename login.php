<?php
include "includes/header.php";

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $hashedPassword);
    mysqli_stmt_fetch($stmt);

    if ($id && password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        header("Location: weather.php");
        exit();
    } else {
        $message = "Invalid credentials.";
    }
}
?>

<h1>Login</h1>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<p><?= $message ?></p>
<?php include "includes/footer.php"; ?>
