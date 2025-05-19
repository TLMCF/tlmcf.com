<?php
session_start();

// Identifiants fixes
$admin_user = "admin";
$admin_pass = "admin"; // À changer évidemment

if ($_POST["username"] === $admin_user && $_POST["password"] === $admin_pass) {
    $_SESSION["admin"] = true;
    header("Location: admin-dashboard.php");
} else {
    echo "<script>alert('Identifiants incorrects');window.location.href='admin-login.html';</script>";
}
?>
