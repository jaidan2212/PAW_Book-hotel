<?php
require_once '../db.php';

$res = $mysqli->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Booking</h3>
  </div>

  <div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Kode Booking</th>
        <th>Nama Pelanggan</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th class="text-end">Total</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php $i=1; foreach ($bookings as $b): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= $b['booking_code'] ?></td>
        <td><?= $b['customer_name'] ?></td>
        <td><?= $b['checkin_date'] ?></td>
        <td><?= $b['checkout_date'] ?></td>
        <td class="text-end">Rp <?= number_format($b['total_amount'],0,',','.') ?></td>
        <td><?= ucfirst($b['status']) ?></td>
        <td>
          <a href="payment_confirmation.php?code=<?= $b['booking_code'] ?>" class="btn btn-sm btn-primary">Kelola</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
