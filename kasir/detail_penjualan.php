<?php
session_start();
include('config.php'); // Pastikan koneksi benar

// Ambil data penjualan terbaru
$sql_penjualan = "SELECT p.penjualanid, p.tanggalpenjualan, p.totalharga, 
                         pl.namapelanggan, pl.alamat, pl.nomortelepon 
                  FROM penjualan p
                  JOIN pelanggan pl ON p.pelangganid = pl.pelangganid
                  ORDER BY p.penjualanid DESC LIMIT 1";
$result_penjualan = mysqli_query($koneksi, $sql_penjualan);
$data_penjualan = mysqli_fetch_assoc($result_penjualan);

// Jika tidak ada data penjualan, tampilkan pesan error
if (!$data_penjualan) {
    die("Belum ada transaksi penjualan.");
}

// Ambil ID penjualan terbaru
$penjualan_id = $data_penjualan['penjualanid'];

// Ambil detail penjualan terbaru
$sql_detail = "SELECT d.detailid, d.jumlah, d.subtotal, 
                      pr.namaproduk, pr.harga 
               FROM detailpenjualan d
               JOIN produk pr ON d.produkid = pr.produkid
               WHERE d.penjualanid = $penjualan_id";
$result_detail = mysqli_query($koneksi, $sql_detail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Detail Penjualan</h2>
        <hr>

        <h4>Informasi Penjualan</h4>
        <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($data_penjualan['namapelanggan']); ?></p>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($data_penjualan['alamat']); ?></p>
        <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($data_penjualan['nomortelepon']); ?></p>
        <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($data_penjualan['tanggalpenjualan']); ?></p>
        <p><strong>Total Harga:</strong> Rp. <?php echo number_format($data_penjualan['totalharga'], 0, ',', '.'); ?></p>

        <h4>Produk yang Dibeli</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($result_detail) > 0) {
                    while ($row = mysqli_fetch_assoc($result_detail)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['namaproduk']) . "</td>";
                        echo "<td>Rp. " . number_format($row['harga'], 0, ',', '.') . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
                        echo "<td>Rp. " . number_format($row['subtotal'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Tidak ada produk dalam transaksi ini</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <!-- Tombol Kembali ke Dashboard -->
        <a href="<?= ($_SESSION['role'] == 'admin') ? 'dashboard_admin.php' : 'dashboard_petugas.php'; ?>" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
