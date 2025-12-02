<?php
require_once __DIR__ . "/../../db.php";

$result = $mysqli->query("SELECT * FROM rooms ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <h2>Data Kamar</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nomor</th>
                    <th>Tipe</th>
                    <th>Harga</th>
                    <th>Dewasa</th>
                    <th>Anak</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($r = $result->fetch_assoc()): ?> <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['room_number'] ?></td>
                        <td><?= $r['type'] ?></td>
                        <td>Rp <?= number_format($r['price'], 0, ',', '.') ?></td>
                        <td><?= $r['capacity_adult'] ?></td>
                        <td><?= $r['capacity_child'] ?></td>
                        <td><?= $r['stock'] ?></td>
                        <td><?= $r['status'] ?></td>
                        <td>
                            <a href="pages/room_edit.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="pages/delete_room.php?id=<?= $r['id'] ?>"
                                onclick="return confirm('Yakin ingin menghapus?')"
                                class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</body>

</html>