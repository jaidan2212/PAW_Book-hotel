<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Booking Hotel</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <?php if(isset($_SESSION['login_error'])): ?>
        <p class="error"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
    <?php endif; ?>

    <form action="process_login.php" method="post">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

<a href="login.php" class="btn login">Login</a>

    </form>
</div>

</body>
</html>
