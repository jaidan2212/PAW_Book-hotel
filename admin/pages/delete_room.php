<?php
include '../../db.php';

$id = (int)$_GET['id'];

$check = mysqli_query($mysqli, "SELECT * FROM booking_rooms WHERE room_id=$id");
if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('Kamar tidak dapat dihapus karena masih digunakan pada booking!'); 
    window.location='../dashboard.php?page=rooms_edit';</script>";
    exit;
}

$q = mysqli_query($mysqli, "SELECT image FROM rooms WHERE id=$id");
$r = mysqli_fetch_assoc($q);

if (!empty($r['image'])) {
    $path = "../../assets/images/" . $r['image'];
    if (file_exists($path)) unlink($path);
}

mysqli_query($mysqli, "DELETE FROM rooms WHERE id=$id");

echo "<script>alert('Kamar berhasil dihapus!'); window.location='../dashboard.php?page=rooms_edit';</script>";
exit;
?>