<?php
require_once 'user/functions.php';
$rooms = getRooms();

$hasStock = false;
try {
    $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if ($chk && $chk->num_rows > 0) $hasStock = true;
} catch (mysqli_sql_exception $e) {
    $hasStock = false;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Booking Hotel | Our Rooms</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">Booking Hotel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="user/rooms.php">Rooms</a></li>
                    <li class="nav-item"><a class="nav-link" href="user/facilities.php">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact us</a></li>
                </ul>
                <div class="d-flex">
                    <?php if(isset($_SESSION['user'])): ?>
                        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary me-2">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <h1 class="mb-4">Our Rooms</h1>

        <?php if (!$hasStock): ?>
            <div class="alert alert-warning">
                Kolom <strong>stock</strong> belum ada di database — itu penyebab hanya muncul 1 pilihan.
                <div class="mt-2">
                    <a class="btn btn-sm btn-primary" href="user/migrate_add_stock_column.php">Jalankan migrasi stock</a>
                    <a class="btn btn-sm btn-secondary" href="user/migrate_set_stock_demo.php?n=5">Set stock demo (n=5)</a>
                </div>
                <div class="mt-2 small text-muted">Jika kamu menggunakan phpMyAdmin, kamu juga bisa menjalankan: <code>ALTER TABLE rooms ADD COLUMN stock INT NOT NULL DEFAULT 1;</code></div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($rooms as $r): ?>
                <?php
                    $stock = isset($r['stock']) ? (int)$r['stock'] : 0;
                    if ($stock > 1) {
                        $max = min($stock, 20);
                        for ($i = 1; $i <= $max; $i++):
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/room1.jpg" class="card-img-top" alt="Room">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($r['room_number'] . ' - ' . $r['type'] . ' #' . $i, ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="text-success fw-bold">Rp <?= number_format($r['price'], 0, ',', '.') ?> per night</p>
                            <p class="mb-1"><span class="badge bg-info text-dark">Tersisa <?= $stock ?> kamar</span></p>
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="user/book.php?room_id=<?= (int)$r['id'] ?>&unit=<?= $i ?>" class="btn btn-primary">Book Now</a>
                                    <button type="button" class="btn btn-outline-info btn-stock" 
                                        data-room-id="<?= (int)$r['id'] ?>" 
                                        data-room-number="<?= htmlspecialchars($r['room_number'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-type="<?= htmlspecialchars($r['type'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-stock="<?= $stock ?>">
                                        Lihat Stok
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; }
                    else {
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/room1.jpg" class="card-img-top" alt="Room">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($r['room_number'] . ' - ' . $r['type'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="text-success fw-bold">Rp <?= number_format($r['price'], 0, ',', '.') ?> per night</p>
                            <?php if ($stock): ?>
                                <p class="mb-1"><span class="badge bg-info text-dark">Tersisa <?= $stock ?> kamar</span></p>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="user/book.php?room_id=<?= (int)$r['id'] ?>&unit=1" class="btn btn-primary">Book Now</a>
                                    <button type="button" class="btn btn-outline-info btn-stock" 
                                        data-room-id="<?= (int)$r['id'] ?>" 
                                        data-room-number="<?= htmlspecialchars($r['room_number'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-type="<?= htmlspecialchars($r['type'], ENT_QUOTES, 'UTF-8') ?>" 
                                        data-stock="<?= $stock ?>">
                                        Lihat Stok
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="text-center py-4">
        <small>© 2025 Booking Hotel</small>
    </footer>

        <div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="stockModalLabel">Stok Kamar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="stockModalRoom"></p>
                        <p id="stockModalCount" class="fw-bold fs-5 text-success"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            (function(){
                const stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
                document.querySelectorAll('.btn-stock').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const roomNumber = btn.getAttribute('data-room-number') || '';
                        const type = btn.getAttribute('data-type') || '';
                        const stock = parseInt(btn.getAttribute('data-stock') || '0', 10);
                        document.getElementById('stockModalRoom').textContent = roomNumber + ' — ' + type;
                        document.getElementById('stockModalCount').textContent = (stock > 0) ? (stock + ' kamar tersedia') : 'Stok habis';
                        stockModal.show();
                    });
                });
            })();
        </script>
    </body>
    </html>
