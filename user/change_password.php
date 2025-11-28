<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$old = $_POST['old_password'];
$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

$id = $_SESSION['user']['id'];

// Ambil password dari DB
$q = mysqli_query($conn, "SELECT password FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) die("User tidak ditemukan.");

// Cek password lama
if (!password_verify($old, $data['password'])) {
    die("Password lama salah.");
}

// Cek konfirmasi
if ($new !== $confirm) {
    die("Password baru tidak cocok!");
}

$hashed = password_hash($new, PASSWORD_DEFAULT);

// Update
mysqli_query($conn, "
    UPDATE users 
    SET password='$hashed'
    WHERE id='$id'
");

header("Location: account.php?success=password_changed");
exit;
