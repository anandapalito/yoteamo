<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['namapelanggan'];
    $alamat = $_POST['alamat'];
    $nomortelepon = $_POST['nomortelepon'];

    // Validasi input tidak boleh kosong
    if (!empty($nama) && !empty($alamat) && !empty($nomortelepon)) {
        // Masukkan data pelanggan ke database
        $query = "INSERT INTO pelanggan (namapelanggan, alamat, nomortelepon) VALUES ('$nama', '$alamat', '$nomortelepon')";
        if (mysqli_query($koneksi, $query)) {
            echo "Pelanggan berhasil ditambahkan! <a href='penjualan.php'>Kembali ke Penjualan</a>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
        echo "Semua field harus diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Pelanggan</title>
</head>
<body>
    <h2>Tambah Pelanggan Baru</h2>
    <form method="POST">
        Nama Pelanggan: <input type="text" name="namapelanggan" required><br>
        Alamat: <input type="text" name="alamat" required><br>
        Nomor Telepon: <input type="text" name="nomortelepon" required><br>
        <button type="submit">Tambah</button>
    </form>
    <br>
    <a href="penjualan.php">Kembali ke Penjualan</a>
</body>
</html>
