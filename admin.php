<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('koneksi.php'); // Koneksi ke database

// Ambil jumlah total barang dan supplier
$query_barang = mysqli_query($conn, "SELECT COUNT(*) AS total_barang FROM barang");
$data_barang = mysqli_fetch_assoc($query_barang);

$query_supplier = mysqli_query($conn, "SELECT COUNT(*) AS total_supplier FROM supplier");
$data_supplier = mysqli_fetch_assoc($query_supplier);

// Ambil daftar pembelian terbaru
$query_pembelian = mysqli_query($conn, "SELECT pembelianid, tanggalpembelian, totalharga FROM pembelian ORDER BY pembelianid DESC LIMIT 5");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Dashboard Admin</h2>
        <p>Selamat datang, <b><?php echo $_SESSION['username']; ?></b></p>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-info text-white p-3">
                    <h5>Total Barang</h5>
                    <h3><?php echo $data_barang['total_barang']; ?></h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-success text-white p-3">
                    <h5>Total Supplier</h5>
                    <h3><?php echo $data_supplier['total_supplier']; ?></h3>
                </div>
            </div>
        </div>

        <a href="pendataan_barang.php" class="btn btn-primary">Kelola Barang</a>  
        <a href="supplier.php" class="btn btn-primary">Kelola Supplier</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>

        <hr>

        <h3>Detail Pembelian Terbaru</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pembelian</th>
                    <th>Tanggal</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($query_pembelian)) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $row['pembelianid'] . "</td>";
                    echo "<td>" . $row['tanggalpembelian'] . "</td>";
                    echo "<td>Rp. " . number_format($row['totalharga'], 0, ',', '.') . "</td>";
                    echo "<td>
                        <a href='detailpembelian.php?pembelian_id=" . $row['pembelianid'] . "' class='btn btn-primary'>Detail</a>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
