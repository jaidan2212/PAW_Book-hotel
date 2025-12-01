<?php
session_start();
require_once 'db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$captchaInput = $_POST['captcha_input'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Masukkan username dan password.';
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['captcha']) || $captchaInput != $_SESSION['captcha']) {
    $_SESSION['login_error'] = 'Captcha salah.';
    
    $_SESSION['captcha'] = rand(10, 999);

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

    unset($_SESSION['captcha']); 

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'photo' => $user['photo'],
        'photo_public_id' => $user['photo_public_id']
    ];

    if (isset($_SESSION['after_login_redirect'])) {
        $redir = $_SESSION['after_login_redirect'];
        unset($_SESSION['after_login_redirect']);
        header("Location: $redir");
        exit;
    }

    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    header("Location: index.php");
    exit;
}

$_SESSION['login_error'] = 'Username atau password salah.';
$_SESSION['captcha'] = rand(10, 999);
header('Location: login.php');
exit;

