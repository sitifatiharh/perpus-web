<?php
session_start();

if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/katalog.php");
    }
} else {
    header("Location: auth/login.php");
}
exit();
?>