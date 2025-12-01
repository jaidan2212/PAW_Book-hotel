<?php
require_once __DIR__ . "/../../db.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$sql = "
    SELECT 
        id,
        booking_code,
        customer_name,
        customer_email,
        checkin_date,
        checkout_date,
        total_amount,
        denda,
        status
    FROM bookings
    WHERE status = 'cancelled'
    ORDER BY id DESC
";

$data = $mysqli->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cancelled Bookings</title>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        h2 { margin-top:20px; }
        .cancelled { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>Daftar Booking Status Cancelled</h2>

<?php if ($data->num_rows == 0): ?>
    <p style="color:gray;">Belum ada booking dengan status cancelled.</p>
<?php else: ?>

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
        <th>Status</th>
    </tr>

    <?php while ($row = $data->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= $row['booking_code']; ?></td>
        <td><?= $row['customer_name']; ?></td>
        <td><?= $row['customer_email']; ?></td>
        <td><?= $row['checkin_date']; ?></td>
        <td><?= $row['checkout_date']; ?></td>
        <td>Rp <?= number_format($row['total_amount'],0,',','.'); ?></td>
        <td>Rp <?= number_format($row['denda'],0,',','.'); ?></td>
        <td class="cancelled"><?= $row['status']; ?></td>
    </tr>
    <?php endwhile; ?>

</table>

<?php endif; ?>

</body>
</html>
