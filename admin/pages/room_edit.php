<?php
require_once __DIR__ . "/../../db.php";

if (!isset($_GET['id'])) die("ID tidak ditemukan");
$id = (int)$_GET['id'];

$data = $mysqli->query("SELECT * FROM rooms WHERE id=$id")->fetch_assoc();

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

    $imageName = $data['image'];
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = __DIR__ . "/../../assets/images/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    $stmt = $mysqli->prepare("
        UPDATE rooms
        SET room_number=?, type=?, price=?, max_person=?, capacity_adult=?, capacity_child=?, description=?, image=?, status=?, stock=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "ssdiissssii",
        $room_number,
        $type,
        $price,
        $max_person,
        $capacity_adult,
        $capacity_child,
        $description,
        $imageName,
        $status,
        $stock,
        $id
    );

    if ($stmt->execute()) {
        $success = "Perubahan berhasil disimpan!";
        $data = $mysqli->query("SELECT * FROM rooms WHERE id=$id")->fetch_assoc();
    } else {
        $error = "Gagal menyimpan perubahan: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
    <link rel="stylesheet" href="../../assets/css/styleAdmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="parent">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>HOTEL SITE</h2>
            <ul class="menu">
                <li><a href="dashboard.php?page=home.php" class="textstyle">Home</a></li>

                <li class="dropdown-li">
                    Room Management
                    <ul class="submenu">
                        <li><a href="dashboard.php?page=rooms" class="textstyle">Rooms</a></li>
                        <li><a href="dashboard.php?page=rooms_add" class="textstyle">Add Rooms</a></li>
                    </ul>
                </li>

                <li class="dropdown-li">
                    Booking Management
                    <ul class="submenu">
                        <li><a href="dashboard.php?page=payment_confirmation" class="textstyle">Confirmation Payment</a></li>
                        <li><a href="dashboard.php?page=booking_confirmation" class="textstyle">Confirmation Booking</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="top-title">Dashboard</div>
            <div class="top-actions">
                <a href="../index.php" class="textstyle">Buka Situs</a>
                <span class="textstyle">Admin</span>
                <a href="../logout.php" class="textstyle">Logout</a>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <h2>Edit Kamar</h2>

            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-6">
            <label>Nomor Kamar</label>
            <input type="text" name="room_number" class="form-control" value="<?= $data['room_number'] ?>">
        </div>

        <div class="col-md-6">
            <label>Tipe</label>
            <select name="type" class="form-control">
                <option <?= $data['type']=='Single'?'selected':'' ?>>Single</option>
                <option <?= $data['type']=='Double'?'selected':'' ?>>Double</option>
                <option <?= $data['type']=='Suite'?'selected':'' ?>>Suite</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>Harga</label>
            <input type="number" name="price" class="form-control" value="<?= $data['price'] ?>">
        </div>

        <div class="col-md-4">
            <label>Maks. Orang</label>
            <input type="number" name="max_person" class="form-control" value="<?= $data['max_person'] ?>">
        </div>

        <div class="col-md-4">
            <label>Stok</label>
            <input type="number" name="stock" class="form-control" value="<?= $data['stock'] ?>">
        </div>

        <div class="col-md-6">
            <label>Kapasitas Dewasa</label>
            <input type="number" name="capacity_adult" class="form-control" value="<?= $data['capacity_adult'] ?>">
        </div>

        <div class="col-md-6">
            <label>Kapasitas Anak</label>
            <input type="number" name="capacity_child" class="form-control" value="<?= $data['capacity_child'] ?>">
        </div>

        <div class="col-md-12">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="4"><?= $data['description'] ?></textarea>
        </div>

        <div class="col-md-12">
            <label>Gambar Kamar</label>
            <input type="file" name="image" class="form-control">
            <small>Gambar saat ini: <?= $data['image'] ?></small>
        </div>

        <div class="col-md-6">
            <label>Status</label>
            <select name="status" class="form-control">
                <option <?= $data['status']=='available'?'selected':'' ?>>available</option>
                <option <?= $data['status']=='booked'?'selected':'' ?>>booked</option>
                <option <?= $data['status']=='maintenance'?'selected':'' ?>>maintenance</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary mt-3">Simpan Perubahan</button>
            <button class="btn btn-secondary mt-3" type="button" onclick="window.location.href='../dashboard.php?page=rooms_edit'">Kembali</button>
        </div>

    </form>

        </div>

    </div>

    <script>
        document.querySelectorAll(".dropdown-li").forEach(menu => {
            menu.addEventListener("click", () => {
                menu.querySelector(".submenu").classList.toggle("show");
            });
        });
    </script>

</body>

</html>