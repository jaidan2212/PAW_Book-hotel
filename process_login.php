<?php
require_once 'db.php';
session_start();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Masukkan username dan password.';
    header('Location: login.php');
    exit;
}

$stmt = $mysqli->prepare("SELECT id, name, password FROM users WHERE name = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name']
    ];

    header('Location: index.php');
    exit;
}

$_SESSION['login_error'] = 'Username atau password salah.';
header('Location: login.php');
exit;
