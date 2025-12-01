<?php
require_once "../db.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID kamar tidak ditemukan'); window.location='index.php?page=rooms';</script>";
    exit;
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT image FROM rooms WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    echo "<script>alert('Data kamar tidak ditemukan'); window.location='index.php?page=rooms';</script>";
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM rooms WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Kamar berhasil dihapus'); window.location='index.php?page=rooms';</script>";
} else {
    echo "<script>alert('Gagal menghapus kamar'); window.location='index.php?page=rooms';</script>";
}
?>
