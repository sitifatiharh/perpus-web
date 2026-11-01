<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    $query_buku = mysqli_query($conn, "SELECT gambar FROM books WHERE id_buku = $id");
    if (mysqli_num_rows($query_buku) > 0) {
        $buku = mysqli_fetch_assoc($query_buku);

        if (!empty($buku['gambar']) && file_exists('../assets/img/buku/' . $buku['gambar'])) {
            unlink('../assets/img/buku/' . $buku['gambar']);
        }

        $delete = mysqli_query($conn, "DELETE FROM books WHERE id_buku = $id");

        if ($delete) {
            $_SESSION['success'] = "Buku berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus buku: " . mysqli_error($conn);
        }
    }
}

header("Location: buku.php");
exit();
?>