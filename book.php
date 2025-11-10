<?php
require_once 'functions.php';

$rooms = getRooms();
$selected = null;
if (isset($_GET['room_id'])) {
    $selected = getRoomById((int)$_GET['room_id']);
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Booking</title></head>
<body>
  <h2>Buat Booking</h2>
  <form method="post" action="process_booking.php">
    <label>Nama Pelanggan:<br><input type="text" name="customer_name" required></label><br><br>
    <label>Email:<br><input type="email" name="customer_email"></label><br><br>

    <label>Pilih Kamar:<br>
      <select name="room_id" required>
        <option value="">-- pilih --</option>
        <?php foreach($rooms as $r): ?>
        <option value="<?= $r['id'] ?>" <?= ($selected && $selected['id']==$r['id'])?'selected':'' ?>>
          <?= htmlspecialchars($r['room_number'].' - '.$r['type'].' ('.number_format($r['price'],0,',','.').')') ?>
        </option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Check-in:<br><input type="date" name="checkin_date" required></label><br><br>
    <label>Check-out:<br><input type="date" name="checkout_date" required></label><br><br>

    <button type="submit">Pesan Sekarang</button>
  </form>
  <p><a href="index.php">Kembali</a></p>
</body>
</html>
