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
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/styleAdmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="parent">

    <!-- SIDEBAR -->
    <div class="sidebar"> 
        <h2>HOTEL SITE</h2> 
<ul class="menu">
    <li><a href="dashboard.php?page=home.php" class="textstyle">Home</a></li>
    
    <li class="dropdown-li">
        Room Management
        <ul class="submenu">
            <li><a href="dashboard.php?page=rooms_edit" class="textstyle">Edit Rooms</a></li>
            <li><a href="dashboard.php?page=rooms_add" class="textstyle">Add Rooms</a></li>
        </ul>
    </li>

    <li class="dropdown-li">
        Booking Management
        <ul class="submenu">
            <li><a href="dashboard.php?page=payment_confirmation" class="textstyle">Confirmation Payment</a></li>
            <li><a href="dashboard.php?page=booking_confirmation" class="textstyle">Confirmation Booking</a></li>
        </ul>
    </li>
</ul>

    </div>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="top-title">Dashboard</div>

        <div class="top-actions">
            <a href="../index.php" class="textstyle">Buka Situs</a>
            <span class="textstyle">Admin</span>
            <a href="../logout.php" class="textstyle">Logout</a>
        </div>
    </div>

    <!-- CONTENT -->
 <div class="content">
    <?php
        $page = $_GET['page'] ?? 'home';

        switch($page){
            case 'rooms_add':
                include 'pages/room_add.php';
                break;

            case 'rooms_edit':
                include 'pages/room_edit.php';
                break;

            case 'booking_confirmation':
                include 'pages/booking_confirmation.php';
                break;

            case 'payment_confirmation':
                include 'pages/payment_confirmation.php';
                break;

            default:
                include 'pages/home.php'; 
                break;
        }
    ?>
</div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById("chartPendapatan"), {
    type: "bar",
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: "Pendapatan (Rp)",
            data: <?= json_encode($values) ?>,
            borderWidth: 2
        }]
    }
});
</script>

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
