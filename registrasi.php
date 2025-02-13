<?php
session_start();
include('koneksi.php'); // Sesuaikan dengan file koneksi database

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'user'; // Default sebagai user biasa

    // Cek apakah username sudah ada
    $checkUser = mysqli_query($conn, "SELECT * FROM Users WHERE Username='$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        echo "<script>alert('Username sudah terdaftar!');</script>";
    } else {
        // Hash password sebelum disimpan
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Simpan ke database
        $query = "INSERT INTO Users (Username, PasswordHash, Role) VALUES ('$username', '$passwordHash', '$role')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Registrasi gagal!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Registrasi Akun</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Daftar</button>
            <p class="mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>
