<?php
session_start();
require '../config/db.php';

// Jika tidak ada email yang sedang diverifikasi, kembalikan ke register
if (!isset($_SESSION['email_verifikasi'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['email_verifikasi'];

if (isset($_POST['verify'])) {
    $otp_input = $_POST['otp'];

    // Ambil OTP dari database untuk email yang sedang diverifikasi
    $query = mysqli_query($conn, "SELECT otp FROM users WHERE email = '$email'");
    if ($row = mysqli_fetch_assoc($query)) {
        if ($row['otp'] === $otp_input) {

            // Update status verifikasi dan kosongkan OTP
            $update = mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL WHERE email = '$email'");

            if ($update) {
                unset ($_SESSION['email_verifikasi']); // Hapus session email verifikasi
                $_SESSION['success'] = "Akun berhasil diverifikasi. Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Terjadi kesalahan saat memverifikasi akun. Silakan coba lagi.";
            }
        } else {
            $error = "Kode OTP salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="profile-icon">
            <img src="../assets/img/user.png" alt="User Icon">
        </div>

        <h2>Verifikasi Akun</h2>
        <p class="auto-link" style="margin-bottom: 20px;">
            Masukkan 6 digit kode OTP yang telah dikirim ke email<br><b><?= $email; ?></b>
        </p>

        <?php if (isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <input type="text" id="otp" name="otp" placeholder="6 Digit Kode OTP" required>
            </div>

            <button type="submit" name="verify" class="btn-primary">Verifikasi</button>
        </form>
    </div>
</body>
</html>