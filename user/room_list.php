<?php
require_once "../db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$type = isset($_GET['type']) ? trim($_GET['type']) : '';
if ($type === '') die("Invalid room type.");

$type_safe = $mysqli->real_escape_string($type);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_safe = $mysqli->real_escape_string($search);

$limit = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$countSql = "
    SELECT COUNT(*) AS total 
    FROM rooms 
    WHERE type = '$type_safe' AND status = 'available'
";

if ($search !== '') {
    $countSql .= " AND (room_number LIKE '%$search_safe%' OR description LIKE '%$search_safe%')";
}

$totalRows = $mysqli->query($countSql)->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "
    SELECT * FROM rooms
    WHERE type = '$type_safe' AND status = 'available'
";

if ($search !== '') {
    $sql .= " AND (room_number LIKE '%$search_safe%' OR description LIKE '%$search_safe%')";
}

$chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
if ($chk && $chk->num_rows > 0) $sql .= " AND stock > 0";

$sql .= " ORDER BY price ASC LIMIT $limit OFFSET $offset";
$query = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?=htmlspecialchars($type)?> Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#eef3f9; font-family: 'Segoe UI', sans-serif; }

        .room-card {
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
            transition:.25s;
        }
        .room-card:hover {
            transform:translateY(-4px);
            box-shadow:0 8px 18px rgba(0,0,0,0.12);
        }
        .room-img {
            width:100%;
            height:160px;
            object-fit:cover;
        }
        .room-title {
            font-size:1.2rem;
            font-weight:600;
            color:#0d3b66;
        }
        .price-tag {
            font-size:1.1rem;
            font-weight:bold;
            color:#0d3b66;
        }
    </style>
</head>
<body>

<div class="container py-5">

    <h2 class="text-center mb-4 fw-bold text-primary">
        Kamar Tipe <?=htmlspecialchars($type)?>
    </h2>

    <form method="GET" class="row mb-4">
        <input type="hidden" name="type" value="<?=htmlspecialchars($type)?>">
        <div class="col-md-9">
            <input 
                type="text" 
                name="search"
                value="<?=htmlspecialchars($search)?>"
                class="form-control shadow-sm"
                placeholder="Cari berdasarkan nomor kamar atau deskripsi..."
            >
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100 shadow-sm">Cari</button>
        </div>
    </form>
    <div class="row g-6">

        <?php if ($query && $query->num_rows > 0): ?>
            <?php while ($room = $query->fetch_assoc()): ?>

                <?php
                
                    if (!empty($room['image'])) {
                        $img = (str_starts_with($room['image'], 'http'))
                                ? $room['image']
                                : "../uploads/" . $room['image'];
                    } else {
                        switch ($room['type']) {
                            case 'Single': $img = "../assets/images/room1.jpeg"; break;
                            case 'Double': $img = "../assets/images/room3.jpeg"; break;
                            case 'Suite':  $img = "../assets/images/room2.jpeg"; break;
                            default: $img = "../assets/images/default.jpeg"; break;
                        }
                    }
                ?>

                <div class="col-md-5">
                    <div class="room-card">
                        <img src="<?=htmlspecialchars($img)?>" class="room-img">

                        <div class="p-3">
                            <div class="room-title">
                                <?=htmlspecialchars($room['type'])?> — No. <?=htmlspecialchars($room['room_number'])?>
                            </div>

                            <p class="small text-muted mt-1">
                                <?=htmlspecialchars($room['description'] ?? '')?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="price-tag">
                                    Rp <?=number_format($room['price'], 0, ',', '.')?>
                                </div>
                                <a href="book.php?id=<?= (int)$room['id'] ?>" 
                                   class="btn btn-sm btn-primary px-3">
                                    Book
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        <?php else: ?>
            <div class="col-12 text-center">
                <h5 class="text-muted">Tidak ada kamar ditemukan.</h5>
            </div>
        <?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        <nav>
            <ul class="pagination">

                <li class="page-item <?=($page <= 1 ? 'disabled' : '')?>">
                    <a class="page-link" 
                       href="?type=<?=$type?>&search=<?=$search?>&page=<?=$page-1?>">
                        &laquo;
                    </a>
                </li>

                <?php for ($i=1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?=($i == $page ? 'active' : '')?>">
                        <a class="page-link"
                           href="?type=<?=$type?>&search=<?=$search?>&page=<?=$i?>">
                           <?=$i?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?=($page >= $totalPages ? 'disabled' : '')?>">
                    <a class="page-link" 
                       href="?type=<?=$type?>&search=<?=$search?>&page=<?=$page+1?>">
                        &raquo;
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    <div class="text-center mt-4">
        <a href="../index.php" class="btn btn-outline-secondary">
            ← Kembali
        </a>
    </div>

</div>

</body>
</html>
