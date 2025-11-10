<?php
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book.php');
    exit;
}

$customer_name = trim($_POST['customer_name']);
$customer_email = trim($_POST['customer_email']);
$room_id = (int)$_POST['room_id'];
$checkin = $_POST['checkin_date'];
$checkout = $_POST['checkout_date'];

if (!$customer_name || !$room_id || !$checkin || !$checkout) {
    die('Data tidak lengkap.');
}

$ci = new DateTime($checkin);
$co = new DateTime($checkout);
$interval = $ci->diff($co);
$nights = (int)$interval->format('%a');
if ($nights <= 0) die('Tanggal checkout harus setelah checkin.');

$stmt = $mysqli->prepare("SELECT price FROM rooms WHERE id = ?");
$stmt->bind_param('i',$room_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die('Kamar tidak ditemukan.');
$row = $res->fetch_assoc();
$price = (float)$row['price'];
$subtotal = $price * $nights;
$total = $subtotal;

$mysqli->begin_transaction();
try {
    $booking_code = generateBookingCode();
    $ins = $mysqli->prepare("INSERT INTO bookings (booking_code, customer_name, customer_email, checkin_date, checkout_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $ins->bind_param('sssssd', $booking_code, $customer_name, $customer_email, $checkin, $checkout, $total);
    $ins->execute();
    $booking_id = $ins->insert_id;

    $insd = $mysqli->prepare("INSERT INTO booking_rooms (booking_id, room_id, price, nights, subtotal) VALUES (?, ?, ?, ?, ?)");
    $insd->bind_param('iiidd', $booking_id, $room_id, $price, $nights, $subtotal);
    $insd->execute();

    $up = $mysqli->prepare("UPDATE rooms SET status='available' WHERE id=?"); 
    $up->bind_param('i',$room_id);
    $up->execute();

    $mysqli->commit();

    header("Location: payment.php?booking_id=".$booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal menyimpan booking: " . $e->getMessage());
}
