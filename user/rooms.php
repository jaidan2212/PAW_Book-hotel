<?php
require_once '../db.php';
require_once __DIR__ . '/../layout/path.php';
include __DIR__ . '/../layout/navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function esc($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
function recommend_room_type($adult, $child)
{
    $total = $adult + $child;
    if ($adult === 1 && $child === 0) return 'Single';
    if ($adult === 1 && $child === 1) return 'Double';
    if ($total >= 4) return 'Suite';
    return 'Double';
}

$q         = trim($_GET['q'] ?? '');
$type      = trim($_GET['type'] ?? '');
$dewasa    = (int)($_GET['dewasa'] ?? 1);
$anak      = (int)($_GET['anak'] ?? 0);
$min_price = ($_GET['min_price'] ?? '') !== '' ? (float)$_GET['min_price'] : null;
$max_price = ($_GET['max_price'] ?? '') !== '' ? (float)$_GET['max_price'] : null;
$checkin   = $_GET['checkin']  ?? '';
$checkout  = $_GET['checkout'] ?? '';

$where = [];

if ($type !== '') {
    $safe = $mysqli->real_escape_string($type);
    $where[] = "type = '$safe'";
}

$where[] = "status = 'available'";

$stockExists = false;
if ($chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'")) {
    $stockExists = $chk->num_rows > 0;
}

if ($stockExists) {
    $where[] = "stock > 0";
}

$capAdultExists = false;
$capChildExists = false;

$ca = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'capacity_adult'");
$cc = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'capacity_child'");
if ($ca && $ca->num_rows > 0) $capAdultExists = true;
if ($cc && $cc->num_rows > 0) $capChildExists = true;

if ($capAdultExists) $where[] = "capacity_adult >= " . intval($dewasa);
if ($capChildExists) $where[] = "capacity_child >= " . intval($anak);


if ($q !== '') {
    $safe = $mysqli->real_escape_string($q);
    $where[] = "(room_number LIKE '%$safe%' OR type LIKE '%$safe%' OR description LIKE '%$safe%')";
}

if (!is_null($min_price)) $where[] = "price >= " . floatval($min_price);
if (!is_null($max_price)) $where[] = "price <= " . floatval($max_price);

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

    <div class="container py-4">
        <h3 class="mb-3">
            <?php if ($q !== ''): ?>
                Hasil untuk: <strong><?= esc($q) ?></strong>
            <?php else: ?>
                Explore Rooms
            <?php endif; ?>

            <?php if ($type !== ''): ?>
                — Tipe: <strong><?= esc($type) ?></strong>
            <?php endif; ?>
        </h3>

        <div class="mb-3">
            <small class="text-muted">Rekomendasi tipe untuk permintaan kamu: <strong><?= esc($recommended) ?></strong></small>
        </div>

        <?php if ($noResults): ?>
            <div class="alert alert-warning">Tidak ada kamar cocok ditemukan.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($rooms as $r): 
                    
                    $imgRaw = $r['image'] ?? '';
                    if ($imgRaw) {
                        if (str_starts_with($imgRaw, 'http')) {
                            $img = $imgRaw;
                        } else {
                            $img = "../assets/images/" . $imgRaw;
                        }
                    } else {
                        switch ($r['type'] ?? '') {
                            case 'Single': $img = "../assets/images/room1.jpeg"; break;
                            case 'Double': $img = "../assets/images/room3.jpeg"; break;
                            case 'Suite':  $img = "../assets/images/room2.jpeg"; break;
                            default:       $img = "../assets/images/default.jpeg"; break;
                        }
                    }
                    $roomNumber = esc($r['room_number'] ?? '');
                    $roomType   = esc($r['type'] ?? '');
                    $price      = number_format($r['price'] ?? 0, 0, ',', '.');
                    $desc       = nl2br(esc($r['description'] ?? ''));
                    $capAdult   = esc($r['capacity_adult'] ?? '-');
                    $capChild   = esc($r['capacity_child'] ?? '-');
                    $status     = esc($r['status'] ?? 'unknown');

                    $stock = $r['stock'] ?? null;
                    $badge = ($r['status'] === 'available') ? 'bg-success' : 'bg-danger';
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= esc($img) ?>" class="card-img-top" style="height:220px; object-fit:cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= $roomNumber ?> — <?= $roomType ?></h5>
                                <p class="fw-bold text-primary">Rp <?= $price ?> / malam</p>
                                <p class="text-muted small"><?= $desc ?></p>
                                <p class="text-muted small">Kapasitas: <?= $capAdult ?> Dewasa, <?= $capChild ?> Anak</p>

                                <p>
                                    <?php if (!is_null($stock)): ?>
                                        <?php if ((int)$stock > 0): ?>
                                            <span class="badge bg-info text-dark">Sisa <?= esc($stock) ?> kamar</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge <?= $badge ?>"><?= $status ?></span>
                                    <?php endif; ?>
                                </p>

                                <a href="book.php?room_id=<?= intval($r['id']) ?>&checkin=<?= esc($checkin) ?>&checkout=<?= esc($checkout) ?>" 
                                   class="btn btn-primary mt-auto <?= ($r['status'] !== 'available') ? 'disabled' : '' ?>">
                                   Book Now
                                </a>
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

<?php include __DIR__ . '/../layout/footer.php'; ?>

</body>

</html>