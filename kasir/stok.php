<?php
require 'config.php'; // Koneksi ke database

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($aksi == "tambah" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($koneksi, "UPDATE produk SET Stok = Stok + $jumlah WHERE ProdukID = $id");
    header("Location: stok.php");
}

if ($aksi == "kurang" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Cek stok terlebih dahulu
    $produk = mysqli_query($koneksi, "SELECT Stok FROM produk WHERE ProdukID = $id");
    $data = mysqli_fetch_assoc($produk);

    if ($data['Stok'] >= $jumlah) {
        mysqli_query($koneksi, "UPDATE produk SET Stok = Stok - $jumlah WHERE ProdukID = $id");
    } else {
        echo "Stok tidak mencukupi!";
    }
    header("Location: stok.php");
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Stok Barang</title>
</head>
<body>

<h2>Tambah Stok</h2>
<form method="POST" action="stok.php?aksi=tambah">
    Produk ID: <input type="number" name="produk_id" required><br>
    Jumlah: <input type="number" name="jumlah" required><br>
    <button type="submit">Tambah</button>
</form>

<h2>Kurangi Stok</h2>
<form method="POST" action="stok.php?aksi=kurang">
    Produk ID: <input type="number" name="produk_id" required><br>
    Jumlah: <input type="number" name="jumlah" required><br>
    <button type="submit">Kurangi</button>
</form>

<h3>Data Stok Barang</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama Produk</th>
        <th>Stok</th>
    </tr>
    <?php
    $query = "SELECT ProdukID, NamaProduk, Stok FROM produk";
    $result = mysqli_query($koneksi, $query);
    while ($row = mysqli_fetch_assoc($result)) :
    ?>
    <tr>
        <td><?= $row['ProdukID']; ?></td>
        <td><?= $row['NamaProduk']; ?></td>
        <td><?= $row['Stok']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<br>

</body>
</html>
