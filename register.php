<?php
include "includes/header.php";

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    if(mysqli_stmt_execute($stmt)){
        $message = "Registration successful! <a href='login.php'>Login</a>";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<h1>Register</h1>
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
<p><?= $message ?></p>
<?php include "includes/footer.php"; ?>
