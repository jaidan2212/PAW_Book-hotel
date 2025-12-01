<?php
session_start();

$_SESSION['captcha'] = rand(10, 999);

if (isset($_GET['redirect'])) {
    $_SESSION['after_login_redirect'] = $_GET['redirect'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solaz Resort | Login</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="d-flex align-items-center justify-content-center" style="min-height:80vh;">
    <div class="card p-4" style="width:360px;">
        <h3 class="mb-3 text-center">Login</h3>

        <?php if(isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form action="process_login.php" method="post">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control" type="text" name="username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Masukkan Angka Berikut</label>
                <div class="p-2 mb-2 bg-dark text-white text-center rounded fs-4 fw-bold">
                    <?= $_SESSION['captcha']; ?>
                </div>
                <input class="form-control" type="number" name="captcha_input" placeholder="Masukkan angka di atas" required>
            </div>

            <button class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3 mb-0">
            Belum punya akun?
            <a href="register.php" class="text-decoration-none">Daftar</a>
        </p>

    </div>
</main>

</body>
</html>
