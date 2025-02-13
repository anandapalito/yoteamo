<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? $_POST['role'] : ''; // Pastikan role ada

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password) || empty($role)) {
        echo "Semua kolom harus diisi!";
        exit;
    }

    // Cek apakah username sudah ada
    $query_check = "SELECT * FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result) > 0) {
        echo "Username sudah digunakan! <a href='register.php'>Coba lagi</a>";
        exit;
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk insert data
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt)) {
        echo "Registrasi berhasil! <a href='login.php'>Login</a>";
    } else {
        echo "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Registrasi</title>
</head>
<body>
    <h2>Registrasi</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        
        <label>Password:</label>
        <input type="password" name="password" required><br>
        
        <label>Role:</label>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
        </select><br>
        
        <button type="submit">Daftar</button>
    </form>
</body>
</html>
