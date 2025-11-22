<?php

require_once __DIR__ . '/../db.php';
header('Content-Type: text/plain; charset=utf-8');

echo "Migration: ensure rooms.reserved_until column exists\n";

$res = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'reserved_until'");
if (!$res) {
    echo "ERROR: cannot read table rooms: " . $mysqli->error . "\n";
    exit(1);
}

if ($res->num_rows > 0) {
    echo "Column 'reserved_until' already exists.\n";
    exit(0);
}

$alter = "ALTER TABLE rooms ADD COLUMN reserved_until DATETIME NULL AFTER status";
if ($mysqli->query($alter) === TRUE) {
    echo "Success: column added.\n";
    exit(0);
} else {
    echo "ERROR: failed to alter table: " . $mysqli->error . "\n";
    exit(1);
}
