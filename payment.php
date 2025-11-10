<?php
require_once 'db.php';

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if (!$booking_id) {
    echo "Booking tidak ditemukan.";
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM bookings WHERE id=?");
$stmt->bind_param('i',$booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
if (!$booking) { echo "Booking tidak ditemukan."; exit; }

$det = $mysqli->prepare("SELECT br.*, r.room_number, r.type FROM booking_rooms br JOIN rooms r ON r.id = br.room_id WHERE br.booking_id = ?");
$det->bind_param('i',$booking_id);
$det->execute();
$details = $det->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payment</title></head><body>
  <h2>Bayar Booking - <?= htmlspecialchars($booking['booking_code']) ?></h2>
  <p>Nama: <?= htmlspecialchars($booking['customer_name']) ?><br>
  Check-in: <?= $booking['checkin_date'] ?> - Check-out: <?= $booking['checkout_date'] ?></p>

  <h3>Detail Kamar</h3>
  <table border="1" cellpadding="5">
    <tr><th>Room</th><th>Type</th><th>Price</th><th>Nights</th><th>Subtotal</th></tr>
    <?php foreach($details as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['room_number']) ?></td>
        <td><?= htmlspecialchars($d['type']) ?></td>
        <td><?= number_format($d['price'],0,',','.') ?></td>
        <td><?= $d['nights'] ?></td>
        <td><?= number_format($d['subtotal'],0,',','.') ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h3>Total: <?= number_format($booking['total_amount'],0,',','.') ?></h3>

  <?php if ($booking['status'] !== 'paid'): ?>
    <form method="post" action="process_payment.php">
      <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
      <label>Jumlah Bayar: <input type="number" name="amount" value="<?= $booking['total_amount'] ?>" required></label><br><br>
      <label>Metode: <select name="method"><option>Cash</option><option>Transfer</option></select></label><br><br>
      <label>Catatan: <input type="text" name="note"></label><br><br>
      <button type="submit">Konfirmasi Pembayaran</button>
    </form>
  <?php else: ?>
    <p><strong>Sudah Lunas</strong></p>
  <?php endif; ?>

  <p><a href="index.php">Kembali ke daftar kamar</a></p>
</body></html>
