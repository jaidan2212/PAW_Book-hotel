<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$types = [];
try {
    $tres = $mysqli->query("SELECT DISTINCT `type` FROM rooms ORDER BY `type` ASC");
    if ($tres) {
        while ($row = $tres->fetch_assoc()) {
            $types[] = $row['type'];
        }
    }
} catch (mysqli_sql_exception $e) {
}

$featured = [];
try {
    $stmt = $mysqli->prepare("
        SELECT r1.*
        FROM rooms r1
        JOIN (
            SELECT `type`, MIN(price) AS min_price
            FROM rooms
            WHERE status = 'available' AND (stock IS NULL OR stock > 0)
            GROUP BY `type`
        ) r2 ON r1.type = r2.type AND r1.price = r2.min_price
        WHERE r1.status = 'available'
        GROUP BY r1.type
        ORDER BY r1.type
    ");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $featured[] = $r;
        }
    }
} catch (mysqli_sql_exception $e) {
}

$photoPath = "assets/images/default.jpeg";
if (isset($_SESSION['user'])) {
    $uid = (int)$_SESSION['user']['id'];
    $stmt = $mysqli->prepare("SELECT photo FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $ud = $stmt->get_result()->fetch_assoc();
        if (!empty($ud['photo'])) {
            $photoPath = (str_starts_with($ud['photo'], 'http')) ? $ud['photo'] : ("uploads/" . $ud['photo']);
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Solaz Resort | Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/images/logo.png">

  <style>
    body { font-family: 'Montserrat', sans-serif; }
    .hero {
      height: 85vh;
      position: relative;
      overflow: hidden;
      display:flex;
      align-items:center;
      background: black;
      color: white;
    }
    .hero video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0; }
    .hero .overlay { position:absolute; inset:0; background:rgba(0,0,0,0.45); z-index:1; }
    .hero .container { position:relative; z-index:2; }
    .search-card {
      background: rgba(255,255,255,0.95);
      padding: 18px;
      border-radius: 12px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    }
    .search-card .btn-search { height:100%; }
    .card-room img { height:200px; object-fit:cover; }
    .featured .card { border-radius:12px; overflow:hidden; }
    @media (max-width: 767px) {
      .hero { height: 62vh; }
    }

  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg position-absolute w-100" style="backdrop-filter: blur(8px); z-index:999;">
  <div class="container">
    <a class="navbar-brand text-white" href="#">
      <img src="assets/images/logo.png" width="90" alt="Logo">
    </a>

    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="nav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item me-2"><a class="nav-link text-white" href="user/rooms.php">Explore Rooms</a></li>
        <li class="nav-item dropdown mx-2">
          <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Menu</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="user/rooms.php">Rooms</a></li>
            <li><a class="dropdown-item" href="#contact">Contact</a></li>
            <li><a class="dropdown-item" href="#about">About Us</a></li>
            <li><hr class="dropdown-divider"></li>
            <?php if (!isset($_SESSION['user'])): ?>
              <li><a class="dropdown-item" href="login.php">Login</a></li>
            <?php else: ?>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            <?php endif; ?>
          </ul>
        </li>

        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item d-none d-md-block me-3 text-white">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?></li>
          <li class="nav-item">
            <a href="user/account.php"><img src="<?= htmlspecialchars($photoPath) ?>" width="42" height="42" style="object-fit:cover;border-radius:50%;border:2px solid white;"></a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<section class="hero">
  <video autoplay muted loop playsinline>
    <source src="assets/video/overlay.mp4" type="video/mp4">
  </video>
  <div class="overlay"></div>

  <div class="container text-white">
    <div class="row align-items-center">
    <div class="col-lg-7">
  <h1 class="display-5 fw-bold">Experience Luxury & Comfort</h1>
  <p class="lead">Your perfect stay starts here — book your dream vacation now.</p>

  <button 
    class="btn btn-outline-light px-4 py-2 rounded-pill mt-3"
    style="backdrop-filter: blur(6px); border:1.5px solid rgba(255,255,255,0.5); font-weight:600;"
    data-bs-toggle="modal"
    data-bs-target="#searchModal">
    <i class="bi bi-search me-2"></i> Cari Kamar
  </button>
</div>

</section>

<section class="py-5 featured">
  <div class="container">
    <h2 class="mb-4 text-center">Featured Rooms</h2>

    <div class="row g-4">
      <?php if (!empty($featured)): ?>
        <?php foreach ($featured as $f): 
            $img = "../assets/images/default.jpeg";
            if (!empty($f['photo']) && str_starts_with($f['photo'], 'http')) {
                $img = $f['photo'];
            } elseif (!empty($f['image'])) {
                $img = "../uploads/" . $f['image'];
            } else {
                $type = $f['type'] ?? '';
                if ($type === 'Single') $img = "assets/images/room1.jpg";
                elseif ($type === 'Double') $img = "assets/images/room2.jpeg";
                elseif ($type === 'Suite')  $img = "assets/images/room3.jpeg";
            }
        ?>
          <div class="col-md-4">
            <div class="card h-100">
              <img src="<?= htmlspecialchars($img) ?>" class="card-img-top card-room" alt="<?= htmlspecialchars($f['type']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($f['type']) ?> Room</h5>
                <p class="card-text text-muted small">
                  Rp <?= number_format((float)$f['price'],0,',','.') ?> / night
                </p>
                <a href="user/room_list.php?type=<?= urlencode($f['type']) ?>" class="btn btn-primary w-100">View Room</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php
          $static = [
            ['type'=>'Single','img'=>'assets/images/room1.jpg','price'=>180000],
            ['type'=>'Double','img'=>'assets/images/room2.jpeg','price'=>350000],
            ['type'=>'Suite','img'=>'assets/images/room3.jpeg','price'=>700000],
          ];
        ?>
        <?php foreach ($static as $s): ?>
          <div class="col-md-4">
            <div class="card h-100">
              <img src="<?= $s['img'] ?>" class="card-img-top card-room" alt="<?= htmlspecialchars($s['type']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($s['type']) ?> Room</h5>
                <p class="card-text text-muted small">
                  Rp <?= number_format($s['price'],0,',','.') ?> / night
                </p>
                <a href="user/room_list.php?type=<?= urlencode($s['type']) ?>" class="btn btn-primary w-100">View Room</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section id="about" class="py-5" style="background: #f5f5f5;">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="fw-bold mb-3">About Us</h2>

                <p>
                    Solaz Resort, sebuah resort mewah di Bali,Indonesia. dirancang oleh para ahli terbaik
                    untuk merayakan kontras yang memukau antara lautan yang berkilauan,arsitektur, dan lanskap yang menakjubkan.
                </p>

                <p>
                    Kami mengundang Anda untuk menjelajahi semua yang ditawarkan destinasi luar biasa ini.
                    Dari relaksasi hingga petualangan, Solaz Resort adalah tempat yang sempurna untuk menikmati
                    kenyamanan dan kemewahan.
                </p>
            </div>

            <div class="col-md-6">
                <img src="assets/images/about.jpg" class="img-fluid rounded shadow" alt="">
            </div>

        </div>
    </div>
</section>


<section class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="fw-bold mb-4">Why Choose Our Hotel?</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <i class="bi bi-stars fs-1 text-warning"></i>
        <h5 class="fw-bold mt-2">World-Class Service</h5>
        <p class="text-muted">Pelayanan profesional 24/7.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-building fs-1 text-primary"></i>
        <h5 class="fw-bold mt-2">Luxury Facilities</h5>
        <p class="text-muted">Spa, gym, lounge, dan lain-lain.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-shield-check fs-1 text-success"></i>
        <h5 class="fw-bold mt-2">Secure & Comfortable</h5>
        <p class="text-muted">Privasi dan keamanan terjamin.</p>
      </div>
    </div>
  </div>
</section>

<section class="py-5" style="background: #f7f6f3;">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">Kebijakan Akomodasi</h2>

        <div class="accordion" id="policyAccordion">

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#policy1">
                        <i class="bi bi-door-open me-2"></i> Check-in & Check-out
                    </button>
                </h2>
                <div id="policy1" class="accordion-collapse collapse show" data-bs-parent="#policyAccordion">
                    <div class="accordion-body">
                        • Check-in: 14.00 <br>
                        • Check-out: 12.00 <br>
                        • Early check-in sesuai ketersediaan.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#policy2">
                        <i class="bi bi-people me-2"></i> Kebijakan Tamu & Anak
                    </button>
                </h2>
                <div id="policy2" class="accordion-collapse collapse" data-bs-parent="#policyAccordion">
                    <div class="accordion-body">
                        • Semua usia anak diperbolehkan. <br>
                        • Anak usia 12 tahun ke atas dihitung sebagai dewasa. <br>
                        • Extra bed tersedia sesuai permintaan.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#policy3">
                        <i class="bi bi-cash-coin me-2"></i> Pembayaran
                    </button>
                </h2>
                <div id="policy3" class="accordion-collapse collapse" data-bs-parent="#policyAccordion">
                    <div class="accordion-body">
                        • Dapat membayar menggunakan kartu kredit, debit, transfer. <br>
                        • Deposit mungkin diperlukan saat check-in. <br>
                        • Pembatalan mengikuti kebijakan masing-masing tipe kamar.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#policy4">
                        <i class="bi bi-ban me-2"></i> Larangan
                    </button>
                </h2>
                <div id="policy4" class="accordion-collapse collapse" data-bs-parent="#policyAccordion">
                    <div class="accordion-body">
                        • Dilarang merokok di area kamar. <br>
                        • Hewan peliharaan tidak diperbolehkan. <br>
                        • Dilarang membawa barang berbahaya.
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<footer class="bg-dark text-white text-center py-3 mt-4">
  <div class="container text-center">
    <h4 class="fw-bold mb-2">Contact & Reservation</h4>
    <p class="mb-1"><i class="bi bi-telephone me-2"></i>+62 852-3326-7990</p>
    <p class="mb-1"><i class="bi bi-envelope me-2"></i>booking@solazresort.com</p>
    <p><i class="bi bi-geo-alt me-2"></i>Bali, Indonesia</p>
  </div>
  © 2025 Booking Hotel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
</script>
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:18px; overflow:hidden; border:0;">

      <div class="modal-header text-white" 
           style="background: linear-gradient(135deg,#0d2d56,#103c6b);">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-funnel me-2"></i> Cari & Filter Kamar
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form action="user/rooms.php" method="GET" id="modalSearchForm">

      <div class="modal-body" style="background:#f7f8fa;">
        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label fw-semibold">Check In</label>
            <input type="date" name="checkin" class="form-control shadow-sm" 
                   style="border-radius:10px;" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Check Out</label>
            <input type="date" name="checkout" class="form-control shadow-sm" 
                   style="border-radius:10px;" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Dewasa</label>
            <select name="dewasa" class="form-select shadow-sm" style="border-radius:10px;">
              <option value="1" selected>1 Dewasa</option>
              <option value="2">2 Dewasa</option>
              <option value="3">3 Dewasa</option>
              <option value="4">4 Dewasa</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Anak</label>
            <select name="anak" class="form-select shadow-sm" style="border-radius:10px;">
              <option value="0" selected>0 Anak</option>
              <option value="1">1 Anak</option>
              <option value="2">2 Anak</option>
              <option value="3">3 Anak</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Jumlah Kamar</label>
            <select name="room" class="form-select shadow-sm" style="border-radius:10px;">
              <option value="1" selected>1 Kamar</option>
              <option value="2">2 Kamar</option>
              <option value="3">3 Kamar</option>
            </select>
          </div>

        </div>
      </div>

      <div class="modal-footer" style="background:#f7f8fa;">
        <button type="submit" 
                class="btn w-100 fw-bold text-white py-2"
                style="background:#103c6b; border-radius:12px; font-size:1rem;">
          <i class="bi bi-search me-2"></i> Temukan Kamar
        </button>
      </div>

      </form>

    </div>
  </div>
</div>

</body>
</html>
