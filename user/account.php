<?php
require_once '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}


$userId = $_SESSION['user']['id'];

$stmt = $mysqli->prepare("SELECT name, email, photo FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$photoPath = (!empty($user['photo']))
    ? "../uploads/" . $user['photo']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&size=120";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solaz Resort | My Account</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:700px;">

    <div class="card shadow-sm">
        <div class="card-header text-center py-4">
            <h3 class="fw-bold mb-0">My Account</h3>
            <small class="text-muted">Manage your profile & settings</small>

            <a href="edit_account.php" class="btn btn-outline-primary w-100 mb-2">Edit Profile</a>
            <a href="change_password.php" class="btn btn-outline-warning w-100 mb-2">Change Password</a>
            <a href="upload_photo.php" class="btn btn-outline-success w-100">Upload Profile Photo</a>
        </div>

        <div class="card-body p-4">

            <div class="mb-4 text-center">
                <img src="<?= $photoPath ?>"
                     width="120" height="120"
                     style="object-fit: cover; border-radius:50%;"
                     class="shadow-sm"
                     alt="Profile Picture">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? 'Not Set'); ?>" disabled>
            </div>

            <hr>

            <div class="d-flex justify-content-between mt-4">
                <a href="edit_account.php" class="btn btn-primary">Edit Profile</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
