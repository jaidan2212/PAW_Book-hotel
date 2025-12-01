<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(isset($_POST['payment_id'], $_POST['action'])) {

        $payment_id = (int) $_POST['payment_id'];
        $action     = $_POST['action'];

        if (!in_array($action, ['approved', 'rejected'])) {
            die("Status tidak valid!");
        }

        $stmt = $mysqli->prepare("UPDATE payments SET payment_status=? WHERE id=?");
        $stmt->bind_param("si", $action, $payment_id);
        $stmt->execute();
    }
}

$q = "
    SELECT id, booking_id, amount, method, note, payment_date
    FROM payments 
    WHERE payment_status='pending'
    ORDER BY payment_date ASC
";
$res = $mysqli->query($q);
?>

<style>
.payment-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-family: Arial, sans-serif;
    background: white;
}

.payment-table th,
.payment-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.payment-table th {
    background: #f4f4f4;
    font-weight: bold;
}

.payment-table tr:hover {
    background: #fafafa;
}

.pay-btn {
    padding: 6px 12px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    font-size: 13px;
}

.pay-approve {
    background: green;
    color: white;
}

.pay-reject {
    background: red;
    color: white;
}
</style>
<h2>Konfirmasi Pembayaran</h2>

<?php if ($res->num_rows == 0): ?>
    <p style="color:gray;">Tidak ada data pembayaran pending.</p>
<?php else: ?>

<table class="payment-table">
    <tr>
        <th>Booking ID</th>
        <th>Jumlah</th>
        <th>Metode</th>
        <th>Catatan</th>
        <th>Tanggal</th>
        <th>Aksi</th>
    </tr>

    <?php while($p = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $p['booking_id'] ?></td>
            <td>Rp <?= number_format($p['amount'],0,',','.') ?></td>
            <td><?= $p['method'] ?></td>
            <td><?= $p['note'] ?></td>
            <td><?= $p['payment_date'] ?></td>

            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="action" value="approved">
                    <button class="pay-btn pay-approve" type="submit">Approve</button>
                </form>

                <form method="POST" style="display:inline;">
                    <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="action" value="rejected">
                    <button class="pay-btn pay-reject" type="submit"
                        onclick="return confirm('Tolak pembayaran ini?');">
                        Reject
                    </button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php endif; ?>
