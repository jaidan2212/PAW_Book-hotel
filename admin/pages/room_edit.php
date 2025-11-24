<?php
require_once "../db.php";

$type = isset($_GET['type']) ? intval($_GET['type']) : 0;

$stmt = $mysqli->prepare("SELECT * FROM rooms WHERE type = ?");
$stmt->bind_param("i", $type);
$stmt->execute();

$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    echo "<p style='color:red;'>Room Type ($id) not found!</p>";
    exit;
}



// --- Update Data ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $room_number  = $_POST['room_number'];
    $type         = $_POST['type'];
    $price        = $_POST['price'];
    $max_person   = $_POST['max_person'];
    $description  = $_POST['description'];
    $status       = $_POST['status'];
    $stock        = $_POST['stock'];

    // Jika ganti foto
    $image = $room['image'];

    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image");
    } 

    $update = $mysqli->prepare("
        UPDATE rooms SET 
            room_number = ?, 
            type = ?, 
            price = ?, 
            max_person = ?, 
            description = ?, 
            image = ?, 
            status = ?, 
            stock = ?
        WHERE id = ?
    ");

    $update->execute([
        $room_number, $type, $price, $max_person, $description, 
        $image, $status, $stock, $id
    ]);

    header("Location: rooms.php?updated=true");
    exit;
}
?>

<h2>Edit Room</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Room Number:</label><br>
    <input type="text" name="room_number" value="<?= htmlspecialchars($room['room_number']) ?>" required><br><br>

    <label>Type:</label><br>
    <input type="text" name="type" value="<?= htmlspecialchars($room['type']) ?>" required><br><br>

    <label>Price:</label><br>
    <input type="number" name="price" value="<?= $room['price'] ?>" required><br><br>

    <label>Max Person:</label><br>
    <input type="number" name="max_person" value="<?= $room['max_person'] ?>" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?= $room['stock'] ?>" required><br><br>

    <label>Status:</label><br>
    <select name="status">
        <option value="available" <?= $room['status'] == 'available' ? 'selected' : '' ?>>Available</option>
        <option value="booked" <?= $room['status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
        <option value="maintenance" <?= $room['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
    </select><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?= htmlspecialchars($room['description']) ?></textarea><br><br>

    <label>Image:</label><br>
    <img src="../uploads/<?= $room['image'] ?>" width="100"><br>
    <input type="file" name="image"><br><br>

    <button type="submit">Update Room</button>
</form>
