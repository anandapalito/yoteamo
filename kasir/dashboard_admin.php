<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Dashboard Admin</h1>
    <nav>
        <ul>
            <li><a href="dashboard_admin.php">Home</a></li>
            <li><a href="penjualan.php">Laporan Penjualan</a></li>
            <li><a href="stok.php">stok</a></li>
            <li><a href="pendataanbarang.php">pendataan Barang</a></li>
            <li><a href="detail_penjualan.php">detail Penjualan</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <section>
        <h2>Selamat datang, <?php echo $username; ?>!</h2>
        <p>Anda login sebagai <strong>Admin</strong>.</p>
    </section>
</body>
</html>
