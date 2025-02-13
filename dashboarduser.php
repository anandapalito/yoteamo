<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

include('koneksi.php'); // Sesuaikan dengan file koneksi database

// Ambil daftar barang
$query_barang = mysqli_query($conn, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Dashboard User</h2>
        <p>Selamat datang, <b><?php echo $_SESSION['username']; ?></b></p>

        <h4>Daftar Barang</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($query_barang)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_barang']}</td>
                        <td>Rp. " . number_format($row['harga'], 0, ',', '.') . "</td>
                        <td>{$row['stok']}</td>
                    </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <a href="pembelian.php" class="btn btn-success">Silahkan Beli</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
