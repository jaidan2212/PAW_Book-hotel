<?php
require_once '../db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function recommend_room_type($adult, $child)
{
    $total = $adult + $child;
    if ($adult === 1 && $child === 0) return 'Single';
    if ($adult === 1 && $child === 1) return 'Double';
    if ($total >= 4) return 'Suite';
    return 'Double';
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$dewasa = isset($_GET['dewasa']) ? (int)$_GET['dewasa'] : 1;
$anak = isset($_GET['anak']) ? (int)$_GET['anak'] : 0;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
$checkin = $_GET['checkin'] ?? null;
$checkout = $_GET['checkout'] ?? null;

$where = [];
if ($type !== '') {
    $where[] = "type = '" . $mysqli->real_escape_string($type) . "'";
}

$stockExists = false;
try {
    $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if ($chk && $chk->num_rows > 0) $stockExists = true;
} catch (mysqli_sql_exception $e) {
    $stockExists = false;
}

$where[] = "status = 'available'";
if ($stockExists) {
    $where[] = "stock > 0";
}

$capAdultExists = false;
$capChildExists = false;
try {
    $ca = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'capacity_adult'");
    $cc = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'capacity_child'");
    if ($ca && $ca->num_rows > 0) $capAdultExists = true;
    if ($cc && $cc->num_rows > 0) $capChildExists = true;
} catch (mysqli_sql_exception $e) {
    $capAdultExists = $capChildExists = false;
}

if ($capAdultExists) {
    $where[] = "capacity_adult >= " . intval($dewasa);
}
if ($capChildExists) {
    $where[] = "capacity_child >= " . intval($anak);
}

if ($q !== '') {
    $safe = $mysqli->real_escape_string($q);
    $where[] = "(room_number LIKE '%$safe%' OR type LIKE '%$safe%' OR description LIKE '%$safe%')";
}

if (!is_null($min_price)) {
    $where[] = "price >= " . floatval($min_price);
}
if (!is_null($max_price)) {
    $where[] = "price <= " . floatval($max_price);
}

$sql = "SELECT * FROM rooms";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id ASC";

$result = $mysqli->query($sql);
$rooms = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$recommended = recommend_room_type($dewasa, $anak);
$noResults = empty($rooms);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Solaz Resort | Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Booking Hotel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="rooms.php">Rooms</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h3 class="mb-3">
            <?php if ($q !== ''): ?>
                Hasil untuk: <strong><?= htmlspecialchars($q) ?></strong>
            <?php else: ?>
                Explore Rooms
            <?php endif; ?>

            <?php if ($type): ?>
                — Tipe: <strong><?= htmlspecialchars($type) ?></strong>
            <?php endif; ?>
        </h3>

        <div class="mb-3">
            <small class="text-muted">Rekomendasi tipe untuk permintaan kamu: <strong><?= htmlspecialchars($recommended) ?></strong></small>
        </div>

        <?php if ($noResults): ?>
            <div class="alert alert-warning">Tidak ada kamar cocok ditemukan.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($rooms as $r):
                    if (!empty($r['image'])) {
                        if (str_starts_with($r['image'], 'http')) {
                            $img = $r['image'];
                        } else {
                            $img = "../assets/images/" . $r['image'];
                        }
                    } else {
                        switch ($r['type']) {
                            case 'Single':
                                $img = "../assets/images/room1.jpg";
                                break;
                            case 'Double':
                                $img = "../assets/images/room2.jpeg";
                                break;
                            case 'Suite':
                                $img = "../assets/images/room3.jpeg";
                                break;
                            default:
                                $img = "../assets/images/default.jpeg";
                                break;
                        }
                    }

                    $stock = isset($r['stock']) ? (int)$r['stock'] : null;
                    $capAdult = isset($r['capacity_adult']) ? (int)$r['capacity_adult'] : '-';
                    $capChild = isset($r['capacity_child']) ? (int)$r['capacity_child'] : '-';
                    $badge = ($r['status'] === 'available') ? 'bg-success' : 'bg-danger';
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:220px; object-fit:cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($r['room_number'] . ' — ' . $r['type']) ?></h5>
                                <p class="fw-bold text-primary">Rp <?= number_format($r['price'], 0, ',', '.') ?> / malam</p>
                                <p class="text-muted small">
                                    <?= nl2br(htmlspecialchars($r['description'])) ?>
                                </p>
                                <p class="text-muted small">Kapasitas: <?= htmlspecialchars($capAdult) ?> Dewasa, <?= htmlspecialchars($capChild) ?> Anak</p>
                                <p>
                                    <?php if (!is_null($stock)): ?>
                                        <?php if ($stock > 0): ?>
                                            <span class="badge bg-info text-dark">Sisa <?= $stock ?> kamar</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge <?= $badge ?>"><?= htmlspecialchars($r['status']) ?></span>
                                    <?php endif; ?>
                                </p>

                                <a href="book.php?room_id=<?= (int)$r['id'] ?>&checkin=<?= urlencode($checkin) ?>&checkout=<?= urlencode($checkout) ?>" class="btn btn-primary mt-auto <?= ($r['status'] !== 'available') ? 'disabled' : '' ?>">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="../index.php" class="btn btn-outline-secondary">← Kembali</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>