<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// 1. Otomais batal request yang lewat 3 hari
$query_auto_batal = mysqli_query($conn, "SELECT id_peminjaman, id_buku FROM peminjaman WHERE status = 'siap diambil' AND tanggal_request < DATE_SUB(CURDATE(), INTERVAL 3 DAY)");

while ($batal = mysqli_fetch_assoc($query_auto_batal)) {
    $id_p = $batal['id_peminjaman'];
    $id_b = $batal['id_buku'];

    mysqli_query($conn, "UPDATE peminjaman SET status = 'dibatalkan' WHERE id_peminjaman = $id_p");
    mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = $id_b");
}

// 2. Proses ubah status peminjaman
if (isset($_POST['update_status'])) {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    $status_baru   = mysqli_real_escape_string($conn, $_POST['status_baru']);

    $q_cek = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id_peminjaman = $id_peminjaman");
    $data_p = mysqli_fetch_assoc($q_cek);
    $id_buku = $data_p['id_buku'];
    $status_lama = $data_p['status'];

    // Validasi hirarki perubahan status
    if ($status_lama !== 'selesai' && $status_lama !== 'dibatalkan') {
        
        if ($status_lama == 'menunggu persetujuan' && $status_baru == 'siap diambil') {
            mysqli_query($conn, "UPDATE peminjaman SET status = 'siap diambil' WHERE id_peminjaman = $id_peminjaman");
            $_SESSION['success'] = "Status berhasil diubah menjadi Siap Diambil!";
        
        } elseif ($status_lama == 'siap diambil' && $status_baru == 'dipinjam') {
            $tanggal_pinjam  = date('Y-m-d');
            $jatuh_tempo = date('Y-m-d', strtotime('+14 days'));
            mysqli_query($conn, "UPDATE peminjaman SET status = 'dipinjam', tanggal_pinjam = '$tanggal_pinjam', jatuh_tempo = '$jatuh_tempo' WHERE id_peminjaman = $id_peminjaman");
            $_SESSION['success'] = "Buku berhasil dipinjamkan!";
        
        } elseif ($status_lama == 'dipinjam' && $status_baru == 'selesai') {
            $tanggal_kembali = date('Y-m-d');
            $denda = 0;

            if (!empty($data_p['jatuh_tempo']) && $tanggal_kembali > $data_p['jatuh_tempo']) {
                $selisih_hari = (strtotime($tanggal_kembali) - strtotime($data_p['jatuh_tempo'])) / (60 * 60 * 24);
                $denda = floor($selisih_hari) * 2000;
            }

            mysqli_query($conn, "UPDATE peminjaman SET status = 'selesai', tanggal_kembali = '$tanggal_kembali', denda = $denda WHERE id_peminjaman = $id_peminjaman");
            mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = $id_buku");
            $_SESSION['success'] = "Transaksi selesai, stok buku berhasil dikembalikan!";
        
        } elseif ($status_baru == 'dibatalkan') {
            mysqli_query($conn, "UPDATE peminjaman SET status = 'dibatalkan' WHERE id_peminjaman = $id_peminjaman");
            mysqli_query($conn, "UPDATE books SET stok_tersedia = stok_tersedia + 1 WHERE id_buku = $id_buku");
            $_SESSION['success'] = "Peminjaman berhasil dibatalkan!";
        }
    }
    header("Location: peminjaman.php");
    exit();
}

// 3. Ambil data peminjaman dengan join users dan books
$query_peminjaman = mysqli_query($conn, "
    SELECT p.*, u.password AS nim, u.nama, b.judul
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id_user
    JOIN books b ON p.id_buku = b.id_buku
    ORDER BY p.id_peminjaman DESC
");

include '../includes/header.php';
?>

<div class="page-header">
    <h2>Peminjaman Buku</h2>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-msg"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table id="tabelPeminjaman" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Judul Buku</th>
                <th>Tgl Request</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl Kembali</th>
                <th>Denda</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_peminjaman)): 
                $nim = $row['nim'];
                $st  = $row['status']
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($nim); ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['judul']); ?></td>
                <td><?= (!empty($row['tanggal_request']) && $row['tanggal_request'] != '0000-00-00') ? date('d/m/Y', strtotime($row['tanggal_request'])) : '-'; ?></td>
                <td><?= (!empty($row['tanggal_pinjam']) && $row['tanggal_pinjam'] != '0000-00-00') ? date('d/m/Y', strtotime($row['tanggal_pinjam'])) : '-'; ?></td>
                <td><?= (!empty($row['jatuh_tempo']) && $row['jatuh_tempo'] != '0000-00-00') ? date('d/m/Y', strtotime($row['jatuh_tempo'])) : '-'; ?></td>
                <td><?= (!empty($row['tanggal_kembali']) && $row['tanggal_kembali'] != '0000-00-00') ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                <td><?= 'Rp ' . number_format($row['denda'], 0, ',', '.'); ?></td>
                <td>
                    <?php if ($st == 'selesai' || $st == 'dibatalkan'): ?>
                        <span class="badge badge-<?= $st == 'selesai' ? 'green' : 'red'; ?>">
                            <?= ucfirst($st); ?>
                        </span>
                    <?php else: ?>
                        <form action="" method="POST" class="form-status">
                            <input type="hidden" name="id_peminjaman" value="<?= $row['id_peminjaman']; ?>">
                            <select name="status_baru" class="select-status" onchange="konfirmasiStatus(this)">
                                <?php if ($st == 'menunggu persetujuan'): ?>
                                    <option value="menunggu persetujuan" selected disabled>Menunggu Persetujuan</option>
                                    <option value="siap diambil">Siap Diambil</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                <?php elseif ($st == 'siap diambil'): ?>
                                    <option value="siap diambil" selected disabled>Siap Diambil</option>
                                    <option value="dipinjam">Dipinjam</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                <?php elseif ($st == 'dipinjam'): ?>
                                    <option value="dipinjam" selected disabled>Dipinjam</option>
                                    <option value="selesai">Selesai</option>
                                <?php endif; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    <?php endif; ?>
                </td>
                <td class="action-cell">
                    <?php if ($st == 'selesai' || $st == 'dibatalkan'): ?>
                        <a href="hapus_peminjaman.php?id=<?= $row['id_peminjaman']; ?>" class="action-btn text-red" title="Hapus" onclick="return confirm('Hapus riwayat peminjaman ini?');">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    <?php else: ?>
                        <span class="no-img">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>