<?php
require_once '../db.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stored = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $stored['password'])) {
        $error = "Current password is incorrect!";
    } elseif ($new !== $confirm) {
        $error = "New password does not match!";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $update = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $hash, $userId);
        $update->execute();

        $_SESSION['success'] = "Password updated successfully.";
        header("Location: account.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Solaz Resort | Change Password</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:650px;">
    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-3">Change Password</h3>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <button class="btn btn-primary w-100">Update Password</button>
            <a href="account.php" class="btn btn-secondary w-100 mt-3">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
