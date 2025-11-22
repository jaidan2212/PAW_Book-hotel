<?php
require_once '../db.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$base = isset($_GET['base']) ? (int)$_GET['base'] : 101;
$n = isset($_GET['n']) ? (int)$_GET['n'] : 5;
if ($type === '' || $n < 1) {
    echo "Usage: generate_rooms_demo.php?type=Single&base=101&n=5";
    exit;
}

$created = 0;
$skipped = 0;
$price = 200000;
$tstmt = $mysqli->prepare("SELECT price FROM rooms WHERE type = ? LIMIT 1");
if ($tstmt) {
    $tstmt->bind_param('s', $type);
    $tstmt->execute();
    $tres = $tstmt->get_result()->fetch_assoc();
    if ($tres && isset($tres['price'])) $price = (float)$tres['price'];
}

for ($i = 0; $i < $n; $i++) {
    $room_number = $base + $i;
    $chk = $mysqli->prepare("SELECT id FROM rooms WHERE room_number = ? LIMIT 1");
    if (!$chk) continue;
    $chk->bind_param('s', (string)$room_number);
    $chk->execute();
    $cres = $chk->get_result();
    if ($cres && $cres->num_rows > 0) {
        $skipped++;
        continue;
    }

    $ins = $mysqli->prepare("INSERT INTO rooms (room_number, type, price, status) VALUES (?, ?, ?, 'available')");
    if (!$ins) continue;
    $ins->bind_param('ssd', (string)$room_number, $type, $price);
    if ($ins->execute()) {
        $created++;
    } else {
        $skipped++;
    }
}

echo "Generate rooms demo for type=" . htmlspecialchars($type) . " completed. Created: $created, Skipped: $skipped";

?>