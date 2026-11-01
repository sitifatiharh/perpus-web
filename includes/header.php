<?php
// Ambil role dari session untuk menentukan menu navigasi
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($role); ?> - Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- DataTables CSS untuk tabel interaktif -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Font Awesome untuk Ikon Edit/Hapus -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <!-- Navbar Atas -->
    <nav class="navbar">
        <!-- Tombol Hamburger untuk Mobile -->
        <div class="hamburger" onclick="toggleSidebar()">
            &#9776; 
        </div>
        
        <div class="nav-brand desktop-only">PERPUSTAKAAN</div>
        
        <!-- Menu Desktop -->
        <div class="nav-menu">
            <?php if ($role === 'admin'): ?>
                <a href="../admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Beranda</a>
                <a href="../admin/buku.php" class="<?= in_array($current_page, ['buku.php', 'tambah_buku.php', 'edit_buku.php', 'buku_add.php', 'buku_edit.php']) ? 'active' : '' ?>">Buku</a>
                <a href="../admin/peminjaman.php" class="<?= $current_page == 'peminjaman.php' ? 'active' : '' ?>">Peminjaman</a>
                <a href="../admin/anggota.php" class="<?= in_array($current_page, ['anggota.php', 'tambah_admin.php', 'edit_admin.php']) ? 'active' : '' ?>">Anggota</a>
            <?php else: ?>
                <a href="../user/katalog.php" class="<?= $current_page == 'katalog.php' ? 'active' : '' ?>">Katalog</a>
                <a href="../user/riwayat.php" class="<?= $current_page == 'riwayat.php' ? 'active' : '' ?>">Riwayat</a>
            <?php endif; ?>
        </div>
        
        <div class="nav-profile">
            <img src="../assets/img/user.png" alt="Profile" class="profile-nav-icon">
            <a href="../auth/logout.php" class="logout-link desktop-only" title="Logout" onclick="return confirm('Yakin ingin keluar?');">Logout</a>
        </div>
    </nav>

    <!-- Sidebar Mobile -->
    <div id="mobile-sidebar" class="sidebar">
        <div class="sidebar-header">
            <h2 class="nav-brand">PERPUSTAKAAN</h2>
            <span class="close-btn" onclick="toggleSidebar()">&times;</span>
        </div>
        <div class="sidebar-menu">
            <?php if ($role === 'admin'): ?>
                <a href="../admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Beranda</a>
                <a href="../admin/buku.php" class="<?= in_array($current_page, ['buku.php', 'tambah_buku.php', 'edit_buku.php', 'buku_add.php', 'buku_edit.php']) ? 'active' : '' ?>">Buku</a>
                <a href="../admin/peminjaman.php" class="<?= $current_page == 'peminjaman.php' ? 'active' : '' ?>">Peminjaman</a>
                <a href="../admin/anggota.php" class="<?= in_array($current_page, ['anggota.php', 'tambah_admin.php', 'edit_admin.php']) ? 'active' : '' ?>">Anggota</a>
            <?php else: ?>
                <a href="../user/katalog.php" class="<?= $current_page == 'katalog.php' ? 'active' : '' ?>">Katalog</a>
                <a href="../user/riwayat.php" class="<?= $current_page == 'riwayat.php' ? 'active' : '' ?>">Riwayat</a>
            <?php endif; ?>
            <a href="../auth/logout.php" style="color: #e55039; font-weight: bold;" onclick="return confirm('Yakin ingin keluar?');">Logout</a>
        </div>
    </div>
    
    <div class="main-content">