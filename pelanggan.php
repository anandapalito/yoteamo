<?php
session_start();
include('koneksi.php');

// Cek apakah user sudah login
if (!isset($_SESSION['userid'])) {
    header('Location: login.php');
    exit;
}

$sukses = "";
$error = "";

// Proses pembelian
if (isset($_POST['beli'])) {
    $barangid = $_POST['barangid'];
    $jumlah = $_POST['jumlah'];
    $tanggal_pembelian = date('Y-m-d');
    $userid = $_SESSION['userid'];
    
    // Ambil data pelanggan
    $query_pelanggan = "SELECT * FROM pelanggan WHERE userid = '$userid'";
    $result_pelanggan = mysqli_query($conn, $query_pelanggan);
    $pelanggan = mysqli_fetch_assoc($result_pelanggan);
    
    if (!$pelanggan) {
        $error = "Data pelanggan tidak ditemukan.";
    } else {
        // Ambil harga barang
        $query_barang = "SELECT * FROM barang WHERE barangid = '$barangid'";
        $result_barang = mysqli_query($conn, $query_barang);
        $barang = mysqli_fetch_assoc($result_barang);
        
        if ($barang && $jumlah > 0 && $barang['stok'] >= $jumlah) {
            $subtotal = $barang['harga'] * $jumlah;
            
            // Simpan ke tabel pembelian
            $sql_pembelian = "INSERT INTO pembelian (tanggalpembelian, pelangganid, totalharga) VALUES ('$tanggal_pembelian', '{$pelanggan['pelangganid']}', '$subtotal')";
            if (mysqli_query($conn, $sql_pembelian)) {
                $pembelian_id = mysqli_insert_id($conn);
                
                // Simpan ke tabel detail pembelian
                $sql_detail = "INSERT INTO detailpembelian (pembelianid, barangid, jumlah, subtotal) VALUES ('$pembelian_id', '$barangid', '$jumlah', '$subtotal')";
                if (mysqli_query($conn, $sql_detail)) {
                    // Kurangi stok barang
                    $stok_baru = $barang['stok'] - $jumlah;
                    $sql_update_stok = "UPDATE barang SET stok = '$stok_baru' WHERE barangid = '$barangid'";
                    mysqli_query($conn, $sql_update_stok);
                    
                    $sukses = "Pembelian berhasil!";
                } else {
                    $error = "Gagal menyimpan detail pembelian: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal menyimpan pembelian: " . mysqli_error($conn);
            }
        } else {
            $error = "Barang tidak ditemukan atau stok tidak mencukupi.";
        }
    }
}

// Ambil daftar barang
$query_barang = "SELECT * FROM barang";
$result_barang = mysqli_query($conn, $query_barang);

// Ambil riwayat pembelian pelanggan
$query_pembelian = "SELECT p.tanggalpembelian, b.nama_barang, d.jumlah, d.subtotal, pl.nama, pl.alamat, pl.notelp FROM pembelian p JOIN detailpembelian d ON p.pembelianid = d.pembelianid JOIN barang b ON d.barangid = b.barangid JOIN pelanggan pl ON p.pelangganid = pl.pelangganid ORDER BY p.tanggalpembelian DESC";
$result_pembelian = mysqli_query($conn, $query_pembelian);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Pembelian Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Pembelian Barang</h2>
    
    <?php if ($sukses) echo "<div class='alert alert-success'>$sukses</div>"; ?>
    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Beli</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result_barang)) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama_barang']; ?></td>
                <td>Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $row['stok']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="barangid" value="<?php echo $row['barangid']; ?>">
                        <input type="number" name="jumlah" min="1" max="<?php echo $row['stok']; ?>" required>
                        <button type="submit" name="beli" class="btn btn-primary">Beli</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <h2 class="mt-5">Riwayat Pembelian</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal Pembelian</th>
                <th>Nama Pelanggan</th>
                <th>Alamat</th>
                <th>No Telepon</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result_pembelian)) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['tanggalpembelian']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['notelp']; ?></td>
                <td><?php echo $row['nama_barang']; ?></td>
                <td><?php echo $row['jumlah']; ?></td>
                <td>Rp. <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <a href="dashboarduser.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>
</body>
</html>
