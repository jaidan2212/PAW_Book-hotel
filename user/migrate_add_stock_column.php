<?php
require_once '../db.php';

echo "Migration: ensure rooms has 'stock' column\n";
try {
    $q = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if ($q && $q->num_rows > 0) {
        echo "Column 'stock' already exists.\n";
        exit;
    }

    $sql = "ALTER TABLE rooms ADD COLUMN stock INT NOT NULL DEFAULT 1";
    if ($mysqli->query($sql) === TRUE) {
        echo "Success: column 'stock' added.\n";
    } else {
        echo "Error adding column 'stock': " . $mysqli->error . "\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

?>