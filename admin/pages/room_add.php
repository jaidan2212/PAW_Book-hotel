<?php
require_once "../db.php";

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $room_number = $_POST['room_number'];
    $type        = $_POST['type'];
    $price       = $_POST['price'];
    $status      = $_POST['status'];
    $stock       = $_POST['stock'];

    $stmt = $mysqli->prepare("INSERT INTO rooms (room_number, type, price, status, stock) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssdsi", $room_number, $type, $price, $status, $stock);

    if ($stmt->execute()) {
        $success = "Room berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan room: " . $mysqli->error;
    }

    $stmt->close();
}
?>

<h2>Tambah Room</h2>

<?php if ($success) echo "<div style='color:green;'>$success</div>"; ?>
<?php if ($error) echo "<div style='color:red;'>$error</div>"; ?>

<form method="POST">

    <label>Room Number</label><br>
    <input type="text" name="room_number" required><br><br>

    <label>Room Type</label><br>
    <input type="text" name="type" required><br><br>

    <label>Price (Rp)</label><br>
    <input type="number" name="price" required><br><br>

    <label>Status</label><br>
    <select name="status">
        <option value="available">Available</option>
        <option value="booked">Booked</option>
        <option value="maintenance">Maintenance</option>
    </select><br><br>

    <label>Stock</label><br>
    <input type="number" name="stock" value="1" min="1" required><br><br>

    <button type="submit">Save Room</button>
</form>
