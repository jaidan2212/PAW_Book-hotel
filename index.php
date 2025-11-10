<?php require_once 'functions.php'; $rooms = getRooms(); ?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/css/style.css">
<title>Daftar Kamar</title>
</head>
<body>

<div class="container">
  <h1>🏨 Daftar Kamar</h1>

  <a href="book.php" class="btn btn-primary">+ Buat Booking</a>
  <a href="report.php" class="btn btn-secondary">📊 Laporan</a>

  <table>
    <tr><th>No</th><th>Kamar</th><th>Tipe</th><th>Harga</th><th></th></tr>
    <?php $i=1; foreach($rooms as $r): ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= $r['room_number'] ?></td>
      <td><?= $r['type'] ?></td>
      <td>Rp <?= number_format($r['price'],0,',','.') ?></td>
      <td><a class="btn btn-primary" href="book.php?room_id=<?= $r['id'] ?>">Booking</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
