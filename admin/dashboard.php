<?php
session_start();
require '../config/db.php';

// Proteksi halaman khusus admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// 1. Request Baru
$req_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'menunggu persetujuan'");
$total_request = mysqli_fetch_assoc($req_query)['total'];

// 52 Buku Siap Diambil
$ambil_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'siap diambil'");
$total_diambil = mysqli_fetch_assoc($ambil_query)['total'];

// 3. Buku Sedang Dipinjam
$pinjam_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'");
$total_dipinjam = mysqli_fetch_assoc($pinjam_query)['total'];

// 4. Total Judul Buku
$buku_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$total_buku = mysqli_fetch_assoc($buku_query)['total'];

// 5. Buku Tersedia
$tersedia_query = mysqli_query($conn, "SELECT SUM(stok_tersedia) as total FROM books");
$total_tersedia = mysqli_fetch_assoc($tersedia_query)['total'] ?? 0;

// 6. Total Anggota (Role = user)
$anggota_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_anggota = mysqli_fetch_assoc($anggota_query)['total'];

include '../includes/header.php';
?>

<div class="dashboard-grid">
        <!-- Kartu 1: Request -->
    <div class="stat-card bg-red">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/clock.png" alt="Request"></div>
            <div class="stat-info">
                <h3><?= $total_request; ?></h3>
                <p>Request Baru</p>
            </div>
        </div>
        <a href="peminjaman.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>

    <!-- Kartu 2: Siap Diambil -->
    <div class="stat-card bg-purple">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/parcel.png" alt="Diambil"></div>
            <div class="stat-info">
                <h3><?= $total_diambil; ?></h3>
                <p>Siap Diambil</p>
            </div>
        </div>
        <a href="peminjaman.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>

    <!-- Kartu 3: Dipinjam -->
    <div class="stat-card bg-dark">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/save.png" alt="Dipinjam"></div>
            <div class="stat-info">
                <h3><?= $total_dipinjam; ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
        </div>
        <a href="peminjaman.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>

    <!-- Kartu 4: Buku -->
    <div class="stat-card bg-blue">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/books.png" alt="Buku"></div>
            <div class="stat-info">
                <h3><?= $total_buku; ?></h3>
                <p>Total Buku</p>
            </div>
        </div>
        <a href="buku.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>

    <!-- Kartu 5: Tersedia -->
    <div class="stat-card bg-green">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/check.png" alt="Tersedia"></div>
            <div class="stat-info">
                <h3><?= $total_tersedia; ?></h3>
                <p>Tersedia</p>
            </div>
        </div>
        <a href="buku.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>

    <!-- Kartu 6: Anggota -->
    <div class="stat-card bg-yellow">
        <div class="stat-main">
            <div class="stat-icon"><img src="../assets/img/users.png" alt="Anggota"></div>
            <div class="stat-info">
                <h3><?= $total_anggota; ?></h3>
                <p>Anggota</p>
            </div>
        </div>
        <a href="anggota.php" class="stat-footer">View details <span>&#9658;</span></a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>