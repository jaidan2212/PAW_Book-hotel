<?php
require_once 'db.php';

$res = $mysqli->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $res->fetch_all(MYSQLI_ASSOC);

$sum = $mysqli->query("SELECT SUM(amount) as total_paid FROM payments")->fetch_assoc()['total_paid'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Laporan</title></head>
<body>
  <h2>Laporan Booking</h2>
  <p>Total pendapatan dari pembayaran: Rp <?= number_format($sum ?? 0,0,',','.') ?></p>
  <table border="1" cellpadding="5">
    <tr><th>#</th><th>Booking Code</th><th>Customer</th><th>Checkin</th><th>Checkout</th><th>Total</th><th>Status</th></tr>
    <?php $i=1; foreach($bookings as $b): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($b['booking_code']) ?></td>
        <td><?= htmlspecialchars($b['customer_name']) ?></td>
        <td><?= $b['checkin_date'] ?></td>
        <td><?= $b['checkout_date'] ?></td>
        <td><?= number_format($b['total_amount'],0,',','.') ?></td>
        <td><?= $b['status'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p><a href="index.php">Home</a></p>
</body>
</html>
