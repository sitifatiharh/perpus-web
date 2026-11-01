<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM peminjaman WHERE id_peminjaman = $id AND (status = 'selesai' OR status = 'dibatalkan')");
    $_SESSION['success'] = "Riwayat peminjaman berhasil dihapus!";
}

header("Location: peminjaman.php");
exit();
?>