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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Hotel | Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        body { font-family: 'Montserrat', sans-serif; }
       .hero-section {
            height: 85vh;
            background: url('assets/images/about.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;

        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
        }
        .hero-content {
            position: relative;
            z-index: 10;
        }

    </style>

</head>

<body>


<nav class="navbar navbar-expand-lg position-absolute w-100"
     style="background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(5px); z-index: 999;">
  <div class="container">

  
    <a class="navbar-brand" href="#">
            <img src="assets/images/logo.png" alt="Logo" width="90">
        </a>

        
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">

      <ul class="navbar-nav">

      
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="menuDropdown" role="button" data-bs-toggle="dropdown">
            Menu
          </a>

          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Rooms</a></li>
            <li><a class="dropdown-item" href="#">Contact</a></li>
            <li><a class="dropdown-item" href="#">About Us</a></li>
            <li><a class="dropdown-item" href="#">Login</a></li>
          </ul>
        </li>

      </ul>

    </div>

  </div>
</nav>

<section class="hero-section">
    <div class="hero-overlay"></div>

    <div class="container hero-content text-white">
        <h1 class="display-4 fw-bold">Experience Luxury & Comfort</h1>
        <p class="fs-5 mb-4">Your perfect stay starts here — book your dream vacation now.</p>

        <a href="#rooms" class="btn btn-light btn-lg px-4">Explore Rooms</a>
    </div>
</section>

<div class="container mt-5">
    <div class="card p-4 shadow-lg">
        <form class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Check-in</label>
                <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Check-out</label>
                <input type="date" class="form-control">
            </div>

<div class="col-md-3">
  <label class="form-label text-white">Tamu Dan Kamar</label>

  <div class="dropdown w-100">
    <button class="btn btn-light w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="guestSummary">
      0 Dewasa, 0 Anak, 0 Kamar
    </button>
    <div class="dropdown-menu p-3" style="width: 320px;">
      
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-fill fs-4"></i>
          <span>Dewasa</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('adult', -1)">−</button>
          <span id="adultCount">2</span>
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('adult', 1)">+</button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-emoji-smile fs-4"></i>
          <span>Anak</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('child', -1)">−</button>
          <span id="childCount">0</span>
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('child', 1)">+</button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-door-open fs-4"></i>
          <span>Kamar</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('room', -1)">−</button>
          <span id="roomCount">1</span>
          <button class="btn btn-outline-secondary btn-sm" onclick="updateGuest('room', 1)">+</button>
        </div>
      </div>

      <button class="btn btn-primary w-100" onclick="updateSummary()">Selesai</button>

    </div>
  </div>
</div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-dark w-100">Search</button>
            </div>
        </form>
    </div>
</div>


<section id="about" class="container mt-5 mb-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">About Us</h2>

            <p>
                Solaz Resort, a luxurious haven in Baja California Sur, was crafted by the finest Mexican talent
                to celebrate the striking contrast between the sparkling ocean and arid desert, architecture,
                and breathtaking landscapes.
            </p>

            <p>
                We invite you to discover everything this extraordinary destination has to offer. From relaxation
                to adventure, Solaz Resort is the perfect place to indulge in comfort and luxury.
            </p>
        </div>

        <div class="col-md-6">
            <img src="assets/images/about.jpg" class="img-fluid rounded shadow" alt="">
        </div>
    </div>
</section>



<footer class="bg-dark text-white text-center py-3 mt-5">
    © 2025 Booking Hotel. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let adult = 2;
  let child = 0;
  let room = 1;

  function updateGuest(type, value) {
    if (type === "adult") {
      adult = Math.max(1, adult + value);
      document.getElementById("adultCount").textContent = adult;
    }
    if (type === "child") {
      child = Math.max(0, child + value);
      document.getElementById("childCount").textContent = child;
    }
    if (type === "room") {
      room = Math.max(1, room + value);
      document.getElementById("roomCount").textContent = room;
    }
  }

  function updateSummary() {
    const summary = `${adult} Dewasa, ${child} Anak, ${room} Kamar`;
    document.getElementById("guestSummary").textContent = summary;
  }
</script>

</body>
</html>

