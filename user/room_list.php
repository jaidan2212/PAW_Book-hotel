<?php
require_once "../db.php";

if (!isset($_GET['type']) || empty($_GET['type'])) {
    die("Invalid room type.");
}

$type   = mysqli_real_escape_string($conn, $_GET['type']);
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$sort     = $_GET['sort'] ?? "asc";
$orderBy  = ($sort === "desc") ? "DESC" : "ASC";

$sql = "
    SELECT *
    FROM rooms
    WHERE type = '$type'
    AND (room_number LIKE '%$search%' OR description LIKE '%$search%')
    ORDER BY price $orderBy
";

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $type; ?> Rooms</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f2f4f7;
        }
        .room-card img {
            height: 220px;
            object-fit: cover;
        }
        .room-card {
            border-radius: 15px;
            overflow: hidden;
            transition: .3s;
        }
        .room-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 22px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

<div class="container py-5">

    <h2 class="text-center fw-bold mb-4"><?= $type ?> Rooms</h2>

    <!-- FILTER FORM -->
    <form method="GET" class="row mb-4 gy-2">
        <input type="hidden" name="type" value="<?= $type ?>">

        <div class="col-md-4">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Cari room number atau deskripsi..."
                value="<?= $search ?>"
            >
        </div>

        <div class="col-md-4">
            <select name="sort" class="form-select">
                <option value="asc"  <?= $sort === "asc" ? "selected" : "" ?>>Harga Termurah</option>
                <option value="desc" <?= $sort === "desc" ? "selected" : "" ?>>Harga Termahal</option>
            </select>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- ROOM LIST -->
    <div class="row g-4">

        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while ($room = mysqli_fetch_assoc($query)): ?>

                <?php
                // Pilih gambar berdasarkan type
                switch ($room['type']) {
                    case 'Single': $img = "room1.jpg"; break;
                    case 'Double': $img = "room2.jpeg"; break;
                    case 'Suite':  $img = "room3.jpeg"; break;
                    default:       $img = "default.jpeg"; break;
                }

                $imagePath = "../assets/images/" . $img;

                // Badge warna status
                $badgeClass = ($room['status'] === 'available') 
                    ? "bg-success" 
                    : "bg-danger";
                ?>

                <div class="col-md-4">
                    <div class="card h-100 room-card shadow-sm">

                        <img src="<?= $imagePath ?>" alt="Room Image" class="card-img-top">

                        <div class="card-body">
                            <h5 class="card-title"><?= $room['type']; ?> Room</h5>

                            <p class="card-text small text-muted">
                                <strong>Room Number:</strong> <?= $room['room_number']; ?><br>
                                <strong>Price:</strong> Rp <?= number_format($room['price']); ?><br>
                                <strong>Status:</strong> 
                                <span class="badge <?= $badgeClass ?>">
                                    <?= $room['status']; ?>
                                </span><br>
                                <strong>Stock:</strong> <?= $room['stock']; ?><br>
                            </p>

                            <a href="booking.php?id=<?= $room['id'] ?>"
                                class="btn btn-success w-100 mt-2"
                                <?= ($room['status'] !== 'available') ? 'disabled' : '' ?>>
                                <?= ($room['status'] === 'available') ? "Book Now" : "Not Available"; ?>
                            </a>
                        </div>

                    </div>
                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12 text-center mt-5">
                <h5 class="text-muted">Tidak ada kamar ditemukan.</h5>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
