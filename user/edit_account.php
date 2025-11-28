<?php
require_once '../db.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    $update = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $update->bind_param("ssi", $name, $email, $userId);

    if ($update->execute()) {
        $_SESSION['user']['name'] = $name;
        $_SESSION['success'] = "Profile successfully updated.";
        header("Location: account.php");
        exit;
    } else {
        $error = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Solaz Resort | Edit Profile</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width:600px;">
    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-4">Edit Profile</h3>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>

            <button class="btn btn-primary w-100">Save Changes</button>

            <a href="account.php" class="btn btn-secondary w-100 mt-3">Cancel</a>
        </form>

    </div>
</div>

</body>
</html>
