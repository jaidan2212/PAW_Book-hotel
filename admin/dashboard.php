<?php
require_once '../db.php';

$tgl_awal  = $_POST['tgl_awal'] ?? "";
$tgl_akhir = $_POST['tgl_akhir'] ?? "";
$label = [];
$jumlah = [];

$sum = 0;
if ($tgl_awal && $tgl_akhir) {
    $query_chart = "
        SELECT DATE(payment_date) AS tanggal, SUM(amount) AS total_harian
        FROM payments
        WHERE DATE(payment_date) BETWEEN '$tgl_awal' AND '$tgl_akhir'
        GROUP BY DATE(payment_date)
        ORDER BY DATE(payment_date) ASC
    ";
    $hasil_chart = $mysqli->query($query_chart);
    while ($row = $hasil_chart->fetch_assoc()) {
        $label[]  = $row['tanggal'];
        $jumlah[] = $row['total_harian'];
    }

    $query_booking = "
        SELECT *
        FROM bookings
        WHERE DATE(created_at) BETWEEN '$tgl_awal' AND '$tgl_akhir'
        ORDER BY created_at ASC
    ";
    $bookings = $mysqli->query($query_booking)->fetch_all(MYSQLI_ASSOC);
    $sum_query = "
        SELECT SUM(amount) AS total 
        FROM payments 
        WHERE DATE(payment_date) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ";
    $sum = $mysqli->query($sum_query)->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/styleAdmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="parent">

    <div class="sidebar"> 
        <h2>HOTEL SITE</h2> 
<ul class="menu">
    <li class="active">Home</li>
    <li class="active dropdown-li">
        Room Management
        <ul class="submenu">
            <li><a href="" class="textstyle"><span >Edit rooms</span></a></li>
            <li><a href="" class="textstyle"><span >Add rooms</span></a></li>
        </ul>
    </li>
    <li class="active dropdown-li">
        Booking Management
        <ul class="submenu">
            <li><a href="" class="textstyle"><span >confirmation payment</span></a></li>
            <li><a href="" class="textstyle"><span >confirmation booking</span></a></li>
        </ul>
    </li>
</ul>

    </div>
    <div class="topbar">
        <div class="top-title">Dashboard</div>

        <div class="top-actions">
            <a href="../index.php" class="textstyle"><span>Buka Situs</span></a>
            <span class="textstyle">Admin</span>
            <a href="../logout.php" class="textstyle"><span >Logout </span></a>
        </div>
    </div>

    <div class="content">
        <main class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Laporan Transaksi</h3>
                <form method="POST">
                <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" style="border-radius: 5px;">
                <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" style="border-radius: 5px;">
                <button type="submit" style="background-color: rgba(34, 155, 28, 0.74); width: 80px; height: 25px; color: white; font-size: 15px; border: none; border-radius: 5px; cursor: pointer;">Tampilkan</button>
                </form>
            </div>

            <?php if ($tgl_awal && $tgl_akhir) { ?>
                <div class="mb-3">Total Pendapatan: <strong>Rp <?= number_format($sum,0,',','.') ?></strong></div>
                <div style="width:800px;">
                    <canvas id="chartPendapatan"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                new Chart(document.getElementById("chartPendapatan"), {
                    type: "bar",
                    data: {
                        labels: <?= json_encode($label) ?>,
                        datasets: [{
                            label: "Pendapatan Harian (Rp)",
                            data: <?= json_encode($jumlah) ?>,
                            borderWidth: 2
                        }]
                    }
                });
                </script>
            <?php } ?>
            <?php if ($tgl_awal && $tgl_akhir) { ?>
            <div class="table-responsive mt-4">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Booking</th>
                        <th>Nama</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach($bookings as $b): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($b['booking_code']) ?></td>
                    <td><?= htmlspecialchars($b['customer_name']) ?></td>
                    <td><?= htmlspecialchars($b['checkin_date']) ?></td>
                    <td><?= htmlspecialchars($b['checkout_date']) ?></td>
                    <td class="text-end">Rp <?= number_format($b['total_amount'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php } ?>
            </div>
        </main>
    </div>
</div>
<script>
    document.querySelectorAll(".dropdown-li").forEach(item => {
        item.addEventListener("click", function() {
            let submenu = this.querySelector(".submenu");
            submenu.classList.toggle("show");
        });
    });
</script>
</body>
</html>
