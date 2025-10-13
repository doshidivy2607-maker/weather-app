<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username = "root";
$password = "";
$database = "weather";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function is_check_user($conn, $email, $password)
{
    // ✅ FIXED: Fetch password hash separately, then verify
    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password, is_admin FROM user_master WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $id, $name, $email, $db_password, $is_admin);
        mysqli_stmt_fetch($stmt);
        
        // ✅ FIXED: Use password_verify instead of direct comparison
        if (password_verify($password, $db_password)) {
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['is_admin'] = $is_admin;
            mysqli_stmt_close($stmt);
            return true;
        }
    }
    
    mysqli_stmt_close($stmt);
    return false;
}

function of_create_user($conn, $name, $email, $password)
{
    // ✅ FIXED: Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = mysqli_prepare($conn, "INSERT INTO user_master (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function of_delete_user($conn, $user_id)
{
    // ✅ FIXED: Changed $id to $user_id
    if (empty($user_id)) {
        return false;  // ✅ FIXED: Return false instead of true for failure
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM user_master WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}
?>
