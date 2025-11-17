<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// contoh sederhana
if ($username === "admin" && $password === "123") {
    $_SESSION['user'] = $username;
    header("Location: index.php");
} else {
    $_SESSION['login_error'] = "Username atau password salah!";
    header("Location: login.php");
}
exit;
