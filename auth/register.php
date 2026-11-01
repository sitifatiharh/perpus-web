<?php
session_start();
require '../config/db.php';

// Memanggil PHPMailer 
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;  

// Jika pengguna sudah login, arahkan ke halaman yang sesuai
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/katalog.php");
    }
    exit();
}

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user'; // Default role untuk pengguna baru

    // Validasi email kampus
    if (!preg_match('/@student\.esaunggul\.ac\.id$/', $email)) {
        $error = "Hanya email kampus (@student.esaunggul.ac.id) yang diizinkan mendaftar.";
    } else {
        // Cek apa email sudah terdaftar
        $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            $otp = sprintf("%06d", mt_rand(0, 999999)); // Generate OTP 6 digi

            // Simpan data pengguna ke database
            $query = "INSERT INTO users (nama, email, password, role, otp, is_verified) VALUES ('$nama', '$email', '$password', '$role', '$otp', 0)";
            
            if (mysqli_query($conn, $query)) {
                // Konfigurasi PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;

                    $mail->Username = 'patriot4089@gmail.com';
                    $mail->Password = 'flgb tgsi vhde xrrt';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('patriot4089@gmail.com', 'Admin Perpus');
                    $mail->addAddress($email, $nama);

                    $mail->isHTML(true);
                    $mail->Subject = 'Kode OTP Verifikasi Akun Perpustakaan';
                    $mail->Body = "Halo $nama,<br><br>Terima kasih telah mendaftar di Sistem Perpustakaan. Berikut adalah kode OTP Anda: <b>$otp</b><br><br>Silakan masukkan kode ini untuk memverifikasi akun Anda.<br><br>Salam,<br>Tim Perpustakaan";

                    $mail->send();

                    // Simpan email ke session sementara untuk dilempar ke form OTP
                    $_SESSION['email_verifikasi'] = $email;
                    header("Location: verify.php");
                    exit();
                } catch (Exception $e) {
                    $error = "Gagal mengirim email verifikasi. Silakan coba lagi. Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Terjadi kesalahan saat registrasi: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="profile-icon">
            <!-- Panggil gambar ikon user di sini -->
            <img src="../assets/img/user.png" alt="User Icon">
        </div>

        <h2>Registrasi</h2>

        <?php if (isset($error)): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
            </div>
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="NIM" required>
            </div>

            <button type="submit" name="register" class="btn-primary">Register</button>
        </form>
        <p class="auth-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>