<?php
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

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, is_admin FROM user_master WHERE email = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $id, $name, $email, $is_admin);
        mysqli_stmt_fetch($stmt);
        $_SESSION['id'] = $id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['is_admin'] = $is_admin;
        mysqli_stmt_close($stmt);
        return true;
    } else {
        mysqli_stmt_close($stmt);
        return false;
    }
}

function of_create_user($conn, $name, $email, $password)
{
    $stmt = mysqli_prepare($conn, "INSERT INTO user_master (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function of_delete_user($conn, $user_id)
{

    if (empty($id)) {
        return true;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM user_master WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

?>