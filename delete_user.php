<?php
include "./components/header.php";

if (!is_admin()) {
    header("Location: login.php");
    exit;
}

of_delete_user($conn, $_GET['id']);
header("Location: admin_dashboard.php");

?>