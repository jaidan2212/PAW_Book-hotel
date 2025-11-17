<?php require_once 'functions.php'; $rooms = getRooms(); ?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/css/style.css">
<title>Booking</title>
</head>
<body>


<div class="container">
  <h2>Form Booking</h2>

  <form action="process_booking.php" method="post">
    <div class="mb-3">
      <label>Nama Pelanggan</label>
      <input type="text" name="customer_name" required>
    </div>
<br>

    <div class="mb-3">
      <label>Email (Opsional)</label>
      <input type="email" name="customer_email">
    </div>
<br>
    <div class="mb-3">
      <label>Pilih Kamar</label>
      <select name="room_id" required>
        <option value="">-- pilih --</option>
        <?php foreach($rooms as $r): ?>
        <option value="<?= $r['id'] ?>">
          <?= $r['room_number']." - ".$r['type']." (Rp ".number_format($r['price'],0,',','.').")" ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
<br>
    <div class="mb-3">
      <label>Check-in</label>
      <input type="date" name="checkin_date" required>
    </div>
<br>
    <div class="mb-3">
      <label>Check-out</label>
      <input type="date" name="checkout_date" required>
    </div>
<br>
    <button class="btn-submit">Simpan & Lanjut</button>
  </form>
<br>
  <a href="index.php" class="btn btn-secondary">← Kembali</a>
</div>

</body>
</html>
