<?php
session_start();
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $_SESSION['register_error'] = "Password tidak sama!";
        header("Location: register.php");
        exit;
    }

    $cek = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $_SESSION['register_error'] = "Email sudah terdaftar!";
        header("Location: register.php");
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'customer')");
    $stmt->bind_param("sss", $name, $email, $hash);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Terjadi kesalahan saat registrasi.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solaz Resort | Register</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="d-flex align-items-center justify-content-center" style="min-height:80vh;">
    <div class="card p-4" style="width:360px;">
        <h3 class="mb-3 text-center">Daftar Akun</h3>

        <?php if(isset($_SESSION['register_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['register_error']); unset($_SESSION['register_error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['register_success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['register_success']); unset($_SESSION['register_success']); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control" type="text" name="name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input class="form-control" type="password" name="confirm_password" required>
            </div>

            <button class="btn btn-primary w-100">Daftar</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Sudah punya akun?
            <a href="login.php" class="text-decoration-none">Login</a>
        </p>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
