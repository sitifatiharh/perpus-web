<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: buku.php");
    exit();
}

$id = (int)$_GET['id'];
$query_buku = mysqli_query($conn, "SELECT * FROM books WHERE id_buku = $id");

if (mysqli_num_rows($query_buku) === 0) {
    header("Location: buku.php");
    exit();
}

$buku = mysqli_fetch_assoc($query_buku);

if (isset($_POST['simpan'])) {
    $kode          = mysqli_real_escape_string($conn, $_POST['kode']);
    $judul         = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis       = mysqli_real_escape_string($conn, $_POST['penulis']);
    $kategori      = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi     = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok_total    = (int)$_POST['stok_total'];
    $stok_tersedia = (int)$_POST['stok_tersedia'];

    $nama_gambar = $buku['gambar'];

    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != '') {
        $filename   = $_FILES['gambar']['name'];
        $tmp_name   = $_FILES['gambar']['tmp_name'];
        $ext        = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed    = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ext), $allowed)) {
            $nama_gambar_baru = time() . '_' . rand(100, 999) . '.' . $ext;
            $target_path = '../assets/img/buku/' . $nama_gambar_baru;

            if (move_uploaded_file($tmp_name, $target_path)) {
                if (!empty($buku['gambar']) && file_exists('../assets/img/buku/' . $buku['gambar'])) {
                    unlink('../assets/img/buku/' . $buku['gambar']);
                }
                $nama_gambar = $nama_gambar_baru;
            }
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
        }
    }

    if (!isset($error)) {
        $query_update = "UPDATE books SET 
                            kode = '$kode', 
                            gambar = '$nama_gambar', 
                            judul = '$judul', 
                            penulis = '$penulis', 
                            kategori = '$kategori', 
                            deskripsi = '$deskripsi', 
                            stok_total = $stok_total, 
                            stok_tersedia = $stok_tersedia 
                            WHERE id_buku = $id";

        if (mysqli_query($conn, $query_update)) {
            $_SESSION['success'] = "Data buku berhasil diperbarui!";
            header("Location: buku.php");
            exit();
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">EDIT BUKU</h2>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?= $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="kode">Kode Buku</label>
            <input type="text" id="kode" name="kode" class="form-control" value="<?= htmlspecialchars($buku['kode']); ?>" required>
        </div>

        <div class="form-group">
            <label for="gambar">Ganti Gambar Cover (Opsional):</label>
            <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <label for="judul">Judul Buku</label>
            <input type="text" id="judul" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul']); ?>" required>
        </div>

        <div class="form-group">
            <label for="penulis">Penulis</label>
            <input type="text" id="penulis" name="penulis" class="form-control" value="<?= htmlspecialchars($buku['penulis']); ?>" required>
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" class="form-control" required>
                <option value="Karya Umum" <?= $buku['kategori'] == 'Karya Umum' ? 'selected' : ''; ?>>Karya Umum</option>
                <option value="Filsafat" <?= $buku['kategori'] == 'Filsafat' ? 'selected' : ''; ?>>Filsafat</option>
                <option value="Psikologi" <?= $buku['kategori'] == 'Psikologi' ? 'selected' : ''; ?>>Psikologi</option>
                <option value="Agama" <?= $buku['kategori'] == 'Agama' ? 'selected' : ''; ?>>Agama</option>
                <option value="Ilmu-ilmu Sosial" <?= $buku['kategori'] == 'Ilmu-ilmu Sosial' ? 'selected' : ''; ?>>Ilmu-ilmu Sosial</option>
                <option value="Bahasa" <?= $buku['kategori'] == 'Bahasa' ? 'selected' : ''; ?>>Bahasa</option>
                <option value="MIPA" <?= $buku['kategori'] == 'MIPA' ? 'selected' : ''; ?>>MIPA</option>
                <option value="Teknologi" <?= $buku['kategori'] == 'Teknologi' ? 'selected' : ''; ?>>Teknologi</option>
                <option value="Seni" <?= $buku['kategori'] == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                <option value="Olahraga" <?= $buku['kategori'] == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                <option value="Sastra" <?= $buku['kategori'] == 'Sastra' ? 'selected' : ''; ?>>Sastra</option>
                <option value="Geografi" <?= $buku['kategori'] == 'Geografi' ? 'selected' : ''; ?>>Geografi</option>
                <option value="Sejarah" <?= $buku['kategori'] == 'Sejarah' ? 'selected' : ''; ?>>Sejarah</option>
                <option value="Fiksi" <?= $buku['kategori'] == 'Fiksi' ? 'selected' : ''; ?>>Fiksi</option>
                
            </select>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Singkat</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($buku['deskripsi']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="stok_total">Total Stok</label>
            <input type="number" id="stok_total" name="stok_total" class="form-control" value="<?= $buku['stok_total']; ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="stok_tersedia">Stok Tersedia</label>
            <input type="number" id="stok_tersedia" name="stok_tersedia" class="form-control" value="<?= $buku['stok_tersedia']; ?>" min="0" required>
        </div>

        <div class="form-actions">
            <a href="buku.php" class="btn-secondary">Batal</a>
            <button type="submit" name="simpan" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>