<?php  ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | Booking Hotel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <main class="d-flex align-items-center justify-content-center" style="min-height:80vh;">
        <div class="card p-4" style="width:360px;">
            <h3 class="mb-3">Login</h3>

            <?php if(isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['login_error']); ?></div>
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
                <button class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
