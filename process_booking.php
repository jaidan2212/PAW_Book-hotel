<?php
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die('Invalid CSRF token.');
}

$customer_name = trim($_POST['customer_name']);
$customer_email = trim($_POST['customer_email']);
$room_id = (int)$_POST['room_id'];
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
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
$hasStock = false;
try {
    $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if ($chk && $chk->num_rows > 0) $hasStock = true;
} catch (mysqli_sql_exception $e) {
    $hasStock = false;
}
if ($hasStock) {
    $sstmt = $mysqli->prepare("SELECT stock FROM rooms WHERE id = ?");
    $sstmt->bind_param('i', $room_id);
    $sstmt->execute();
    $srow = $sstmt->get_result()->fetch_assoc();
    $avail = isset($srow['stock']) ? (int)$srow['stock'] : 0;
    if ($quantity > $avail) {
        die('Jumlah kamar melebihi stok tersedia.');
    }
}
$subtotal = $price * $nights * $quantity;
$total = $subtotal;

$mysqli->begin_transaction();
try {
    $booking_code = generateBookingCode();
    $ins = $mysqli->prepare("INSERT INTO bookings (booking_code, customer_name, customer_email, checkin_date, checkout_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    if (!$ins) throw new Exception('Prepare insert bookings failed: ' . $mysqli->error);
    $ins->bind_param('sssssd', $booking_code, $customer_name, $customer_email, $checkin, $checkout, $total);
    if (!$ins->execute()) throw new Exception('Insert bookings failed: ' . $ins->error);
    $booking_id = $ins->insert_id;

    $insd = $mysqli->prepare("INSERT INTO booking_rooms (booking_id, room_id, price, nights, subtotal, quantity) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$insd) throw new Exception('Prepare insert booking_rooms failed: ' . $mysqli->error);
    $insd->bind_param('iididi', $booking_id, $room_id, $price, $nights, $subtotal, $quantity);
    if (!$insd->execute()) throw new Exception('Insert booking_rooms failed: ' . $insd->error);

    
    $mysqli->commit();

    header("Location: payment.php?booking_id=".$booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal menyimpan booking: " . $e->getMessage());
}
?>
