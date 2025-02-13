<?php

require 'config.php'; // Menghubungkan ke database

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

if ($aksi == "tambah" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES ('$nama', '$harga', '$stok')";
    mysqli_query($koneksi, $query);
    header("Location: produk.php");
}

if ($aksi == "edit" && $_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = "UPDATE produk SET namaproduk='$nama', harga='$harga', stok='$stok' WHERE produkid=$id";
    mysqli_query($koneksi, $query);
    header("Location: produk.php");
}

if ($aksi == "hapus" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM produk WHERE produkid = $id";
    mysqli_query($koneksi, $query);
    header("Location: produk.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen Produk</title>
</head>
<body>

<h2>Manajemen Produk</h2>

<?php if ($aksi == "tambah" || $aksi == "edit") : ?>
    <?php
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $nama = $harga = $stok = '';

    if ($aksi == "edit") {
        $query = "SELECT * FROM produk WHERE produkid = $id";
        $result = mysqli_query($koneksi, $query);
        $data = mysqli_fetch_assoc($result);
        $nama = $data['namaproduk'];
        $harga = $data['harga'];
        $stok = $data['stok'];
    }
    ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $id; ?>">
        Nama Produk: <input type="text" name="nama" value="<?= $nama; ?>" required><br>
        Harga: <input type="number" name="harga" value="<?= $harga; ?>" required><br>
        Stok: <input type="number" name="stok" value="<?= $stok; ?>" required><br>
        <button type="submit"><?= ($aksi == "edit") ? "Update" : "Tambah"; ?></button>
    </form>
    <a href="produk.php">Batal</a>

<?php else : ?>
    <a href="produk.php?aksi=tambah">Tambah Produk</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php
        $query = "SELECT * FROM produk";
        $result = mysqli_query($koneksi, $query);
        while ($row = mysqli_fetch_assoc($result)) :
        ?>
        <tr>
            <td><?= $row['produkid']; ?></td>
            <td><?= $row['namaproduk']; ?></td>
            <td><?= $row['harga']; ?></td>
            <td><?= $row['stok']; ?></td>
            <td>
                <a href="produk.php?aksi=edit&id=<?= $row['produkid']; ?>">Edit</a> |
                <a href="produk.php?aksi=hapus&id=<?= $row['produkid']; ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </table>
    <?php if ($_SESSION['role'] == 'admin') { ?>
    <a href="dashboard_admin.php">Kembali ke Dashboard Admin</a>
<?php } else { ?>
    <a href="dashboard_petugas.php">Kembali ke Dashboard Petugas</a>
<?php } ?>

</body>
</html>
