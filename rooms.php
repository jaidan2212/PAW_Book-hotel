<?php 
session_start();
require_once 'functions.php'; 
$rooms = getRooms(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Hotel | Rooms</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo">Booking Hotel</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="rooms.php" class="active">Rooms</a>
            <a href="facilities.php">Facilities</a>
            <a href="contact.php">Contact Us</a>
            <a href="about.php">About</a>
        </nav>
    </div>
</header>

<section class="rooms-section">
    <h1>OUR ROOMS</h1>
    <div class="rooms-container">
        <?php foreach ($rooms as $r): ?>
            <div class="room-card">
                <img src="assets/images/room1.jpg" alt="Room 1">
                <div class="room-info">
                    <h2><?= htmlspecialchars($r['type']) ?></h2>
                    <p class="price">Rp <?= number_format($r['price'], 0, ',', '.') ?> / night</p>

                    <a href="book.php?room_id=<?= $r['id'] ?>" class="btn-book">Book Now</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer>
    <p>© 2025 Booking Hotel</p>
</footer>

</body>
</html>