<?php
session_start();
require '../config/db.php';

// Proteksi halaman khusus admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM books ORDER BY id_buku DESC");

include '../includes/header.php';
?>

<div class="page-header">
    <h2>Katalog Buku</h2>
    <a href="buku_add.php" class="btn-primary">+ Tambah Buku</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-msg"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table id="tabelBuku" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Tersedia</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['kode']); ?></td>
                <td>
                    <?php if(!empty($row['gambar'])): ?>
                        <img src="../assets/img/buku/<?= $row['gambar']; ?>" alt="Cover" class="img-cover">
                    <?php else: ?>
                        <span class="no-img">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['judul']); ?></td>
                <td><?= htmlspecialchars($row['penulis']); ?></td>
                <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                <td><?= htmlspecialchars($row['kategori']); ?></td>
                <td class="text-center"><?= $row['stok_total']; ?></td>
                <td class="text-center"><?= $row['stok_tersedia']; ?></td>
                <td class="action-cell">
                    <a href="buku_edit.php?id=<?= $row['id_buku']; ?>" class="action-btn text-blue" title="edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <a href="buku_delete.php?id=<?= $row['id_buku']; ?>" class="action-btn text-red" title="hapus" onclick="return confirm('Yakin ingin menghapus buku ini?');">
                        <i class="fa-solid fa-trash-can"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>