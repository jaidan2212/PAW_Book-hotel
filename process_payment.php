<link rel="stylesheet" href="assets/css/style.css">


<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$booking_id = (int)$_POST['booking_id'];
$amount = (float)$_POST['amount'];
$method = $_POST['method'] ?? 'Cash';
$note = $_POST['note'] ?? '';

$mysqli->begin_transaction();
try {
    $ins = $mysqli->prepare("INSERT INTO payments (booking_id, amount, method, note) VALUES (?, ?, ?, ?)");
    $ins->bind_param('idss', $booking_id, $amount, $method, $note);
    $ins->execute();

    $stmt = $mysqli->prepare("SELECT total_amount FROM bookings WHERE id=?");
    $stmt->bind_param('i',$booking_id);
    $stmt->execute();
    $tot = $stmt->get_result()->fetch_assoc();
    $total_amount = (float)$tot['total_amount'];

    if ($amount >= $total_amount) {
        $up = $mysqli->prepare("UPDATE bookings SET status='paid' WHERE id=?");
        $up->bind_param('i',$booking_id);
        $up->execute();
    }

    $mysqli->commit();
    header("Location: payment.php?booking_id=".$booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal proses pembayaran: " . $e->getMessage());
}
