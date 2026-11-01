<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: anggota.php");
    exit();
}

$id = (int)$_GET['id'];
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id AND role = 'admin'");

if (mysqli_num_rows($query_admin) === 0) {
    header("Location: anggota.php");
    exit();
}

$admin = mysqli_fetch_assoc($query_admin);

if (isset($_POST['simpan'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND id_user != $id");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah digunakan oleh akun lain.";
    } else {
        $query_update = "UPDATE users SET nama = '$nama', email = '$email', password = '$password' WHERE id_user = $id";

        if (mysqli_query($conn, $query_update)) {
            $_SESSION['success'] = "Data admin berhasil diperbarui!";
            header("Location: anggota.php");
            exit();
        } else {
            $error = "Gagal memperbarui data admin: " . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
?>

<div class="form-container">
    <h2 class="form-title">EDIT ADMIN</h2>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?= $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($admin['nama']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">NIM / Password</label>
            <input type="text" id="password" name="password" class="form-control" value="<?= htmlspecialchars($admin['password']); ?>" required>
        </div>

        <div class="form-actions">
            <a href="anggota.php" class="btn-secondary">Batal</a>
            <button type="submit" name="simpan" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>