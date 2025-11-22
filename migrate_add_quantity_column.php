<?php
require_once 'db.php';

echo "Migration: ensure booking_rooms has 'quantity' column\n";
try {
    $q = $mysqli->query("SHOW COLUMNS FROM booking_rooms LIKE 'quantity'");
    if ($q && $q->num_rows > 0) {
        echo "Column 'quantity' already exists.\n";
        exit;
    }

    $sql = "ALTER TABLE booking_rooms ADD COLUMN quantity INT NOT NULL DEFAULT 1";
    if ($mysqli->query($sql) === TRUE) {
        echo "Success: column 'quantity' added.\n";
    } else {
        echo "Error adding column 'quantity': " . $mysqli->error . "\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

?>