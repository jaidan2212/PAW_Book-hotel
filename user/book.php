<?php
session_start();
if (!isset($_SESSION['user'])) {
    $id = $_GET['id'] ?? '';
    header("Location: ../login.php?redirect=user/book.php?id=$id");
    exit;
}
?>

<?php
require_once __DIR__ . '/../layout/path.php';
include __DIR__ . '/../layout/navbar.php';
require_once 'functions.php';
$rooms = getRooms();

$reservedMessage = '';
$selectedUnit = null;
if (isset($_GET['room_id'])) {
  $rid = (int)$_GET['room_id'];
  $ok = reserveRoom($rid, 15); 
  if ($ok) {
    $reservedMessage = 'Kamar berhasil ditahan selama 15 menit. Silakan lanjut mengisi form.';
  } else {
    $reservedMessage = 'Gagal menahan kamar. Mungkin sudah dipesan atau ditahan orang lain.';
  }
}

if (isset($_GET['unit'])) {
  $selectedUnit = max(1, (int)$_GET['unit']);
}

$rooms = getRooms();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<main class="container py-5">
  <div class="card mx-auto" style="max-width:700px;">
    <div class="card-body">
      <h3 class="card-title mb-3">Form Booking</h3>

      <form action="process_booking.php" method="post">
        <?= csrf_input_field() ?>

        <div class="mb-3">
          <label class="form-label">Nama Pelanggan</label>
          <input class="form-control" type="text" name="customer_name" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email (Opsional)</label>
          <input class="form-control" type="email" name="customer_email">
        </div>

        <div class="mb-3">
          <label class="form-label">Pilih Kamar</label>
          <select name="room_id" id="room_id" class="form-select" required>
            <option value="">-- pilih --</option>
            <?php foreach($rooms as $r): ?>
            <option value="<?= (int)$r['id'] ?>" data-stock="<?= isset($r['stock']) ? (int)$r['stock'] : 0 ?>">
              <?= htmlspecialchars($r['room_number'].' - '.$r['type'].' (Rp '.number_format($r['price'],0,',','.').')', ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <?php if ($selectedUnit): ?>
          <div class="mb-3">
            <label class="form-label">Unit Terpilih</label>
            <input class="form-control" type="text" value="#<?= htmlspecialchars($selectedUnit, ENT_QUOTES) ?>" disabled>
            <input type="hidden" name="unit" value="<?= htmlspecialchars($selectedUnit, ENT_QUOTES) ?>">
          </div>
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Jumlah Kamar</label>
          <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" required>
          <div class="form-text" id="stockHelp"></div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Check-in</label>
            <input class="form-control" type="date" name="checkin_date" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Check-out</label>
            <input class="form-control" type="date" name="checkout_date" required>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-success">Simpan & Lanjut</button>
          <a href="rooms.php" class="btn btn-outline-secondary">← Kembali</a>
        </div>
      </form>
      <?php if ($reservedMessage): ?>
        <div class="alert alert-info"><?= htmlspecialchars($reservedMessage, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (function(){
    const roomSelect = document.getElementById('room_id');
    const qty = document.getElementById('quantity');
    const stockHelp = document.getElementById('stockHelp');
    if (!roomSelect || !qty) return;

    function update() {
      const opt = roomSelect.options[roomSelect.selectedIndex];
      if (!opt) return;
      const stock = parseInt(opt.getAttribute('data-stock') || '0', 10);
      if (stock > 0) {
        qty.max = stock;
        stockHelp.textContent = 'Tersisa ' + stock + ' kamar.';
      } else {
        qty.removeAttribute('max');
        stockHelp.textContent = '';
      }
    }

    roomSelect.addEventListener('change', update);
    update();
  })();
</script>


</body>
</html>
