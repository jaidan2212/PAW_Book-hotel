<?php
require_once '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['photo']['name'])) {

    $folder = __DIR__ . '/../uploads/';

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $fileExt = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $newFilename = "profile_" . $userId . "." . $fileExt;
    $targetFile = $folder . $newFilename;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {

        $stmt = $mysqli->prepare("UPDATE users SET photo = ? WHERE id = ?");
        $stmt->bind_param("si", $newFilename, $userId);
        $stmt->execute();

        $_SESSION['success'] = "Foto profil berhasil diperbarui!";
        header("Location: account.php");
        exit;
    } else {
        $message = "❌ Gagal upload file. Cek permission folder uploads atau ukuran file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Upload Photo | Booking Hotel</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:500px;">
    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-3">Upload Profile Photo</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" class="form-control mb-3" name="photo" accept="image/*" required>
            <button class="btn btn-success w-100">Upload</button>
        </form>

        <a href="account.php" class="btn btn-secondary w-100 mt-3">Cancel</a>
    </div>
</div>

</body>
</html>
