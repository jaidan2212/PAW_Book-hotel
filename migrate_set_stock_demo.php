<?php
require_once 'db.php';

echo "Set stock demo\n";
$n = isset($_GET['n']) ? (int)$_GET['n'] : 3;
if ($n < 1) $n = 1;

try {
    $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if (!($chk && $chk->num_rows > 0)) {
        echo "Column 'stock' does not exist. Please run migrate_add_stock_column.php first.\n";
        exit;
    }

    $sql = "UPDATE rooms SET stock = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $mysqli->error . "\n";
        exit;
    }
    $stmt->bind_param('i', $n);
    if ($stmt->execute()) {
        echo "Success: set stock = $n for all rooms. Affected rows: " . $stmt->affected_rows . "\n";
    } else {
        echo "Execute failed: " . $stmt->error . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>