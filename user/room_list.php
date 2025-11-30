<?php
require_once "../db.php";

$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($type === '') {
    die("Invalid room type.");
}

$type_safe = $mysqli->real_escape_string($type);

$sql = "SELECT * FROM rooms WHERE type = '$type_safe' AND status = 'available'";
$chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
if ($chk && $chk->num_rows > 0) {
    $sql .= " AND stock > 0";
}
$sql .= " ORDER BY price ASC";

$query = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?=htmlspecialchars($type)?> Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f2f4f7; }
        .room-card img { height:220px; object-fit:cover; }
        .room-card { border-radius:12px; overflow:hidden; transition: .25s; }
        .room-card:hover { transform: translateY(-6px); box-shadow: 0 12px 22px rgba(0,0,0,0.12); }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4"><?=htmlspecialchars($type)?> Rooms</h2>

    <form method="GET" class="row mb-4">
        <input type="hidden" name="type" value="<?=htmlspecialchars($type)?>">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Cari room number atau deskripsi...">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if ($query && $query->num_rows > 0): ?>
            <?php while ($room = $query->fetch_assoc()):
                if (!empty($room['image'])) {
                    if (str_starts_with($room['image'], 'http')) $img = $room['image'];
                    else $img = "../uploads/" . $room['image'];
                } else {
                    switch ($room['type']) {
                        case 'Single': $img = "../assets/images/room1.jpg"; break;
                        case 'Double': $img = "../assets/images/room2.jpeg"; break;
                        case 'Suite':  $img = "../assets/images/room3.jpeg"; break;
                        default: $img = "../assets/images/default.jpeg"; break;
                    }
                }
                $badgeClass = ($room['status'] === 'available') ? 'bg-success' : 'bg-danger';
            ?>
            <div class="col-md-4">
                <div class="card room-card h-100 shadow-sm">
                    <img src="<?=htmlspecialchars($img)?>" class="card-img-top" alt="Room image">
                    <div class="card-body">
                        <h5 class="card-title"><?=htmlspecialchars($room['type'])?> Room</h5>
                        <p class="small text-muted">
                            <strong>No:</strong> <?=htmlspecialchars($room['room_number'])?><br>
                            <strong>Price:</strong> Rp <?=number_format($room['price'])?><br>
                            <strong>Status:</strong> <span class="badge <?=$badgeClass?>"><?=htmlspecialchars($room['status'])?></span>
                        </p>
                        <a href="book.php?id=<?= (int)$room['id'] ?>" class="btn btn-success w-100 <?= ($room['status'] !== 'available') ? 'disabled' : '' ?>">Book Now</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <h5 class="text-muted">Tidak ada kamar ditemukan untuk tipe ini.</h5>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4"><a href="../index.php" class="btn btn-outline-secondary">← Kembali</a></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
