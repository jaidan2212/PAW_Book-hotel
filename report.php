<?php
require_once 'db.php';

$res = $mysqli->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $res->fetch_all(MYSQLI_ASSOC);

$sum = $mysqli->query("SELECT SUM(amount) as total FROM payments")->fetch_assoc()['total'] ?? 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laporan Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Laporan Transaksi</h3>
    <a href="index.php" class="btn btn-outline-secondary">← Kembali</a>
  </div>

  <div class="mb-3">Total Pendapatan: <strong>Rp <?= number_format($sum,0,',','.') ?></strong></div>

  <div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Kode Booking</th>
        <th>Nama</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th class="text-end">Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
    <?php $i=1; foreach($bookings as $b): ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= htmlspecialchars($b['booking_code'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($b['customer_name'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($b['checkin_date'], ENT_QUOTES, 'UTF-8') ?></td>
      <td><?= htmlspecialchars($b['checkout_date'], ENT_QUOTES, 'UTF-8') ?></td>
      <td class="text-end">Rp <?= number_format($b['total_amount'],0,',','.') ?></td>
      <td><?= htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
