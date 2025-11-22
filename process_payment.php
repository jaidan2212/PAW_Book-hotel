<?php
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
    die('Invalid CSRF token.');
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
        if (!$up) throw new Exception('Prepare update bookings failed: ' . $mysqli->error);
        $up->bind_param('i',$booking_id);
        if (!$up->execute()) throw new Exception('Update bookings failed: ' . $up->error);

        $hasStock = false;
        $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
        if ($chk && $chk->num_rows > 0) $hasStock = true;

        $q = $mysqli->prepare("SELECT room_id, quantity FROM booking_rooms WHERE booking_id = ?");
        if (!$q) throw new Exception('Prepare select booking_rooms failed: ' . $mysqli->error);
        $q->bind_param('i', $booking_id);
        $q->execute();
        $resRooms = $q->get_result();
        while ($rr = $resRooms->fetch_assoc()) {
            $rid = (int)$rr['room_id'];
            $qty = isset($rr['quantity']) ? (int)$rr['quantity'] : 1;

            if ($hasStock) {
                $upd = $mysqli->prepare("UPDATE rooms SET stock = GREATEST(stock - ?, 0) WHERE id = ?");
                if (!$upd) throw new Exception('Prepare update stock failed: ' . $mysqli->error);
                $upd->bind_param('ii', $qty, $rid);
                if (!$upd->execute()) throw new Exception('Update stock failed for room ' . $rid . ': ' . $upd->error);

                $upr2 = $mysqli->prepare("UPDATE rooms SET status='booked' WHERE id = ? AND stock = 0");
                if (!$upr2) throw new Exception('Prepare update room status failed: ' . $mysqli->error);
                $upr2->bind_param('i', $rid);
                if (!$upr2->execute()) throw new Exception('Update room status failed for room ' . $rid . ': ' . $upr2->error);
            } else {
                $upr = $mysqli->prepare("UPDATE rooms SET status='booked' WHERE id=?");
                if (!$upr) throw new Exception('Prepare update rooms failed: ' . $mysqli->error);
                $upr->bind_param('i', $rid);
                if (!$upr->execute()) throw new Exception('Update room status failed for room ' . $rid . ': ' . $upr->error);
            }
        }
    }

    $mysqli->commit();
    header("Location: payment.php?booking_id=".$booking_id);
    exit;

} catch (Exception $e) {
    $mysqli->rollback();
    die("Gagal proses pembayaran: " . $e->getMessage());
}
