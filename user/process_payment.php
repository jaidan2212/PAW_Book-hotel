<?php
require_once '../db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die('Invalid CSRF token.');
}

$booking_id = (int)$_POST['booking_id'];
$amount = (float)$_POST['amount'];
$method = $_POST['method'] ?? 'Cash';
$note = $_POST['note'] ?? '';

$allowed = ['Cash', 'Transfer', 'QRIS'];
if (!in_array($method, $allowed)) {
    die('Metode pembayaran tidak valid.');
}

if ($amount <= 0) {
    die('Jumlah pembayaran tidak valid.');
}

$mysqli->begin_transaction();

try {
    $stmt = $mysqli->prepare("SELECT total_amount FROM bookings WHERE id = ?");
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        throw new Exception("Booking tidak ditemukan.");
    }

    $total_amount = (float)$row['total_amount'];

    $rooms = $mysqli->prepare("SELECT room_id, quantity FROM booking_rooms WHERE booking_id = ?");
    $rooms->bind_param('i', $booking_id);
    $rooms->execute();
    $resRooms = $rooms->get_result();

    if ($resRooms->num_rows === 0) {
        throw new Exception("Tidak ada kamar untuk booking ini.");
    }

    $ins = $mysqli->prepare("INSERT INTO payments (booking_id, amount, method, note) VALUES (?, ?, ?, ?)");
    $ins->bind_param('idss', $booking_id, $amount, $method, $note);
    $ins->execute();

    if ($amount >= $total_amount) {
        $up = $mysqli->prepare("UPDATE bookings SET status='pending' WHERE id = ?");
        $up->bind_param('i', $booking_id);
        $up->execute();

        $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
        $hasStock = ($chk && $chk->num_rows > 0);

        while ($rr = $resRooms->fetch_assoc()) {
            $rid = (int)$rr['room_id'];
            $qty = (int)$rr['quantity'];

            if ($hasStock) {
                $upd = $mysqli->prepare("UPDATE rooms SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
                $upd->bind_param('ii', $qty, $rid);
                $upd->execute();

                $statusCheck = $mysqli->prepare("SELECT stock FROM rooms WHERE id = ?");
                $statusCheck->bind_param('i', $rid);
                $statusCheck->execute();
                $s = $statusCheck->get_result()->fetch_assoc();

                if ($s['stock'] == 0) {
                    $upr2 = $mysqli->prepare("UPDATE rooms SET status='booked' WHERE id = ?");
                    $upr2->bind_param('i', $rid);
                    $upr2->execute();
                }
            } else {
                $upr = $mysqli->prepare("UPDATE rooms SET status='booked' WHERE id = ?");
                $upr->bind_param('i', $rid);
                $upr->execute();
            }
        }
    }

    $mysqli->commit();
    header("Location: payment.php?booking_id=" . $booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal proses pembayaran: " . $e->getMessage());
}