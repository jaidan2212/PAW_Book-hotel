<?php
require_once __DIR__ . "/../../db.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>ID booking tidak ditemukan.</p>";
    exit;
}

$id = (int) $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>Booking tidak ditemukan.</p>";
    exit;
}

$booking = $result->fetch_assoc();

$today    = new DateTime();
$checkout = new DateTime($booking['checkout_date']);

// Hitung keterlambatan lengkap dalam jam
$interval = $checkout->diff($today);

$totalHoursLate = 0;
$denda = 0;
$hargaKamar = (int)$booking['total_amount'];

if ($checkout < $today) {
    // Hitung total jam telat
    $totalHoursLate = ($interval->days * 24) + $interval->h + ($interval->i / 60);

    if ($totalHoursLate > 0) {

        if ($totalHoursLate < 2) {
            // Telat kurang dari 2 jam: denda 20% dari harga kamar
            $denda = $hargaKamar * 0.20;

        } else {
            // Telat lebih dari 2 jam: 100% per 24 jam (dibulatkan ke atas)
            $hariPenuh = ceil($totalHoursLate / 24);
            $denda = $hariPenuh * $hargaKamar;
        }

        // Simpan denda ke database
        $update = $mysqli->prepare("UPDATE bookings SET denda=? WHERE id=?");
        $update->bind_param("ii", $denda, $id);
        $update->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Denda Booking</title>
    <style>
        table { width:100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        h2 { margin-top:20px; }
        .late { color: #6a0dad; font-weight:bold; }
    </style>
</head>
<body>

<h2>Detail Booking dan Denda</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Kode Booking</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>Total</th>
        <th>Denda</th>
        <th>Keterangan</th>
    </tr>

    <tr>
        <td><?= $booking['id']; ?></td>
        <td><?= $booking['booking_code']; ?></td>
        <td><?= $booking['customer_name']; ?></td>
        <td><?= $booking['customer_email']; ?></td>
        <td><?= $booking['checkin_date']; ?></td>
        <td><?= $booking['checkout_date']; ?></td>
        <td>Rp <?= number_format($booking['total_amount'], 0, ',', '.'); ?></td>
        <td class="late">Rp <?= number_format($denda, 0, ',', '.'); ?></td>
        <td>
            <?php
                if ($totalHoursLate <= 0) {
                    echo "Tidak telat";
                } else {
                    echo "Telat " . number_format($totalHoursLate, 2) . " jam";
                }
            ?>
        </td>
    </tr>
</table>

</body>
</html>
