<?php
require_once 'functions.php';
$rooms = getRooms();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Hotel - List Rooms</title>
</head>
<body>
  <h1>Daftar Kamar</h1>
  <a href="report.php">Laporan</a> | <a href="book.php">Buat Booking</a>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>No</th><th>Room</th><th>Type</th><th>Price</th><th>Aksi</th></tr>
    <?php $i=1; foreach($rooms as $r): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($r['room_number']) ?></td>
        <td><?= htmlspecialchars($r['type']) ?></td>
        <td><?= number_format($r['price'],0,',','.') ?></td>
        <td><a href="book.php?room_id=<?= $r['id'] ?>">Booking</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
