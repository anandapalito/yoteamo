<?php
session_start();
include('koneksi.php'); // Pastikan koneksi benar

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nama_barang = "";
$harga = "";
$stok = "";
$sukses = "";
$error = "";

// Cek operasi (edit/delete)
$op = isset($_GET['op']) ? $_GET['op'] : "";

// Hapus data
if ($op == 'delete') {
    $id = intval($_GET['id']);
    
    // Hapus data terkait di tabel detailpembelian jika ada
    $sql_delete_detail = "DELETE FROM detailpembelian WHERE barangid = $id";
    mysqli_query($conn, $sql_delete_detail);
    
    // Hapus barang
    $sql1 = "DELETE FROM barang WHERE barangid = $id";
    $q1 = mysqli_query($conn, $sql1);
    if ($q1) {
        $sukses = "Berhasil menghapus data";
    } else {
        $error = "Gagal menghapus data: " . mysqli_error($conn);
    }
}

// Edit data
if ($op == 'edit') {
    $id = intval($_GET['id']);
    $sql1 = "SELECT * FROM barang WHERE barangid = $id";
    $q1 = mysqli_query($conn, $sql1);
    $r1 = mysqli_fetch_array($q1);
    if ($r1) {
        $nama_barang = $r1['nama_barang'];
        $harga = $r1['harga'];
        $stok = $r1['stok'];
    } else {
        $error = "Data tidak ditemukan";
    }
}

// Simpan data (Insert atau Update)
if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($nama_barang && $harga && $stok) {
        if ($op == 'edit') { // Update data
            $sql1 = "UPDATE barang SET nama_barang = '$nama_barang', harga = '$harga', stok = '$stok' WHERE barangid = $id";
            $q1 = mysqli_query($conn, $sql1);
            if ($q1) {
                $sukses = "Data berhasil diperbarui";
            } else {
                $error = "Data gagal diperbarui: " . mysqli_error($conn);
            }
        } else { // Insert data baru
            $sql1 = "INSERT INTO barang (nama_barang, harga, stok) VALUES ('$nama_barang', '$harga', '$stok')";
            $q1 = mysqli_query($conn, $sql1);
            if ($q1) {
                $sukses = "Berhasil menambahkan data baru";
            } else {
                $error = "Gagal menambahkan data: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "Silakan isi semua data";
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
        <?php if ($error) { ?>
            <div class="alert alert-danger"> <?php echo $error; ?> </div>
        <?php } ?>
        <?php if ($sukses) { ?>
            <div class="alert alert-success"> <?php echo $sukses; ?> </div>
        <?php } ?>

        <!-- Form Tambah / Edit -->
        <div class="card mb-3">
            <div class="card-header">Tambah / Edit Barang</div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo $nama_barang; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $harga; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $stok; ?>" required>
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
                        $sql2 = "SELECT * FROM barang ORDER BY barangid DESC";
                        $q2 = mysqli_query($conn, $sql2);
                        $urut = 1;
                        while ($r2 = mysqli_fetch_array($q2)) {
                            $id = $r2['barangid'];
                        ?>
                            <tr>
                                <td><?php echo $urut++; ?></td>
                                <td><?php echo $r2['nama_barang']; ?></td>
                                <td>Rp <?php echo number_format($r2['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $r2['stok']; ?></td>
                                <td>
                                    <a href="pendataan_barang.php?op=edit&id=<?php echo $id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="pendataan_barang.php?op=delete&id=<?php echo $id; ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="admin.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>