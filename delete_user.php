<?php
include "db.php";

$error = "";    

if (isset($_GET['id'])) {

    $id = trim($_GET['id']);

    if (empty($id)) {
        $error = "⚠️ Please fill all fields!";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM user_master WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "s", $id);

        $error = "Data is founded and query is createed with id: " . $id . "<br>";

        if (mysqli_stmt_execute($stmt)) {
            echo "✅ User deleted successfully!<br>";
        } else {
            echo "❌ Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: admin_dashboard.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>DELETE User</title>
    </head>
</html>