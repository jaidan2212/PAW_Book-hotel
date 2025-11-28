<?php
require_once '../db.php';
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dewasa  = isset($_GET['dewasa']) ? (int)$_GET['dewasa'] : 1;
$anak    = isset($_GET['anak']) ? (int)$_GET['anak'] : 0;
$checkin = $_GET['checkin']  ?? null;
$checkout = $_GET['checkout'] ?? null;

$rooms = getRooms();

$roomsFiltered = array_values(array_filter($rooms, function($r) use ($dewasa, $anak) {

    $capAdult = isset($r['capacity_adult']) ? (int)$r['capacity_adult'] : 1;
    $capChild = isset($r['capacity_child']) ? (int)$r['capacity_child'] : 0;

    $enoughAdult = $capAdult >= $dewasa;
    $enoughChild = $capChild >= $anak;

    return $enoughAdult && $enoughChild;
}));

$noResults = false;

if (empty($roomsFiltered)) {
    $noResults = true;
    $roomsFiltered = $rooms; 
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Solaz Resort | Rooms</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <meta name="viewport" content="width=device-width,initial-scale=1">
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

    <h3 class="mb-3 fw-bold">
        <?php if (!$noResults): ?>
            Ditemukan kamar yang sesuai untuk 
            <strong><?= $dewasa ?></strong> dewasa & 
            <strong><?= $anak ?></strong> anak.
        <?php else: ?>
            Tidak ada kamar yang tepat — menampilkan yang paling mendekati.
        <?php endif; ?>
    </h3>

    <?php if (empty($roomsFiltered)): ?>
        <div class="alert alert-warning">Tidak ada kamar yang tersedia.</div>

    <?php else: ?>

    <div class="row g-4">
        <?php foreach ($roomsFiltered as $r): 
            $img = (!empty($r['image'])) 
                ? "../uploads/".$r['image'] 
                : "../assets/images/room1.jpg";
        ?>
            <div class="col-md-4">

                <div class="card h-100 shadow-sm">
                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:220px;object-fit:cover">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= htmlspecialchars($r['room_number']." — ".$r['type']) ?>
                        </h5>

                        <p class="fw-bold text-primary">
                            Rp <?= number_format($r['price'],0,',','.') ?> / malam
                        </p>

                        <p class="text-muted small">
                            Kapasitas: 
                            <?= $r['capacity_adult'] ?> Dewasa, 
                            <?= $r['capacity_child'] ?> Anak
                        </p>

                        <p>
                            <?php if ($r['stock'] > 0): ?>
                                <span class="badge bg-info text-dark">Sisa <?= $r['stock'] ?> kamar</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Habis</span>
                            <?php endif; ?>
                        </p>

                        <a href="book.php?room_id=<?= $r['id'] ?>&checkin=<?= urlencode($checkin) ?>&checkout=<?= urlencode($checkout) ?>"
                           class="btn btn-primary mt-auto">
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

</body>
</html>
