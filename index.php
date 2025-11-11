<?php 
require_once 'functions.php'; 
$rooms = getRooms(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Hotel | Our Rooms</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<header>
    <div class="navbar">
        <div class="logo">Booking Hotel</div>
        <nav>
            <a href="#">Rooms</a>
            <a href="#">Facilities</a>
            <a href="#">Contact us</a>
            <a href="#">About</a>
        </nav>
        <div class="auth">
            <button class="btn login">Login</button>
            <button class="btn register">Register</button>
        </div>
    </div>
</header>

<!-- ===== MAIN SECTION ===== -->
<section class="rooms-section">
    <h1>OUR ROOMS</h1>
    <div class="rooms-container">
        <?php $i = 1; foreach ($rooms as $r): ?>
            <div class="room-card">
                <img src="assets/images/<?= $r['image'] ?? 'Single Bad.jpg' ?>" alt="<?= $r['type'] ?>">
                <div class="room-info">
                    <h2><?= htmlspecialchars($r['type']) ?></h2>
                    <p class="price">Rp <?= number_format($r['price'], 0, ',', '.') ?> per night</p>

                    <h3>Features</h3>
                    <div class="tags">
                        <span class="tag">bedroom</span>
                        <span class="tag">balcony</span>
                        <span class="tag">kitchen</span>
                    </div>

                    <h3>Facilities</h3>
                    <div class="tags">
                        <span class="tag">Wifi</span>
                        <span class="tag">Air conditioner</span>
                        <span class="tag">Room Heater</span>
                        <span class="tag">Geyser</span>
                    </div>

                    <a href="book.php?room_id=<?= $r['id'] ?>" class="btn-book">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
    <p>© 2025 Booking Hotel</p>
</footer>

</body>
</html>
