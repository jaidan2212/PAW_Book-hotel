<?php
require_once 'db.php';

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

$det = $mysqli->prepare("
    SELECT br.*, r.room_number, r.type 
    FROM booking_rooms br 
    JOIN rooms r ON r.id = br.room_id 
    WHERE br.booking_id = ?
");
$det->bind_param('i', $booking_id);
$det->execute();
$details = $det->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Pembayaran Booking</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

  <h2>Pembayaran</h2>
  <p><strong>Kode Booking:</strong> <?= $booking['booking_code'] ?></p>
  <p><strong>Nama:</strong> <?= $booking['customer_name'] ?></p>
  <p><strong>Tanggal:</strong> <?= $booking['checkin_date'] ?> → <?= $booking['checkout_date'] ?></p>

  <h3>Detail Kamar</h3>
  <table>
    <tr><th>Kamar</th><th>Tipe</th><th>Harga</th><th>Malam</th><th>Subtotal</th></tr>
    <?php foreach($details as $d): ?>
    <tr>
      <td><?= $d['room_number'] ?></td>
      <td><?= $d['type'] ?></td>
      <td>Rp <?= number_format($d['price'],0,',','.') ?></td>
      <td><?= $d['nights'] ?></td>
      <td>Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h3>Total: Rp <?= number_format($booking['total_amount'],0,',','.') ?></h3>

  <?php if ($booking['status'] !== 'paid'): ?>
  <form action="process_payment.php" method="post">
    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

    <div class="mb-3">
      <label>Jumlah Bayar</label>
      <input type="number" name="amount" value="<?= $booking['total_amount'] ?>" required>
    </div>

    <div class="mb-3">
      <label>Metode Pembayaran</label>
      <select name="method">
        <option>Cash</option>
        <option>Transfer</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Catatan (opsional)</label>
      <input type="text" name="note">
    </div>

    <button class="btn-submit">Konfirmasi Pembayaran</button>
  </form>
  <?php else: ?>
  <p><strong style="color:green;">Sudah Lunas ✅</strong></p>
  <?php endif; ?>

  <a href="index.php" class="btn btn-secondary">← Kembali</a>

</div>

</body>
</html>
