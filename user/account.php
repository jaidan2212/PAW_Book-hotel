<?php
session_start();
require "../db.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

$photo = $user['photo'] ?: "../assets/images/default_user.jpg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5" style="max-width: 700px;">

    <h2 class="mb-4">Pengaturan Akun</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body text-center">

            <img src="<?= $photo ?>" 
                 class="rounded-circle mb-3"
                 style="width:140px; height:140px; object-fit:cover;">

            <h4><?= htmlspecialchars($user['name']) ?></h4>

            <form action="upload_photo.php" method="POST" enctype="multipart/form-data" class="mt-3">
                <input type="file" name="photo" class="form-control mb-2" required>
                <button class="btn btn-primary w-100">Upload Foto</button>
            </form>

        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header"><strong>Edit Profile</strong></div>
        <div class="card-body">

            <form action="update_account.php" method="POST">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control mb-3"
                       value="<?= htmlspecialchars($user['name']) ?>" required>

                <button class="btn btn-success w-100">Simpan Perubahan</button>
            </form>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><strong>Ubah Password</strong></div>
        <div class="card-body">

            <form action="change_password.php" method="POST">

                <label>Password Lama</label>
                <input type="password" name="old_password" class="form-control mb-2" required>

                <label>Password Baru</label>
                <input type="password" name="new_password" class="form-control mb-2" required>

                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" class="form-control mb-3" required>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning w-50">Ubah Password</button>
                    <a href="../index.php" class="btn btn-secondary w-50">Kembali</a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
