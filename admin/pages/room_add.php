<?php
require_once "../db.php";

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_number = $_POST['room_number'];
    $type        = $_POST['type'];
    $price       = $_POST['price'];
    $max_person  = $_POST['max_person'];
    $capacity_adult = $_POST['capacity_adult'];
    $capacity_child = $_POST['capacity_child'];
    $description = $_POST['description'];
    $status      = $_POST['status'];
    $stock       = $_POST['stock'];

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = __DIR__ . "/../../assets/images/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    $stmt = $mysqli->prepare("
        INSERT INTO rooms 
        (room_number, type, price, max_person, capacity_adult, capacity_child, description, image, status, stock)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdiissssi",
        $room_number,
        $type,
        $price,
        $max_person,
        $capacity_adult,
        $capacity_child,
        $description,
        $imageName,
        $status,
        $stock
    );

    if ($stmt->execute()) {
        $success = "Kamar berhasil ditambahkan!";
    } else {
        $error = "Gagal menambah kamar: " . $stmt->error;
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
    <h3>Tambah Kamar</h3>

    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-6">
            <label>Nomor Kamar</label>
            <input type="text" name="room_number" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label>Tipe</label>
            <select name="type" class="form-control" required>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Suite">Suite</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label>Maks. Orang</label>
            <input type="number" name="max_person" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label>Kapasitas Dewasa</label>
            <input type="number" name="capacity_adult" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label>Kapasitas Anak</label>
            <input type="number" name="capacity_child" class="form-control" required>
        </div>

        <div class="col-md-12">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="col-md-12">
            <label>Gambar Kamar</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="col-md-6">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="booked">Booked</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary mt-3">Tambah Kamar</button>
        </div>

    </form>
</div>

</body>
</html>
