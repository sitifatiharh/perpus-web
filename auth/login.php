<?php
session_start();
require '../config/db.php';

// Jika pengguna sudah login, cegah akses ke halaman login
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/katalog.php");
    }
    exit();
}

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);

        // Cek pw
        if ($password === $row['password']) {
            // Cek status verifikasi akun
            if ($row['is_verified'] == 0) {
                $_SESSION['email_verifikasi'] = $email;
                header("Location: verify.php");
                exit();
            }

            // Jika sudah diverifikasi, set session ke login
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            // Redirect berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/katalog.php");
            }
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="profile-icon">
            <!-- Panggil gambar ikon user di sini -->
            <img src="../assets/img/user.png" alt="User Icon">
        </div>

        <h2>Login</h2>

        <!-- Tampilkan pesan sukses dari register.php -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-msg"><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); // Hapus pesan sukses setelah ditampilkan ?>
        <?php endif; ?>

        <!-- Tampilkan pesan error login -->
        <?php if (isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="NIM" required>
            </div>

            <button type="submit" name="login" class="btn-primary">Login</button>
        </form>
        <p class="auth-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
