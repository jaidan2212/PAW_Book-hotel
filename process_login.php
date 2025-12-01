<?php
session_start();
require_once 'db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Masukkan username dan password.';
    header('Location: login.php');
    exit;
}

$stmt = $mysqli->prepare("
    SELECT id, name, email, password, role, photo, photo_public_id
    FROM users
    WHERE name = ?
    LIMIT 1
");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'photo' => $user['photo'],
        'photo_public_id' => $user['photo_public_id']
    ];

    // REDIRECT SETELAH LOGIN (BOOKING)
    if (isset($_SESSION['after_login_redirect'])) {
        $redir = $_SESSION['after_login_redirect'];
        unset($_SESSION['after_login_redirect']);
        header("Location: $redir");
        exit;
    }

    // ADMIN
    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    // USER BIASA
    header("Location: index.php");
    exit;
}

$_SESSION['login_error'] = 'Username atau password salah.';
header('Location: login.php');
exit;
