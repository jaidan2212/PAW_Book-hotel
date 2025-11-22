<?php
require_once 'functions.php';
$rooms = getRooms();

$filterType = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($filterType !== '') {
    $rooms = array_values(array_filter($rooms, function($r) use ($filterType) {
        return isset($r['type']) && $r['type'] === $filterType;
    }));
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Booking Hotel | Rooms</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">Booking Hotel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="rooms.php">Rooms</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <h1 class="mb-4"><?php echo $filterType ? 'Rooms: ' . htmlspecialchars($filterType, ENT_QUOTES) : 'Our Rooms'; ?></h1>
        <?php if ($filterType): ?>
            <p><a href="rooms.php" class="btn btn-sm btn-outline-secondary">Lihat semua tipe</a></p>
        <?php endif; ?>

        <?php if (empty($rooms)): ?>
            <div class="alert alert-warning">Tidak ada kamar untuk tipe <strong><?= htmlspecialchars($filterType, ENT_QUOTES) ?></strong>.</div>
        <?php else: ?>
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
                            <p class="text-success fw-bold">Rp <?= number_format($r['price'], 0, ',', '.') ?> / night</p>
                            <p class="mb-1"><span class="badge bg-info text-dark">Tersisa <?= $stock ?> kamar</span></p>
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="book.php?room_id=<?= (int)$r['id'] ?>&unit=<?= $i ?>" class="btn btn-primary">Book Now</a>
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
                            <p class="text-success fw-bold">Rp <?= number_format($r['price'], 0, ',', '.') ?> / night</p>
                            <?php if ($stock): ?>
                                <p class="mb-1"><span class="badge bg-info text-dark">Tersisa <?= $stock ?> kamar</span></p>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="book.php?room_id=<?= (int)$r['id'] ?>&unit=1" class="btn btn-primary">Book Now</a>
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
        <?php endif; ?>
    </main>

    <footer class="text-center py-4">
        <small>© 2025 Booking Hotel</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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