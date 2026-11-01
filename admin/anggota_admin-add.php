<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar dalam sistem.";
    } else {
        $query_insert = "INSERT INTO users (nama, email, password, role, is_verified) 
                        VALUES ('$nama', '$email', '$password', 'admin', 1)";

        if (mysqli_query($conn, $query_insert)) {
            $_SESSION['success'] = "Akun admin baru berhasil ditambahkan!";
            header("Location: anggota.php");
            exit();
        } else {
            $error = "Gagal menambahkan admin: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">TAMBAH ADMIN</h2>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?= $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama Lengkap Admin" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Email Admin" required>
        </div>

        <div class="form-group">
            <label for="password">NIM</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password atau Kode Admin" required>
        </div>

        <div class="form-actions">
            <a href="anggota.php" class="btn-secondary">Batal</a>
            <button type="submit" name="simpan" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>