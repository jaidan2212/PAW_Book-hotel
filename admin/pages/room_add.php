<?php
require_once "../db.php";

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_number = $_POST['room_number'];
    $type        = $_POST['type'];
    $price       = $_POST['price'];
    $max_person  = $_POST['max_person'];
    $description = $_POST['description'];
    $status      = $_POST['status'];
    $stock       = $_POST['stock'];

    // Upload gambar
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        $path = "../uploads/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $path);
    }

    $stmt = $mysqli->prepare("
        INSERT INTO rooms (room_number, type, price, max_person, description, image, status, stock)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdiissi",
        $room_number, $type, $price, $max_person, $description, $imageName, $status, $stock
    );

    if ($stmt->execute()) {
        $success = "Kamar berhasil ditambahkan.";
    } else {
        $error = "Gagal menyimpan kamar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2>Tambah Kamar</h2>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>No Kamar</label>
            <input type="text" name="room_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tipe Kamar</label>
            <select name="type" class="form-control" required>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Suite">Suite</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Maksimal Orang</label>
            <input type="number" name="max_person" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stock Kamar</label>
            <input type="number" name="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label>Gambar</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="rooms.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
