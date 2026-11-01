<?php
session_start();
require '../config/db.php';

// Proteksi halaman khusus admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $kode           = mysqli_real_escape_string($conn, $_POST['kode']);
    $judul          = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis        = mysqli_real_escape_string($conn, $_POST['penulis']);
    $kategori       = mysqli_real_escape_string($conn, $_POST['kategori']);
    $deskripsi      = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $stok_total     = (int)$_POST['stok_total'];
    $stok_tersedia  = (int)$_POST['stok_tersedia'];

    // Proses upload gambar
    $nama_gambar = '';
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != '') {
        $filename   = $_FILES['gambar']['name'];
        $tmp_name   = $_FILES['gambar']['tmp_name'];
        $ext        = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed    = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($ext), $allowed)) {
            // Rename file gambar agar unik
            $nama_gambar = time() . '_' . rand(100, 999) . '.' . $ext;
            $target_path = '../assets/img/buku/' . $nama_gambar;

            move_uploaded_file($tmp_name, $target_path);
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
        }
    }

    if (!isset($error)) {
        $query = "INSERT INTO books (kode, gambar, judul, penulis, kategori, deskripsi, stok_total, stok_tersedia) 
                    VALUES ('$kode', '$nama_gambar', '$judul', '$penulis', '$kategori', '$deskripsi', '$stok_total', '$stok_tersedia')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Buku berhasil ditambahkan!";
            header("Location: buku.php");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">Tambah Buku</h2>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?= $error; ?>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="kode" class="form-control" placeholder="Kode Buku" required>
        </div>

        <div class="form-group">
            <label for="gambar">Pilih Gambar Cover:</label>
            <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
        </div>

        <div class="form-group">
            <input type="text" name="judul" class="form-control" placeholder="Judul Buku" required>
        </div>

        <div class="form-group">
            <input type="text" name="penulis" class="form-control" placeholder="Penulis" required>
        </div>

        <div class="form-group">
            <select name="kategori" class="form-control" required>
                <option value="" disabled selected>-- Pilih Kategori --</option>
                <option value="Karya Umum">Karya Umum</option>
                <option value="Filsafat">Filsafat</option>
                <option value="Psikologi">Psikologi</option>
                <option value="Agama">Agama</option>
                <option value="Ilmu-ilmu Sosial">Ilmu-ilmu Sosial</option>
                <option value="Bahasa">Bahasa</option>
                <option value="MIPA">MIPA</option>
                <option value="Teknologi">Teknologi</option>
                <option value="Seni">Seni</option>
                <option value="Olahraga">Olahraga</option>
                <option value="Sastra">Sastra</option>
                <option value="Geografi">Geografi</option>
                <option value="Sejarah">Sejarah</option>
                <option value="Fiksi">Fiksi</option>
            </select>
        </div>

        <div class="form-group">
            <textarea name="deskripsi" class="form-control" placeholder="Deskripsi" rows="3"></textarea>
        </div>

        <div class="form-group">
            <input type="number" name="stok_total" class="form-control" placeholder="Total Stok" min="1" required>
        </div>

        <div class="form-group">
            <input type="number" name="stok_tersedia" class="form-control" placeholder="Stok Tersedia" min="0" required>
        </div>

        <div class="form-actions">
            <a href="buku.php" class="btn-secondary">Batal</a>
            <button type="submit" name="simpan" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>