<?php
require_once 'db.php';

$res = $mysqli->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $res->fetch_all(MYSQLI_ASSOC);

$sum = $mysqli->query("SELECT SUM(amount) as total FROM payments")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Laporan Booking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

  <h2>Laporan Transaksi</h2>

  <p><strong>Total Pendapatan:</strong> Rp <?= number_format($sum,0,',','.') ?></p>

  <table>
    <tr>
      <th>No</th>
      <th>Kode Booking</th>
      <th>Nama</th>
      <th>Check-in</th>
      <th>Check-out</th>
      <th>Total</th>
      <th>Status</th>
    </tr>

    <?php $i=1; foreach($bookings as $b): ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= $b['booking_code'] ?></td>
      <td><?= $b['customer_name'] ?></td>
      <td><?= $b['checkin_date'] ?></td>
      <td><?= $b['checkout_date'] ?></td>
      <td>Rp <?= number_format($b['total_amount'],0,',','.') ?></td>
      <td><?= $b['status'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <a href="index.php" class="btn btn-secondary">← Kembali</a>

</div>

</body>
</html>
