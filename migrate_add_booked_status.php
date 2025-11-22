<?php
// Migration: add 'booked' to rooms.status enum if missing
// Usage (CLI): php migrate_add_booked_status.php
// Or access via browser: http://localhost/Tugas_Akhir_PAW/migrate_add_booked_status.php

require_once __DIR__ . '/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Migration: ensure rooms.status enum contains 'booked'\n";

$query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='rooms' AND COLUMN_NAME='status'";
$res = $mysqli->query($query);
if (!$res) {
    echo "ERROR: cannot read column metadata: " . $mysqli->error . "\n";
    exit(1);
}

$row = $res->fetch_assoc();
if (!$row) {
    echo "ERROR: column 'status' not found on table 'rooms'.\n";
    exit(1);
}

$colType = $row['COLUMN_TYPE'];
echo "Current COLUMN_TYPE: $colType\n";

if (strpos($colType, "'booked'") !== false) {
    echo "Nothing to do — 'booked' already present.\n";
    exit(0);
}

$newEnum = "ENUM('available','booked','maintenance') DEFAULT 'available'";

$alter = "ALTER TABLE rooms MODIFY COLUMN status $newEnum";
echo "Running: $alter\n";

if ($mysqli->query($alter) === TRUE) {
    echo "Success: column altered.\n";
    exit(0);
} else {
    echo "ERROR: failed to alter table: " . $mysqli->error . "\n";
    exit(1);
}

?>