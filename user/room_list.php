<?php
require_once "db.php";

if (!isset($_GET['type']) || empty($_GET['type'])) {
    die("Invalid room type.");
}

$type = mysqli_real_escape_string($conn, $_GET['type']);

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$sort = isset($_GET['sort']) ? $_GET['sort'] : "asc";
$orderBy = ($sort === "desc") ? "DESC" : "ASC";

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
</head>
<body style="background:#f5f5f5">

<div class="container py-5">

    <h2 class="text-center mb-4"><?php echo $type; ?> Rooms</h2>

    <form method="GET" class="row mb-4">
        <input type="hidden" name="type" value="<?php echo $type; ?>">

        <div class="col-md-4">
            <input type="text" name="search" class="form-control" 
                   placeholder="Cari room number atau deskripsi..."
                   value="<?php echo $search; ?>">
        </div>

        <div class="col-md-4">
            <select name="sort" class="form-control">
                <option value="asc"  <?php echo ($sort === "asc") ? "selected" : ""; ?>>Harga Termurah</option>
                <option value="desc" <?php echo ($sort === "desc") ? "selected" : ""; ?>>Harga Termahal</option>
            </select>
        </div>

        <div class="col-md-4">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="row g-4">

        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while ($room = mysqli_fetch_assoc($query)): ?>

                <?php
                switch ($room['type']) {
                    case 'Single':  $img = "room1.jpeg"; break;
                    case 'Double':  $img = "room2.jpeg"; break;
                    case 'Suite':   $img = "room3.jpeg"; break;
                    default:        $img = "default.jpg"; break;
                }

                $imagePath = "assets/images/" . $img;

                $badgeClass = ($room['status'] === "available") ? "bg-success" : "bg-danger";
                ?>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">

                        <img src="<?php echo $imagePath; ?>" 
                             class="card-img-top" 
                             style="height:220px; object-fit:cover;"
                             alt="Room Image">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo $room['type']; ?> Room</h5>

                            <p class="card-text">
                                <strong>Room Number:</strong> <?php echo $room['room_number']; ?><br>

                                <strong>Price:</strong>
                                Rp <?php echo number_format($room['price']); ?><br>

                                <strong>Status:</strong>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $room['status']; ?>
                                </span><br>

                                <strong>Stock:</strong> <?php echo $room['stock']; ?><br>
                            </p>

                            <a href="booking.php?id=<?php echo $room['id']; ?>" 
                               class="btn btn-success w-100"
                               <?php echo ($room['status'] !== 'available') ? 'disabled' : ''; ?>>
                                <?php echo ($room['status'] === 'available') ? "Book Now" : "Not Available"; ?>
                            </a>
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
</div>

</body>
</html>
