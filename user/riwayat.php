<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// 1. Proses Pembatalan Pinjam Mandiri dari Riwayat
if (isset($_POST['batal_pinjam'])) {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    $id_buku_batal = (int)$_POST['id_buku'];

    $q_cek = mysqli_query($conn, "SELECT status FROM peminjaman WHERE id_peminjaman = $id_peminjaman AND id_user = $id_user");
    if (mysqli_num_rows($q_cek) > 0) {
        $st_sekarang = mysqli_fetch_assoc($q_cek)['status'];
        if ($st_sekarang == 'menunggu persetujuan' || $st_sekarang == 'siap diambil') {
            mysqli_query($conn, "UPDATE peminjaman SET status = 'dibatalkan' WHERE id_peminjaman = $id_peminjaman");
            mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = $id_buku_batal");
            $_SESSION['success'] = "Peminjaman berhasil dibatalkan.";
            header("Location: riwayat.php");
            exit();
        }
    }
}

// 2. Ambil seluruh riwayat
$query_riwayat = mysqli_query($conn, "
    SELECT p.*, b.judul, b.gambar 
    FROM peminjaman p
    JOIN books b ON p.id_buku = b.id_buku
    WHERE p.id_user = $id_user
    ORDER BY p.id_peminjaman DESC
");

include '../includes/header.php';
?>

<!-- Toolbar Search Bar -->
<div class="catalog-toolbar">
    <div class="search-box">
        <input type="text" id="searchRiwayatInput" placeholder="Cari riwayat judul buku..." onkeyup="filterRiwayat()">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-msg"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="riwayat-container">
    <?php if (mysqli_num_rows($query_riwayat) === 0): ?>
        <p class="no-img" style="text-align: center; padding: 40px 0;">Belum ada riwayat peminjaman buku.</p>
    <?php else: ?>
        <div class="book-grid" id="riwayatGrid">
            <?php while ($row = mysqli_fetch_assoc($query_riwayat)): 
                $st = $row['status'];
                
                // Variasi Warna & Ikon Badge
                $badge_class = 'badge-yellow'; $icon = 'fa-clock';
                if ($st == 'siap diambil') { $badge_class = 'badge-sky'; $icon = 'fa-box-archive'; }
                elseif ($st == 'dipinjam') { $badge_class = 'badge-indigo'; $icon = 'fa-book-reader'; }
                elseif ($st == 'selesai') { $badge_class = 'badge-green'; $icon = 'fa-check'; }
                elseif ($st == 'dibatalkan') { $badge_class = 'badge-red'; $icon = 'fa-xmark'; }
            ?>
                <!-- Kartu bisa di-klik untuk menuju detail buku -->
                <div class="book-card riwayat-card-item" data-judul="<?= htmlspecialchars($row['judul']); ?>" onclick="window.location.href='detail_buku.php?id=<?= $row['id_buku']; ?>'" style="cursor: pointer;">
                    
                    <div class="book-cover-wrap">
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="../assets/img/buku/<?= $row['gambar']; ?>" alt="Cover" class="book-cover-img">
                        <?php else: ?>
                            <i class="fa-solid fa-book book-cover-placeholder"></i>
                        <?php endif; ?>
                    </div>

                    <div class="book-card-info">
                        <div>
                            <h4 class="book-card-title"><?= htmlspecialchars($row['judul']); ?></h4>
                            
                            <div class="riwayat-dates">
                                <?php if ($st == 'dipinjam' || $st == 'selesai'): ?>
                                    <p>Tanggal Pinjam: <span><?= (!empty($row['tanggal_pinjam']) && $row['tanggal_pinjam'] != '0000-00-00') ? date('d/m/Y', strtotime($row['tanggal_pinjam'])) : '-'; ?></span></p>
                                    <p>Jatuh Tempo: <span><?= (!empty($row['jatuh_tempo']) && $row['jatuh_tempo'] != '0000-00-00') ? date('d/m/Y', strtotime($row['jatuh_tempo'])) : '-'; ?></span></p>
                                <?php endif; ?>

                                <?php if ($st == 'selesai'): ?>
                                    <p>Tanggal Kembali: <span><?= (!empty($row['tanggal_kembali']) && $row['tanggal_kembali'] != '0000-00-00') ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-'; ?></span></p>
                                    <?php if ($row['denda'] > 0): ?>
                                        <p class="text-denda">Denda: <span>Rp <?= number_format($row['denda'], 0, ',', '.'); ?></span></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Baris Tombol Batal & Badge Status -->
                        <div class="riwayat-badge-wrap" style="display: flex; gap: 8px; justify-content: flex-end; align-items: center; margin-top: 10px;">
                            <?php if ($st == 'menunggu persetujuan' || $st == 'siap diambil'): ?>
                                <form action="" method="POST" style="margin: 0;">
                                    <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                                    <input type="hidden" name="id_buku" value="<?= $row['id_buku']; ?>">
                                    <!-- event.stopPropagation() mencegah klik kartu saat mengeklik tombol batal -->
                                    <button type="submit" name="batal_pinjam" class="btn-danger" onclick="event.stopPropagation(); return confirm('Yakin ingin membatalkan pengajuan pinjaman ini?');">
                                        Batal
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <span class="status-badge <?= $badge_class; ?>">
                                <i class="fa-solid <?= $icon; ?>"></i> <?= ucfirst($st); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>