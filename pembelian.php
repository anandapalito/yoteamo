<?php
session_start();
include('koneksi.php'); // Pastikan koneksi database benar

if (isset($_POST['beli'])) {
    $barangid = $_POST['barangid'];
    $jumlah = $_POST['jumlah'];
    $tanggal_pembelian = date('Y-m-d');

    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomor_telepon = $_POST['nomor_telepon'];

    // Ambil harga barang
    $query_barang = "SELECT * FROM barang WHERE barangid = '$barangid'";
    $result_barang = mysqli_query($conn, $query_barang);
    $barang = mysqli_fetch_assoc($result_barang);

    if ($barang && $jumlah > 0) {
        $subtotal = $barang['harga'] * $jumlah;

        // Simpan ke tabel pembelian
        $sql_pembelian = "INSERT INTO pembelian (tanggalpembelian, totalharga, nama_pelanggan, alamat, nomor_telepon) 
                          VALUES ('$tanggal_pembelian', '$subtotal', '$nama_pelanggan', '$alamat', '$nomor_telepon')";

        if (mysqli_query($conn, $sql_pembelian)) {
            $pembelian_id = mysqli_insert_id($conn);

            // Simpan ke tabel detail pembelian
            $sql_detail = "INSERT INTO detailpembelian (pembelianid, barangid, jumlah, subtotal) 
                           VALUES ('$pembelian_id', '$barangid', '$jumlah', '$subtotal')";

            if (mysqli_query($conn, $sql_detail)) {
                // Kurangi stok barang
                $stok_baru = $barang['stok'] - $jumlah;
                $sql_update_stok = "UPDATE barang SET stok = '$stok_baru' WHERE barangid = '$barangid'";
                mysqli_query($conn, $sql_update_stok);

                echo "<script>alert('Pembelian berhasil!'); window.location='admin.php';</script>";
            } else {
                echo "Gagal menyimpan detail pembelian: " . mysqli_error($conn);
            }
        } else {
            echo "Gagal menyimpan pembelian: " . mysqli_error($conn);
        }
    } else {
        echo "Barang tidak ditemukan atau jumlah tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Pembelian Barang</h2>
        <hr>

        <form method="POST">
            <label>Pilih Barang:</label>
            <select name="barangid" class="form-control mb-2">
                <?php
                $result = mysqli_query($conn, "SELECT * FROM barang");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['barangid']}'>{$row['nama_barang']} - Rp. " . number_format($row['harga'], 0, ',', '.') . "</option>";
                }
                ?>
            </select>
            
            <label>Jumlah:</label>
            <input type="number" name="jumlah" min="1" required class="form-control mb-2">

            <h5>Data Pelanggan</h5>
            <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" required class="form-control mb-2">
            <input type="text" name="alamat" placeholder="Alamat" required class="form-control mb-2">
            <input type="text" name="nomor_telepon" placeholder="Nomor Telepon" required class="form-control mb-2">

            <button type="submit" name="beli" class="btn btn-primary">Beli</button>
        </form>
    </div>
</body>
</html>
