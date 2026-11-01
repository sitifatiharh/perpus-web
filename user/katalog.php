<?php
session_start();
require '../config/db.php';

// Proteksi Halaman Akses Mahasiswa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit();
}

// Mengambil semua data buku dari database
$query_books = mysqli_query($conn, "SELECT * FROM books ORDER BY kategori ASC, id_buku DESC");

// Kelompokkan data buku berdasarkan Kategori
$books_by_category = [];
while ($row = mysqli_fetch_assoc($query_books)) {
    $cat = !empty($row['kategori']) ? $row['kategori'] : 'Lainnya';
    $books_by_category[$cat][] = $row;
}

include '../includes/header.php';
?>

<!-- Toolbar Search & Dropdown Kategori -->
<div class="catalog-toolbar">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari judul buku, deskripsi, atau kategori..." onkeyup="filterBooks()">
        <i class="fa-solid fa-magnifying-glass"></i>
    </div>

    <!-- PENTING: Pembungkus dan tombol dropdown kategori -->
    <div class="category-dropdown">
        <button type="button" id="categoryBtn" class="category-btn" onclick="toggleCategoryMenu()">
            <i class="fa-solid fa-layer-group"></i>
            <span>Kategori</span>
            <i class="fa-solid fa-chevron-down" style="font-size: 11px;"></i>
        </button>
        <div id="categoryMenu" class="category-menu">
            <?php 
            $i = 0;
            foreach ($books_by_category as $catName => $listOfBooks): 
            ?>
                <a href="javascript:void(0)" class="category-item" onclick="jumpToCategory('cat-<?= $i; ?>')">
                    <?= htmlspecialchars($catName); ?> (<?= count($listOfBooks); ?>)
                </a>
            <?php 
            $i++;
            endforeach; 
            ?>
        </div>
    </div>
</div>

<!-- CONTAINER MODE NORMAL (ACCORDION PER KATEGORI) -->
<div id="accordionContainer">
    <?php if (empty($books_by_category)): ?>
        <p class="no-img" style="text-align: center; padding: 40px 0;">Belum ada koleksi buku yang tersedia.</p>
    <?php else: ?>
        <?php 
        $i = 0;
        foreach ($books_by_category as $catName => $listOfBooks): 
        ?>
            <div class="accordion-item" id="cat-<?= $i; ?>">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <div class="accordion-title">
                        <i class="fa-solid fa-layer-group" style="font-size: 14px; color: #4a69bd;"></i>
                        <span><?= htmlspecialchars($catName); ?></span>
                        <span class="accordion-count">(<?= count($listOfBooks); ?>)</span>
                    </div>
                    <i class="fa-solid fa-chevron-down accordion-icon"></i>
                </div>
                <div class="accordion-body">
                    <div class="book-grid">
                        <?php foreach ($listOfBooks as $buku): ?>
                            <a href="detail_buku.php?id=<?= $buku['id_buku']; ?>" 
                                class="book-card book-card-source" 
                                data-judul="<?= htmlspecialchars($buku['judul']); ?>"
                                data-deskripsi="<?= htmlspecialchars($buku['deskripsi']); ?>"
                                data-kategori="<?= htmlspecialchars($buku['kategori']); ?>">
                                    
                                    <!-- Sampul Gambar (Kiri) -->
                                    <div class="book-cover-wrap">
                                        <?php if (!empty($buku['gambar'])): ?>
                                            <img src="../assets/img/buku/<?= $buku['gambar']; ?>" alt="Cover" class="book-cover-img">
                                        <?php else: ?>
                                            <i class="fa-solid fa-book book-cover-placeholder"></i>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Informasi Teks (Kanan) -->
                                    <div class="book-card-info">
                                        <h4 class="book-card-title"><?= htmlspecialchars($buku['judul']); ?></h4>
                                        <p class="book-card-desc"><?= htmlspecialchars($buku['deskripsi']); ?></p>
                                        
                                        <span class="stock-status <?= $buku['stok_tersedia'] > 0 ? 'stock-available' : 'stock-empty'; ?>">
                                            <?= $buku['stok_tersedia'] > 0 ? 'Tersedia: ' . $buku['stok_tersedia'] : 'Tidak Tersedia'; ?>
                                        </span>
                                    </div>
                                </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php 
        $i++;
        endforeach; 
        ?>
    <?php endif; ?>
</div>

<!-- CONTAINER MODE SEARCH (GRID RATAN DARI SELURUH KATEGORI) -->
<div id="searchResultsContainer" style="display: none;">
    <h3 style="color: #0f2c59; margin-bottom: 15px; font-size: 18px;">Hasil Pencarian</h3>
    <div id="searchResultsGrid" class="search-results-grid"></div>
</div>

<?php include '../includes/footer.php'; ?>