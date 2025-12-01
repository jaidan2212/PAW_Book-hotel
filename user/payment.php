<?php
require_once '../db.php';
require_once 'functions.php';

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if (!$booking_id) {
    echo "Booking tidak ditemukan.";
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM bookings WHERE id=?");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
if (!$booking) {
    echo "Booking tidak ditemukan.";
    exit;
}

$det = $mysqli->prepare("SELECT br.*, r.room_number, r.type FROM booking_rooms br JOIN rooms r ON r.id = br.room_id WHERE br.booking_id = ?");
$det->bind_param('i', $booking_id);
$det->execute();
$details = $det->get_result()->fetch_all(MYSQLI_ASSOC);

$pstmt = $mysqli->prepare("SELECT * FROM payments WHERE booking_id = ? ORDER BY payment_date DESC");
$pstmt->bind_param('i', $booking_id);
$pstmt->execute();
$payments = $pstmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pembayaran Booking</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  @media print {
    .no-print { display: none !important; }
  }
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm no-print">
  <div class="container">
    <a class="navbar-brand" href="index.php">Booking Hotel</a>
  </div>
</nav>

<main class="container my-4">
  <div class="card">
    <div class="card-body">
      <div id="receipt-area">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="mb-0">Booking Hotel</h4>
            <small class="text-muted">Pembayaran & Nota</small>
          </div>
          <div class="text-end">
            <small>Booking ID: <?= (int)$booking['id'] ?></small><br>
            <small>Kode: <?= htmlspecialchars($booking['booking_code'], ENT_QUOTES, 'UTF-8') ?></small>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-md-6">
            <strong>Nama:</strong> <?= htmlspecialchars($booking['customer_name'], ENT_QUOTES, 'UTF-8') ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($booking['customer_email'], ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="col-md-6 text-md-end">
            <strong>Tanggal:</strong> <?= htmlspecialchars($booking['checkin_date'], ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars($booking['checkout_date'], ENT_QUOTES, 'UTF-8') ?><br>
            <strong>Status:</strong> <?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        </div>

        <h6>Detail Kamar</h6>
        <div class="table-responsive">
        <table class="table table-sm table-bordered">
          <thead class="table-light">
            <tr>
              <th>Kamar</th>
              <th>Tipe</th>
              <th class="text-end">Harga</th>
              <th class="text-center">Malam</th>
              <th class="text-center">Jumlah</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($details as $d): ?>
            <tr>
              <td><?= htmlspecialchars($d['room_number'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($d['type'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="text-end">Rp <?= number_format($d['price'],0,',','.') ?></td>
              <td class="text-center"><?= (int)$d['nights'] ?></td>
              <td class="text-center"><?= isset($d['quantity']) ? (int)$d['quantity'] : 1 ?></td>
              <td class="text-end">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>

        <div class="d-flex justify-content-end">
          <div class="text-end">
            <div>Total:</div>
            <div class="fs-5 text-success">Rp <?= number_format($booking['total_amount'],0,',','.') ?></div>
          </div>
        </div>

        <?php if (!empty($payments)): ?>
          <hr>
          <h6>Riwayat Pembayaran</h6>
          <table class="table table-sm">
            <thead><tr><th>Tanggal</th><th class="text-end">Jumlah</th><th>Metode</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php foreach($payments as $pm): ?>
              <tr>
                <td><?= htmlspecialchars($pm['payment_date'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-end">Rp <?= number_format($pm['amount'],0,',','.') ?></td>
                <td><?= htmlspecialchars($pm['method'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($pm['note'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <div class="mt-3 no-print">
        <?php if ($booking['status'] !== 'paid'): ?>
          <form action="process_payment.php" method="post" class="row g-2">
            <?= csrf_input_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">

            <div class="col-12 col-md-4">
              <label class="form-label">Jumlah Bayar</label>
              <input class="form-control" type="number" name="amount" value="<?= htmlspecialchars($booking['total_amount'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Metode Pembayaran</label>
              <select name="method" class="form-select">
                <option>Cash</option>
                <option>Transfer</option>
              </select>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Catatan (opsional)</label>
              <input class="form-control" type="text" name="note">
            </div>

            <div class="col-12">
              <button class="btn btn-success">Konfirmasi Pembayaran</button>
            </div>
          </form>
        <?php else: ?>
          <div class="d-flex gap-2">
            <a href="../index.php" class="btn btn-outline-primary">Kembali</a>
            <button class="btn btn-primary" onclick="window.print()">Cetak Nota</button>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
