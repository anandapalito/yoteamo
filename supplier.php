<?php
session_start();
include('koneksi.php');

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$nama_supplier = "";
$alamat = "";
$no_telp = "";
$sukses = "";
$error = "";

// Proses DELETE
if (isset($_GET['op']) && $_GET['op'] == 'delete') {
    $id = $_GET['id'];
    $sql = "DELETE FROM supplier WHERE supplierid = '$id'";
    if (mysqli_query($conn, $sql)) {
        $sukses = "Berhasil menghapus supplier";
    } else {
        $error = "Gagal menghapus supplier: " . mysqli_error($conn);
    }
}

// Proses EDIT
if (isset($_GET['op']) && $_GET['op'] == 'edit') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM supplier WHERE supplierid = '$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $nama_supplier = $row['namasupplier'];
        $alamat = $row['alamat'];
        $no_telp = $row['notelp'];
    } else {
        $error = "Data tidak ditemukan";
    }
}

// Proses INSERT & UPDATE
if (isset($_POST['simpan'])) {
    $nama_supplier = $_POST['nama_supplier'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];
    
    if ($nama_supplier && $alamat && $no_telp) {
        if (isset($_GET['op']) && $_GET['op'] == 'edit') {
            $id = $_GET['id'];
            $sql = "UPDATE supplier SET namasupplier='$nama_supplier', alamat='$alamat', notelp='$no_telp' WHERE supplierid='$id'";
        } else {
            $sql = "INSERT INTO supplier (namasupplier, alamat, notelp) VALUES ('$nama_supplier', '$alamat', '$no_telp')";
        }
        if (mysqli_query($conn, $sql)) {
            $sukses = "Data berhasil disimpan";
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    } else {
        $error = "Silakan isi semua data";
    }
}

// Ambil data supplier
$query = "SELECT * FROM supplier ORDER BY supplierid DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Supplier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Manajemen Supplier</h2>
    
    <?php if ($sukses) echo "<div class='alert alert-success'>$sukses</div>"; ?>
    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label>Nama Supplier</label>
            <input type="text" name="nama_supplier" class="form-control" value="<?php echo $nama_supplier; ?>" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" value="<?php echo $alamat; ?>" required>
        </div>
        <div class="mb-3">
            <label>No. Telp</label>
            <input type="text" name="no_telp" class="form-control" value="<?php echo $no_telp; ?>" required>
        </div>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
    </form>
    
    <h3 class="mt-4">Daftar Supplier</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Supplier</th>
                <th>Alamat</th>
                <th>No. Telp</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['namasupplier']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['notelp']; ?></td>
                <td>
                    <a href="?op=edit&id=<?php echo $row['supplierid']; ?>" class="btn btn-warning">Edit</a>
                    <a href="?op=delete&id=<?php echo $row['supplierid']; ?>" class="btn btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <a href="admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>
</body>
</html>
