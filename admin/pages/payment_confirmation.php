<?php
require_once '../db.php';

$res = $mysqli->query("
    SELECT payments.*, bookings.customer_name, bookings.booking_code 
    FROM payments 
    JOIN bookings ON payments.booking_id = bookings.id
    ORDER BY payments.payment_date DESC
");
$payments = $res->fetch_all(MYSQLI_ASSOC);

$total = $mysqli->query("SELECT SUM(amount) as t FROM payments")->fetch_assoc()['t'] ?? 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Pembayaran</h3>
  </div>

  <div class="mb-2">Total Pembayaran Masuk: <strong>Rp <?= number_format($total,0,',','.') ?></strong></div>

  <div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Kode Booking</th>
        <th>Nama</th>
        <th>Metode</th>
        <th>Tanggal</th>
        <th class="text-end">Jumlah</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
    <?php $i=1; foreach($payments as $p): ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= $p['booking_code'] ?></td>
      <td><?= $p['customer_name'] ?></td>
      <td><?= $p['method'] ?></td>
      <td><?= $p['payment_date'] ?></td>
      <td class="text-end">Rp <?= number_format($p['amount'],0,',','.') ?></td>
      <td><?= $p['note'] ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
