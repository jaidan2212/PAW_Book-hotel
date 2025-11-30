<?php
require_once 'db.php';
require_once 'user/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rooms = getRooms();

$hasStock = false;
try {
    $chk = $mysqli->query("SHOW COLUMNS FROM rooms LIKE 'stock'");
    if ($chk && $chk->num_rows > 0) $hasStock = true;
} catch (mysqli_sql_exception $e) {
    $hasStock = false;
}

$photoPath = "assets/default.png";

if (isset($_SESSION['user'])) {
    $uid = $_SESSION['user']['id'];
    $stmt = $mysqli->prepare("SELECT photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if (!empty($userData['photo'])) {

        if (str_starts_with($userData['photo'], "http")) {
            $photoPath = $userData['photo'];
        } else {
            $photoPath = "uploads/" . $userData['photo'];
        }

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solaz Resort | Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .object-fit-cover { object-fit: cover; }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg position-absolute w-100"
     style="
        backdrop-filter: blur(12px);
        background: rgba(0, 0, 0, 0.35);
        border-bottom: 1px solid rgba(255,255,255,0.15);
        z-index:999;
        transition: transform 0.35s ease;
     ">
  <div class="container">

    <a class="navbar-brand" href="#">
        <img src="assets/images/logo.png" alt="Logo" width="90" class="me-2">
    </a>

    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">

      <ul class="navbar-nav align-items-center">

 
        <li class="nav-item dropdown mx-2">
          <a class="nav-link dropdown-toggle text-white fw-semibold"
             href="#" id="menuDropdown" role="button" data-bs-toggle="dropdown">
            Menu
          </a>

          <ul class="dropdown-menu dropdown-menu-end shadow-sm"
              style="backdrop-filter:blur(10px); background:rgba(255,255,255,0.95); border-radius:10px;">
            <li><a class="dropdown-item" href="user/cek_room.php">Rooms</a></li>
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

            <li class="nav-item text-white fw-semibold d-none d-md-block">
                Hi, <?= htmlspecialchars($_SESSION['user']['name']); ?>
            </li>

            <li class="nav-item ms-3">
                <a href="user/account.php">
                    <img src="<?= htmlspecialchars($photoPath) ?>"
                         width="42" height="42"
                         style="object-fit:cover;border-radius:50%;border:2px solid white;box-shadow:0 0 8px rgba(255,255,255,0.4);">
                </a>
            </li>

            <li class="nav-item ms-3 d-md-none mt-2">
                <a href="user/account.php" class="btn btn-outline-light btn-sm">My Account</a>
            </li>

        <?php else: ?>

            <li class="nav-item ms-3">
                <a href="login.php" class="btn btn-outline-light btn-sm px-3">Login</a>
            </li>

        <?php endif; ?>

      </ul>

    </div>
  </div>
</nav>


<section class="position-relative" style="height: 85vh; overflow:hidden;">

    <video autoplay muted loop playsinline 
           class="position-absolute w-100 h-100 top-50 start-50 translate-middle object-fit-cover">
        <source src="assets/video/overlay.mp4" type="video/mp4">
    </video>

    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

    <div class="container position-relative text-white d-flex flex-column justify-content-center h-100">
        <h1 class="display-4 fw-bold">Experience Luxury & Comfort</h1>
        <p class="fs-5 mb-4">Your perfect stay starts here — book your dream vacation now.</p>

        <div class="d-flex">
            <a href="user/rooms.php" class="btn btn-light px-4 py-2">Explore Rooms</a>


            
            <a href="#" class="btn btn-outline-light btn-lg ms-3" data-bs-toggle="modal" data-bs-target="#searchModal">
                <i class="bi bi-search"></i> Search Rooms
            </a>
        </div>
    </div>

</section>

<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-funnel me-2"></i> Filter Pencarian Kamar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="user/rooms.php" method="GET" id="modalSearchForm">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Check In</label>
            <input type="date" name="checkin" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Check Out</label>
            <input type="date" name="checkout" class="form-control" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Dewasa</label>
            <select name="dewasa" class="form-select">
              <option value="1" selected>1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Anak</label>
            <select name="anak" class="form-select">
              <option value="0" selected>0</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Rooms</label>
            <select name="room" class="form-select">
              <option value="1" selected>1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary w-100 fw-bold">
            <i class="bi bi-search"></i> Cari Kamar
        </button>
      </div>

      </form>
    </div>
  </div>
</div>
<section class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Featured Rooms</h2>

        <div class="row g-4">

            
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/images/room1.jpg" class="card-img-top" alt="Single Room">
                    <div class="card-body">
                        <h5 class="card-title">Single Room</h5>
                        <p class="card-text">Kamar nyaman untuk 1 orang.</p>
                        <a href="user/room_list.php?type=Single" class="btn btn-primary w-100">View Room</a>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/images/room1.jpg" class="card-img-top" alt="Double Room">
                    <div class="card-body">
                        <h5 class="card-title">Double Room</h5>
                        <p class="card-text">Kamar luas untuk 2 orang.</p>
                        <a href="user/room_list.php?type=Double" class="btn btn-primary w-100">View Room</a>
                    </div>
                </div>
            </div>

           
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="assets/images/room1.jpg" class="card-img-top" alt="Suite Room">
                    <div class="card-body">
                        <h5 class="card-title">Suite Room</h5>
                        <p class="card-text">Kamar paling mewah dan premium.</p>
                        <a href="user/room_list.php?type=Suite" class="btn btn-primary w-100">View Room</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section id="about" class="py-5" style="background: #f5f5f5;">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="fw-bold mb-3">About Us</h2>

                <p>
                    Solaz Resort, sebuah resort mewah di Bali, Indonesia. dirancang oleh para ahli terbaik
                    Meksiko untuk merayakan kontras yang memukau antara lautan yang berkilauan dan
                    gurun yang gersang, arsitektur, dan lanskap yang menakjubkan.
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
                <p class="text-muted">
                    Pelayanan profesional 24/7 layaknya hotel bintang 5 internasional.
                </p>
            </div>

            <div class="col-md-4">
                <i class="bi bi-building fs-1 text-primary"></i>
                <h5 class="fw-bold mt-2">Luxury Facilities</h5>
                <p class="text-muted">
                    Nikmati fasilitas mewah seperti spa, lounge, gym, dan private pool.
                </p>
            </div>

            <div class="col-md-4">
                <i class="bi bi-shield-check fs-1 text-success"></i>
                <h5 class="fw-bold mt-2">Secure & Comfortable</h5>
                <p class="text-muted">
                    Keamanan modern, privasi terjaga, kenyamanan maksimal.
                </p>
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

<section id="contact" class="py-4 bg-dark text-white">
    <div class="container text-center">
        <h4 class="fw-bold mb-2">Contact & Reservation</h4>
        <p class="mb-1"><i class="bi bi-telephone me-2"></i>+62 852-3326-7990</p>
        <p class="mb-1"><i class="bi bi-envelope me-2"></i>booking@solazresort.com</p>
        <p><i class="bi bi-geo-alt me-2"></i>Bali, Indonesia</p>
    </div>
</section>


<footer class="bg-dark text-white text-center py-3 mt-5">
    © 2025 Booking Hotel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateGuest(event, type, value) {
    event.preventDefault();
    event.stopPropagation();

    if (type === "dewasa") {
        dewasa = Math.max(1, dewasa + value);
        document.getElementById("dewasaCount").textContent = dewasa;
    }

    if (type === "anak") {
        anak = Math.max(0, anak + value);
        document.getElementById("anakCount").textContent = anak;
    }

    if (type === "room") {
        room = Math.max(1, room + value);
        document.getElementById("roomCount").textContent = room;
    }

    updateSummary();
}

function openSearchModalOrRedirect() {
    const checkout = document.getElementById('search_checkout').value;

    if (checkin && checkout) {

        const modal = new bootstrap.Modal(document.getElementById('searchModal'));
        document.querySelector('#searchModal input[name="checkin"]').value = checkin;
        document.querySelector('#searchModal input[name="checkout"]').value = checkout;
        document.querySelector('#searchModal select[name="dewasa"]').value = dewasa;
        document.querySelector('#searchModal select[name="anak"]').value = anak;
        document.querySelector('#searchModal select[name="room"]').value = room;
        modal.show();
        return;
    }

    window.location.href = `user/rooms.php?dewasa=${dewasa}&anak=${anak}&room=${room}`;
}

let lastScroll = 0;
const navbar = document.querySelector("nav");

window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
        navbar.style.transform = "translateY(0)";
        return;
    }

    if (currentScroll > lastScroll) {
        navbar.style.transform = "translateY(-100%)";
    } else {
        navbar.style.transform = "translateY(0)";
    }

    lastScroll = currentScroll;
});
</script>

</body>
</html>
