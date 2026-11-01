<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");

include '../includes/header.php';
?>

<div class="page-header">
    <h2>Daftar Anggota</h2>
    <a href="anggota_admin-add.php" class="btn-primary">+ Tambah Admin</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <p class="success-msg"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table id="tabelAnggota" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>NIM</th>
                <th>Role</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($query_users)): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['password']); ?></td>
                <td><?= ucfirst(htmlspecialchars($row['role'])); ?></td>
                <td class="action-cell">
                    <?php if ($row['role'] === 'admin'): ?>
                        <a href="anggota_admin-edit.php?id=<?= $row['id_user']; ?>" class="action-btn text-blue" title="Edit Admin">
                            <i class="fa-solid fa-pen-to-square"></i>
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