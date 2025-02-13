<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelangganid = $_POST['pelangganid'];
    $produkid = $_POST['produkid'];
    $jumlah = $_POST['jumlah'];

    // Cek stok produk
    $query_stok = "SELECT stok, harga FROM produk WHERE produkid = $produkid";
    $result_stok = mysqli_query($koneksi, $query_stok);
    $produk = mysqli_fetch_assoc($result_stok);

    if ($produk['stok'] >= $jumlah) {
        // Hitung subtotal harga
        $subtotal = $jumlah * $produk['harga'];

        // Simpan data ke tabel penjualan
        $query_penjualan = "INSERT INTO penjualan (tanggalpenjualan, totalharga, pelangganid) VALUES (NOW(), $subtotal, $pelangganid)";
        mysqli_query($koneksi, $query_penjualan);
        $penjualanid = mysqli_insert_id($koneksi);

        // Simpan data ke tabel detailpenjualan
        $query_detail = "INSERT INTO detailpenjualan (penjualanid, produkid, jumlah, subtotal) VALUES ($penjualanid, $produkid, $jumlah, $subtotal)";
        mysqli_query($koneksi, $query_detail);

        // Update stok produk
        $query_update_stok = "UPDATE produk SET stok = stok - $jumlah WHERE produkid = $produkid";
        mysqli_query($koneksi, $query_update_stok);

        echo "Pembelian berhasil! <a href='penjualan.php'>Kembali</a>";
    } else {
        echo "Stok tidak mencukupi!";
    }
}

// Ambil daftar produk untuk dropdown
$query_produk = "SELECT * FROM produk";
$result_produk = mysqli_query($koneksi, $query_produk);

// Ambil daftar pelanggan untuk dropdown
$query_pelanggan = "SELECT * FROM pelanggan";
$result_pelanggan = mysqli_query($koneksi, $query_pelanggan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penjualan</title>
</head>
<body>
<br>
<a href="tambahpelanggan.php">Tambah Pelanggan Baru</a>

    <h2>Form Pembelian</h2>
    <form method="POST">
        Pelanggan:
        <select name="pelangganid">
            <?php while ($pelanggan = mysqli_fetch_assoc($result_pelanggan)) { ?>
                <option value="<?= $pelanggan['pelangganid']; ?>"><?= $pelanggan['namapelanggan']; ?></option>
            <?php } ?>
        </select>
        <br>

        Produk:
        <select name="produkid">
            <?php while ($produk = mysqli_fetch_assoc($result_produk)) { ?>
                <option value="<?= $produk['produkid']; ?>"><?= $produk['namaproduk']; ?> (Stok: <?= $produk['stok']; ?>)</option>
            <?php } ?>
        </select>
        <br>

        Jumlah:
        <input type="number" name="jumlah" required>
        <br>

        <button type="submit">Beli</button>
    </form>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <a href="dashboard_admin.php" class="btn btn-secondary mt-3">Kembali ke Dashboard Admin</a>
                <?php } else { ?>
                    <a href="dashboard_petugas.php" class="btn btn-secondary mt-3">Kembali ke Dashboard Petugas</a>
                <?php } ?>

</body>
</html>
