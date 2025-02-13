<?php
session_start();
include('koneksi.php');

// Periksa apakah pembelian_id ada
if (!isset($_GET['pembelian_id']) || empty($_GET['pembelian_id']) || !is_numeric($_GET['pembelian_id'])) {
    die("ID Pembelian tidak valid.");
}

$pembelian_id = intval($_GET['pembelian_id']);

// Ambil data pembelian termasuk informasi pelanggan
$sql_pembelian = "SELECT pembelianid, tanggalpembelian, totalharga, nama_pelanggan, alamat, nomor_telepon 
                  FROM pembelian 
                  WHERE pembelianid = $pembelian_id";
$result_pembelian = mysqli_query($conn, $sql_pembelian);
$data_pembelian = mysqli_fetch_assoc($result_pembelian);

// Ambil detail pembelian
$sql_detail = "SELECT d.detailpembelian, d.jumlah, d.subtotal, b.nama_barang, b.harga 
               FROM detailpembelian d
               JOIN barang b ON d.barangid = b.barangid
               WHERE d.pembelianid = $pembelian_id";
$result_detail = mysqli_query($conn, $sql_detail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pembelian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Detail Pembelian</h2>
        <hr>

        <h4>Informasi Pembelian</h4>
        <p><strong>ID Pembelian:</strong> <?php echo htmlspecialchars($data_pembelian['pembelianid']); ?></p>
        <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($data_pembelian['nama_pelanggan']); ?></p>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($data_pembelian['alamat']); ?></p>
        <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($data_pembelian['nomor_telepon']); ?></p>
        <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($data_pembelian['tanggalpembelian']); ?></p>
        <p><strong>Total Harga:</strong> Rp. <?php echo number_format($data_pembelian['totalharga'], 0, ',', '.'); ?></p>

        <h4>Barang yang Dibeli</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result_detail)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                    echo "<td>Rp. " . number_format($row['harga'], 0, ',', '.') . "</td>";
                    echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                    echo "<td>Rp. " . number_format($row['subtotal'], 0, ',', '.') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <a href="admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
</body>
</html>
