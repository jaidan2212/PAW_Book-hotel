<?php
require_once '../db.php';
require_once 'functions.php';
require_once __DIR__ . '/../layout/path.php';
include __DIR__ . '/../layout/navbar.php';

$sql = "SELECT type, COUNT(*) AS available_count, COUNT(*) + 0 AS room_count FROM rooms WHERE status = 'available' GROUP BY type ORDER BY type";
$res = $mysqli->query($sql);
$types = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $types[$row['type']] = [
            'available' => (int)$row['available_count'],
            'count' => (int)$row['available_count']
        ];
    }
}

$known = ['Single','Double','Suite'];
foreach ($known as $kt) {
    if (!isset($types[$kt])) {
        $types[$kt] = ['available' => 0, 'count' => 0];
    }
}

$type_descriptions = [
    'Single' => 'Kamar nyaman untuk 1 tamu, cocok untuk pelancong bisnis atau tamu solo. Ukuran kompak namun dilengkapi fasilitas dasar untuk menginap yang nyaman.',
    'Double' => 'Kamar untuk 1-2 tamu dengan ruang lebih luas dan kenyamanan ekstra—cocok untuk pasangan atau tamu yang membutuhkan ruang lebih.',
    'Suite'  => 'Suite premium dengan ruang tamu terpisah, fasilitas lebih lengkap, dan kenyamanan ekstra untuk pengalaman menginap yang istimewa.'
];

$facilities_map = [
    'Single' => [
        'Kasur single berkualitas',
        'AC dengan kontrol suhu',
        'Wi‑Fi gratis kecepatan tinggi',
        'Kamar mandi pribadi dengan shower dan toiletries',
        'TV layar datar dengan saluran lokal/internasional',
        'Meja kerja & kursi',
        'Lemari pakaian',
        'Pengering rambut',
        'Ketel listrik (tea/coffee set)',
        'Brankas dalam kamar',
        'Layanan kebersihan harian'
    ],
    'Double' => [
        'Kasur double (atau twin) nyaman',
        'AC dengan kontrol suhu',
        'Wi‑Fi gratis kecepatan tinggi',
        'Kamar mandi dengan shower, toiletries, dan handuk',
        'TV layar datar',
        'Meja kerja',
        'Minibar (berbayar)',
        'Brankas dalam kamar',
        'Pengering rambut',
        'Ketel listrik (tea/coffee set)',
        'Layanan sarapan (opsional)',
        'Layanan kebersihan harian'
    ],
    'Suite' => [
        'King bed berkualitas',
        'Ruang tamu terpisah dengan sofa',
        'AC terpisah untuk ruang tidur & ruang tamu',
        'Wi‑Fi gratis kecepatan tinggi',
        'Kamar mandi besar dengan bathtub + shower dan toiletries premium',
        'TV layar datar di kamar dan ruang tamu',
        'Minibar & mesin kopi',
        'Meja kerja',
        'Brankas dalam kamar',
        'Layanan kamar 24 jam',
        'Sarapan termasuk (tergantung paket)',
        'Layanan kebersihan harian dan laundry (opsional)'
    ]
];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Hotel | Facilities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Booking Hotel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                <li class="nav-item"><a class="nav-link active" href="facilities.php">Facilities</a></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <h1 class="mb-4">Facilities & Stock per Room Type</h1>

    <div class="row g-4">
        <?php if (empty($types)): ?>
            <div class="col-12">
                <div class="alert alert-warning">Tidak ada data kamar. Pastikan tabel <code>rooms</code> berisi data.</div>
            </div>
        <?php else: ?>
            <?php foreach ($types as $type => $meta): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="assets/images/room1.jpg" class="card-img-top" alt="<?= htmlspecialchars($type, ENT_QUOTES) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($type, ENT_QUOTES) ?> <span class="badge bg-info text-dark ms-2"><?= (int)($meta['available'] ?? 0) ?> tersedia</span></h5>
                            <?php if (!empty($type_descriptions[$type])): ?>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($type_descriptions[$type], ENT_QUOTES) ?></p>
                            <?php endif; ?>
                            <p class="text-muted small">Tipe ini memiliki <?= (int)($meta['count'] ?? 0) ?> baris di database.</p>

                            <h6>Fasilitas</h6>
                            <ul>
                                <?php
                                $list = $facilities_map[$type] ?? ['Tidak ada data fasilitas untuk tipe ini.'];
                                foreach ($list as $f): ?>
                                    <li><?= htmlspecialchars($f, ENT_QUOTES) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="rooms.php?type=<?= urlencode($type) ?>" class="btn btn-outline-primary">Lihat Kamar</a>
                                    <a href="generate_rooms_demo.php?type=<?= urlencode($type) ?>&base=101&n=5" class="btn btn-sm btn-outline-success">Generate 5 kamar demo</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</main>


<?php include __DIR__ . '/../layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
