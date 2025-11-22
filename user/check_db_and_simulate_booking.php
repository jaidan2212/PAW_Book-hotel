<?php

require_once __DIR__ . '/../db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DB check and booking simulation\n\n";

function coltype($mysqli, $table, $col) {
    $q = $mysqli->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='".$mysqli->real_escape_string($table)."' AND COLUMN_NAME='".$mysqli->real_escape_string($col)."'");
    if (!$q) return null;
    $r = $q->fetch_assoc();
    return $r['COLUMN_TYPE'] ?? null;
}

echo "rooms.status: " . (coltype($mysqli,'rooms','status') ?? '(not found)') . "\n";
echo "bookings.status: " . (coltype($mysqli,'bookings','status') ?? '(not found)') . "\n\n";

echo "Sample rooms rows:\n";
$res = $mysqli->query("SELECT id, room_number, status FROM rooms LIMIT 5");
while ($r = $res->fetch_assoc()) {
    echo " - id={$r['id']} room_number={$r['room_number']} status={$r['status']}\n";
}

echo "\nSimulating booking insert (transaction, then rollback)...\n";

$ci = date('Y-m-d', strtotime('+1 day'));
$co = date('Y-m-d', strtotime('+2 day'));

$mysqli->begin_transaction();
try {
    $booking_code = 'TST' . strtoupper(substr(bin2hex(random_bytes(3)),0,6));
    $ins = $mysqli->prepare("INSERT INTO bookings (booking_code, customer_name, customer_email, checkin_date, checkout_date, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $cust = 'Test User';
    $email = 'test@example.test';
    $total = 100.00;
    $ins->bind_param('sssssd', $booking_code, $cust, $email, $ci, $co, $total);
    if (!$ins->execute()) {
        throw new Exception('Insert bookings failed: ' . $ins->error);
    }
    $booking_id = $ins->insert_id;

    $r = $mysqli->query("SELECT id, price FROM rooms LIMIT 1")->fetch_assoc();
    if (!$r) throw new Exception('No rooms available to test');
    $room_id = (int)$r['id'];
    $price = (float)$r['price'];
    $nights = 1;
    $subtotal = $price * $nights;

    $insd = $mysqli->prepare("INSERT INTO booking_rooms (booking_id, room_id, price, nights, subtotal) VALUES (?, ?, ?, ?, ?)");
    $insd->bind_param('iiidd', $booking_id, $room_id, $price, $nights, $subtotal);
    if (!$insd->execute()) {
        throw new Exception('Insert booking_rooms failed: ' . $insd->error);
    }

    $up = $mysqli->prepare("UPDATE rooms SET status='booked' WHERE id=?");
    $up->bind_param('i', $room_id);
    if (!$up->execute()) {
        throw new Exception('Update rooms failed: ' . $up->error);
    }

    $mysqli->rollback();
    echo "Simulation completed: inserts and update executed successfully (rolled back).\n";
    exit(0);

} catch (Exception $e) {
    $mysqli->rollback();
    echo "ERROR during simulation: " . $e->getMessage() . "\n";
    exit(1);
}

