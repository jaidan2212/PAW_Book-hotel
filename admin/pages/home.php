<?php
require_once '../db.php';

// Ambil data ringkasan
$totalIncome  = $mysqli->query("SELECT SUM(amount) AS total FROM payments")->fetch_assoc()['total'] ?? 0;
$totalBooking = $mysqli->query("SELECT COUNT(*) AS jml FROM bookings")->fetch_assoc()['jml'] ?? 0;
$pendingBooking = $mysqli->query("SELECT COUNT(*) AS pending FROM bookings WHERE status='pending'")->fetch_assoc()['pending'] ?? 0;

// Ambil data grafik pendapatan
$dataChart = $mysqli->query("
    SELECT DATE(payment_date) AS tanggal, SUM(amount) AS total_harian
    FROM payments
    GROUP BY DATE(payment_date)
    ORDER BY DATE(payment_date) ASC
")->fetch_all(MYSQLI_ASSOC);

$label = array_column($dataChart, 'tanggal');
$jumlah = array_column($dataChart, 'total_harian');
?>

<main class="container py-4">
    
    <h3 class="mb-3">Ringkasan Sistem</h3>
    
    <form method="POST" class="d-flex" style="gap: 10px;">
        <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control">
        <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control">
        <button type="submit" class="btn btn-success btn-sm">Filter</button>
    </form>
    <?php echo"<br>"; if ($tgl_awal && $tgl_akhir) { ?>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="p-3 rounded bg-primary text-white">
                <h5>Total Pendapatan</h5>
                <h3>Rp <?= number_format($totalIncome,0,',','.') ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded bg-success text-white">
                <h5>Total Booking</h5>
                <h3><?= $totalBooking ?></h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 rounded bg-warning text-dark">
                <h5>Menunggu Konfirmasi</h5>
                <h3><?= $pendingBooking ?></h3>
            </div>
        </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
    <h4>Grafik Pendapatan</h4>

</div>

<div>
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
                    borderWidth: 2,
                    fill: true
                }]
            }
        });
    </script>
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
</main>
