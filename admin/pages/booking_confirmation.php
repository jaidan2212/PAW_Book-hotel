<?php
require_once __DIR__ . "/../../db.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['ID'], $_POST['action'])) {

        $id     = (int) $_POST['ID'];
        $action = $_POST['action'];

        if ($action === "approve") {
            $stmt = $mysqli->prepare("UPDATE bookings SET status='paid' WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
        elseif ($action === "reject") {
            $stmt = $mysqli->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

$sql = "
    SELECT 
        b.id,
        b.booking_code,
        b.customer_name,
        b.customer_email,
        b.checkin_date,
        b.checkout_date,
        b.total_amount,
        b.status,
        b.denda,
        p.amount AS payment_amount,
        p.payment_status
    FROM bookings b
    LEFT JOIN payments p 
        ON p.booking_id = b.id 
        AND p.payment_status = 'approved'
    WHERE b.status = 'pending'
    ORDER BY b.id DESC
";

$data = $mysqli->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>

    <style>
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        .btn { padding: 6px 12px; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .approve { background: #008000; }
        .reject { background: #b30000; }
        .late { background: #6a0dad; }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-buttons form,
        .action-buttons a {
            display: inline-block;
        }
    </style>
</head>
<body>

<h2>Booking Dengan Pembayaran Approved</h2>

<?php if ($data->num_rows == 0): ?>
    <p style="color:gray;">Belum ada booking dengan status pembayaran approved.</p>
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
        <th>Aksi</th>
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
        <td><?= $row['status']; ?></td>

        <td>
            <div class="action-buttons">

                <!-- APPROVE -->
                <form method="POST">
                    <input type="hidden" name="ID" value="<?= $row['id']; ?>">
                    <input type="hidden" name="action" value="approve">
                    <button class="btn approve" type="submit">Approve</button>
                </form>

                <!-- REJECT -->
                <form method="POST" onsubmit="return confirm('Tolak booking ini?');">
                    <input type="hidden" name="ID" value="<?= $row['id']; ?>">
                    <input type="hidden" name="action" value="reject">
                    <button class="btn reject" type="submit">Reject</button>
                </form>

                <!-- TELAT / DENDA -->
                <a href="dashboard.php?page=denda&id=<?= $row['id']; ?>" class="btn late">Telat</a>
            </div>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

<?php endif; ?>

</body>
</html>
