<?php
require_once '../db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die('Invalid CSRF token.');
}

$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
$checkin_raw = $_POST['checkin_date'] ?? '';
$checkout_raw = $_POST['checkout_date'] ?? '';

if ($customer_name === '' || $customer_email === '' || $room_id <= 0 || $checkin_raw === '' || $checkout_raw === '') {
    die('Data tidak lengkap.');
}

if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    die('Email tidak valid.');
}

$ci = DateTime::createFromFormat('Y-m-d', $checkin_raw);
$co = DateTime::createFromFormat('Y-m-d', $checkout_raw);
$ci_errors = DateTime::getLastErrors();
$co_errors = DateTime::getLastErrors();
if (!$ci || !$co || $ci_errors['warning_count'] > 0 || $ci_errors['error_count'] > 0 || $co_errors['warning_count'] > 0 || $co_errors['error_count'] > 0) {
    die('Tanggal tidak valid.');
}

$interval = $ci->diff($co);
$nights = (int)$interval->format('%a');
if ($nights <= 0) die('Tanggal checkout harus setelah checkin.');

if ($quantity > 20) die('Jumlah kamar tidak wajar.');

$stmt = $mysqli->prepare("SELECT id, price FROM rooms WHERE id = ?");
if (!$stmt) die('Query error.');
$stmt->bind_param('i', $room_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die('Kamar tidak ditemukan.');
$row = $res->fetch_assoc();
$base_price = (float)$row['price'];

$hasStock = false;
$chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
if ($chk && $chk->num_rows > 0) $hasStock = true;

if ($hasStock) {
    $sstmt = $mysqli->prepare("SELECT stock FROM rooms WHERE id = ? FOR UPDATE");
    $sstmt->bind_param('i', $room_id);
    $sstmt->execute();
    $srow = $sstmt->get_result()->fetch_assoc();
    $avail = isset($srow['stock']) ? (int)$srow['stock'] : 0;
    if ($quantity > $avail) {
        die('Jumlah kamar melebihi stok tersedia.');
    }
}

$total = 0.0;
$cursor = clone $ci;
for ($i = 0; $i < $nights; $i++) {
    $dayNum = (int)$cursor->format('N');
    $price = $base_price;
    if ($dayNum >= 1 && $dayNum <= 5) {
        $price = $price * 0.95;
    }
    $total += $price * $quantity;
    $cursor->modify('+1 day');
}

$subtotal = $total;

$mysqli->begin_transaction();

try {
    do {
        $booking_code = generateBookingCode();
        $chkcode = $mysqli->prepare("SELECT id FROM bookings WHERE booking_code = ?");
        $chkcode->bind_param('s', $booking_code);
        $chkcode->execute();
        $exists = $chkcode->get_result()->num_rows;
        $chkcode->close();
    } while ($exists > 0);

    $ins = $mysqli->prepare("INSERT INTO bookings (booking_code, customer_name, customer_email, checkin_date, checkout_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'unpaid')");
    if (!$ins) throw new Exception('Insert booking prepare failed: ' . $mysqli->error);
    $ins->bind_param('sssssd', $booking_code, $customer_name, $customer_email, $checkin_raw, $checkout_raw, $total);
    if (!$ins->execute()) throw new Exception('Insert booking failed: ' . $ins->error);
    $booking_id = $ins->insert_id;
    $ins->close();

    $insd = $mysqli->prepare("INSERT INTO booking_rooms (booking_id, room_id, price, nights, subtotal, quantity) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$insd) throw new Exception('Insert booking_rooms prepare failed: ' . $mysqli->error);
    $insd->bind_param('iididi', $booking_id, $room_id, $base_price, $nights, $subtotal, $quantity);
    if (!$insd->execute()) throw new Exception('Insert booking_rooms failed: ' . $insd->error);
    $insd->close();

    if ($hasStock) {
        $upd = $mysqli->prepare("UPDATE rooms SET stock = stock - ? WHERE id = ? AND stock >= ?");
        if (!$upd) throw new Exception('Update stock prepare failed: ' . $mysqli->error);
        $upd->bind_param('iii', $quantity, $room_id, $quantity);
        if (!$upd->execute()) throw new Exception('Update stock failed: ' . $upd->error);
        $upd->close();

        $statusCheck = $mysqli->prepare("SELECT stock FROM rooms WHERE id = ?");
        $statusCheck->bind_param('i', $room_id);
        $statusCheck->execute();
        $s = $statusCheck->get_result()->fetch_assoc();
        $statusCheck->close();

        if (isset($s['stock']) && (int)$s['stock'] <= 0) {
            $upr = $mysqli->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
            $upr->bind_param('i', $room_id);
            $upr->execute();
            $upr->close();
        }
    }

    $mysqli->commit();
    header("Location: payment.php?booking_id=" . $booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal menyimpan booking: " . $e->getMessage());
}
