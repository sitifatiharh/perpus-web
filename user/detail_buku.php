<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: katalog.php");
    exit();
}

$id_buku = (int)$_GET['id'];
$id_user = $_SESSION['id_user'];

// Proses Pembatalan Pinjam
if (isset($_POST['batal_pinjam'])) {
    $id_p = (int)$_POST['id_peminjaman'];
    $q_cek = mysqli_query($conn, "SELECT status FROM peminjaman WHERE id_peminjaman = $id_p AND id_user = $id_user");
    if (mysqli_num_rows($q_cek) > 0) {
        $st_sekarang = mysqli_fetch_assoc($q_cek)['status'];
        if ($st_sekarang == 'menunggu persetujuan' || $st_sekarang == 'siap diambil') {
            mysqli_query($conn, "UPDATE peminjaman SET status = 'dibatalkan' WHERE id_peminjaman = $id_p");
            mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = $id_buku");
            $_SESSION['success'] = "Peminjaman berhasil dibatalkan.";
            header("Location: detail_buku.php?id=$id_buku");
            exit();
        }
    }
}

// Proses Pengajuan Pinjam
if (isset($_POST['request_pinjam'])) {
    $q_cek_pinjam = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_user = $id_user AND id_buku = $id_buku AND status NOT IN ('selesai', 'dibatalkan')");
    $q_buku_stok = mysqli_query($conn, "SELECT stok_tersedia FROM books WHERE id_buku = $id_buku");
    $buku_stok = mysqli_fetch_assoc($q_buku_stok)['stok_tersedia'];

    if (mysqli_num_rows($q_cek_pinjam) == 0 && $buku_stok > 0) {
        $tanggal_request = date('Y-m-d');
        $q_insert = "INSERT INTO peminjaman (id_user, id_buku, tanggal_request, status, denda) VALUES ($id_user, $id_buku, '$tanggal_request', 'menunggu persetujuan', 0)";
        
        if (mysqli_query($conn, $q_insert)) {
            mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia - 1 WHERE id_buku = $id_buku");
            $_SESSION['success'] = "Pengajuan pinjam berhasil dikirim!";
            header("Location: detail_buku.php?id=$id_buku");
            exit();
        }
    }
}

// Ambil Data
$q_buku = mysqli_query($conn, "SELECT * FROM books WHERE id_buku = $id_buku");
if (mysqli_num_rows($q_buku) === 0) {
    header("Location: katalog.php");
    exit();
}
$buku = mysqli_fetch_assoc($q_buku);

$q_cek_pinjam = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_user = $id_user AND id_buku = $id_buku AND status NOT IN ('selesai', 'dibatalkan')");
$ada_peminjaman = mysqli_num_rows($q_cek_pinjam) > 0;
$data_pinjam = $ada_peminjaman ? mysqli_fetch_assoc($q_cek_pinjam) : null;

$query_cat = mysqli_query($conn, "SELECT DISTINCT kategori FROM books WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");

include '../includes/header.php';
?>

<div class="catalog-toolbar">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari judul buku, deskripsi, atau kategori..." onkeypress="if(event.key==='Enter'){ window.location.href='katalog.php?q='+encodeURIComponent(this.value); }">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>
    <div class="category-dropdown">
        <button type="button" id="categoryBtn" class="category-btn" onclick="toggleCategoryMenu()">
            <i class="fa-solid fa-layer-group"></i> <span>Kategori</span> <i class="fa-solid fa-chevron-down" style="font-size: 11px;"></i>
        </button>
        <div id="categoryMenu" class="category-menu">
            <?php while ($cat = mysqli_fetch_assoc($query_cat)): ?>
                <a href="katalog.php?cat=<?= urlencode($cat['kategori']); ?>" class="category-item"><?= htmlspecialchars($cat['kategori']); ?></a>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-msg"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="book-detail-container">
    <div class="book-detail-cover">
        <?php if (!empty($buku['gambar'])): ?>
            <img src="../assets/img/buku/<?= $buku['gambar']; ?>" alt="Cover Buku">
        <?php else: ?>
            <i class="fa-solid fa-book placeholder-icon"></i>
        <?php endif; ?>
    </div>

    <div class="book-detail-content">
        <a href="katalog.php?cat=<?= urlencode($buku['kategori']); ?>" class="book-detail-category">
            <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($buku['kategori']); ?>
        </a>

        <h1 class="book-detail-title"><?= htmlspecialchars($buku['judul']); ?></h1>
        <p class="book-detail-author">Penulis: <strong><?= htmlspecialchars($buku['penulis']); ?></strong></p>

        <div class="book-detail-desc">
            <h3>Deskripsi Buku</h3>
            <p><?= nl2br(htmlspecialchars($buku['deskripsi'])); ?></p>
        </div>

        <div class="book-detail-stock">
            <span class="stock-status <?= $buku['stok_tersedia'] > 0 ? 'stock-available' : 'stock-empty'; ?>">
                <?= $buku['stok_tersedia'] > 0 ? 'Tersedia: ' . $buku['stok_tersedia'] : 'Tidak Tersedia'; ?>
            </span>
        </div>

        <div class="book-detail-action" style="display: flex; gap: 15px; align-items: center;">
            <?php if ($ada_peminjaman): ?>
                <?php 
                $st = $data_pinjam['status'];
                $badge_class = 'badge-yellow'; $icon = 'fa-clock';
                if ($st == 'siap diambil') { $badge_class = 'badge-sky'; $icon = 'fa-box-archive'; }
                elseif ($st == 'dipinjam') { $badge_class = 'badge-indigo'; $icon = 'fa-book-reader'; }
                ?>
                <span class="status-badge lg <?= $badge_class; ?>">
                    <i class="fa-solid <?= $icon; ?>"></i> <?= ucfirst($st); ?>
                </span>

                <?php if ($st == 'menunggu persetujuan' || $st == 'siap diambil'): ?>
                    <form action="" method="POST">
                        <input type="hidden" name="id_peminjaman" value="<?= $data_pinjam['id_peminjaman']; ?>">
                        <button type="submit" name="batal_pinjam" class="btn-danger" style="padding: 10px 20px; font-size: 13px;" onclick="return confirm('Yakin ingin membatalkan pengajuan pinjaman ini?');">
                            <i class="fa-solid fa-xmark"></i> Batal Pinjam
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($buku['stok_tersedia'] > 0): ?>
                    <form action="" method="POST">
                        <button type="submit" name="request_pinjam" class="btn-primary btn-lg" onclick="return confirm('Apakah Anda yakin ingin mengajukan peminjaman buku ini?');">
                            <i class="fa-solid fa-paper-plane"></i> Request Pinjam
                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" class="btn-disabled btn-lg" disabled>Stok Tidak Tersedia</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>