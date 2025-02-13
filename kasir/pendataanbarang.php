<?php
session_start();
include('config.php'); // Pastikan file koneksi benar

$namaproduk = "";
$harga = "";
$stok = "";
$sukses = "";
$error = "";

// Cek operasi (edit/delete)
$op = isset($_GET['op']) ? $_GET['op'] : "";

// Hapus data
if ($op == 'delete') {
    $id = intval($_GET['id']);

    // Hapus data terkait di detailpembelian
    $sql_delete_detail = "DELETE FROM detailpenjualan WHERE produkid = $id";
    mysqli_query($koneksi, $sql_delete_detail);

    // Hapus barang
    $sql1 = "DELETE FROM produk WHERE produkid = $id";
    $q1 = mysqli_query($koneksi, $sql1);
    if ($q1) {
        $sukses = "Berhasil menghapus data.";
    } else {
        $error = "Gagal menghapus data: " . mysqli_error($koneksi);
    }
}

// Edit data
if ($op == 'edit') {
    $id = intval($_GET['id']);
    $sql1 = "SELECT * FROM produk WHERE produkid = $id";
    $q1 = mysqli_query($koneksi, $sql1);
    $r1 = mysqli_fetch_array($q1);
    if ($r1) {
        $namaproduk = $r1['namaproduk'];
        $harga = $r1['harga'];
        $stok = $r1['stok'];
    } else {
        $error = "Data tidak ditemukan.";
    }
}

// Simpan data (Insert atau Update)
if (isset($_POST['simpan'])) {
    // Pastikan key tersedia untuk menghindari error
    $namaproduk = isset($_POST['namaproduk']) ? mysqli_real_escape_string($koneksi, $_POST['namaproduk']) : "";
    $harga = isset($_POST['harga']) ? intval($_POST['harga']) : 0;
    $stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;

    if (!empty($namaproduk) && $harga > 0 && $stok >= 0) {
        if ($op == 'edit' && isset($_GET['id'])) { // Update data
            $id = intval($_GET['id']);
            $sql1 = "UPDATE produk SET namaproduk = '$namaproduk', harga = $harga, stok = $stok WHERE produkid = $id";
            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = "Data berhasil diperbarui!";
            } else {
                $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
            }
        } else { // Insert data baru
            $sql1 = "INSERT INTO produk (namaproduk, harga, stok) VALUES ('$namaproduk', $harga, $stok)";
            $q1 = mysqli_query($koneksi, $sql1);
            if ($q1) {
                $sukses = "Berhasil menambahkan data baru!";
            } else {
                $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
            }
        }
    } else {
        $error = "Silakan isi semua data dengan benar!";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Manajemen Barang</h2>

        <!-- Notifikasi -->
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <?php if (!empty($sukses)) { ?>
            <div class="alert alert-success"><?php echo $sukses; ?></div>
        <?php } ?>

        <!-- Form Tambah / Edit -->
        <div class="card mb-3">
            <div class="card-header">Tambah / Edit Produk</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="namaproduk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="namaproduk" name="namaproduk" value="<?php echo htmlspecialchars($namaproduk); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>

        <!-- Tabel Barang -->
        <div class="card">
            <div class="card-header">Data Barang</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql2 = "SELECT * FROM produk ORDER BY produkid DESC";
                        $q2 = mysqli_query($koneksi, $sql2);
                        $urut = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $id = $r2['produkid'];
                        ?>
                            <tr>
                                <td><?php echo $urut++; ?></td>
                                <td><?php echo htmlspecialchars($r2['namaproduk']); ?></td>
                                <td>Rp <?php echo number_format($r2['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($r2['stok']); ?></td>
                                <td>
                                    <a href="pendataanbarang.php?op=edit&id=<?php echo $id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="pendataanbarang.php?op=delete&id=<?php echo $id; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Tombol Kembali ke Dashboard -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                    <a href="dashboard_admin.php" class="btn btn-secondary mt-3">Kembali ke Dashboard Admin</a>
                <?php } else { ?>
                    <a href="dashboard_petugas.php" class="btn btn-secondary mt-3">Kembali ke Dashboard Petugas</a>
                <?php } ?>

            </div>
        </div>
    </div>
</body>
</html>
