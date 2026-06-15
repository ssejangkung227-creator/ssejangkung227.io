<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Redirect jika kasir
if ($_SESSION['role'] == 'kasir') {
    header("Location: dashboard_kasir.php");
    exit;
}

include 'koneksi.php';

$pesan = '';

// Tambah User
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT username FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = '<div class="alert alert-danger">Username sudah ada!</div>';
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')");
        $pesan = '<div class="alert alert-success">User berhasil ditambahkan!</div>';
    }
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id != $_SESSION['id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $pesan = '<div class="alert alert-success">User berhasil dihapus!</div>';
    }
    header("Location: users.php");
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - EOQ Sistem</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="grid-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-cubes-stacked"></i>
                <h3>EOQ SYSTEM KITTE</h3>
            </div>
            <div class="sidebar-user">
                <div class="sidebar-user-avatar"><i class="fa-solid fa-user"></i></div>
                <div class="sidebar-user-name"><?= $_SESSION['nama']; ?></div>
                <div class="sidebar-user-role"><?= strtoupper($_SESSION['role']); ?></div>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-title">Menu Utama</li>
                <li><a href="dashboard_owner.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="produk_owner.php"><i class="fa-solid fa-box"></i> Data Barang</a></li>
                <li><a href="kategori_owner.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
                <li><a href="pos.php"><i class="fa-solid fa-cash-register"></i> Kasir POS</a></li>
                <li><a href="eoq.php"><i class="fa-solid fa-calculator"></i> Analisis EOQ</a></li>
                <li><a href="laporan_owner.php"><i class="fa-solid fa-file-lines"></i> Laporan</a></li>
                <li class="sidebar-divider"></li>
                <li class="sidebar-menu-title">Pengaturan</li>
                <li><a href="akun.php"><i class="fa-solid fa-user-gear"></i> Akun Saya</a></li>
                <li><a href="users.php"><i class="fa-solid fa-users"></i> Kelola User</a></li>
                <li><a href="pengaturan.php"><i class="fa-solid fa-gear"></i> Pengaturan</a></li>
                <li class="sidebar-divider"></li>
                <li><a href="../logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="fa-solid fa-users"></i> Kelola User</h1>
                    <p class="page-subtitle">Tambah, edit, atau hapus pengguna sistem</p>
                </div>
            </div>

            <?= $pesan; ?>

            <div class="stats-grid">
                <!-- Tambah User -->
                <div class="glass-card">
                    <h3 class="glass-card-title"><i class="fa-solid fa-user-plus"></i> Tambah User Baru</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama user" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control">
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                                <option value="kasir">Kasir</option>
                            </select>
                        </div>
                        <button type="submit" name="tambah" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah User</button>
                    </form>
                </div>

                <!-- Daftar User -->
                <div class="glass-card">
                    <h3 class="glass-card-title"><i class="fa-solid fa-list"></i> Daftar User</h3>
                    <div style="overflow-x:auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($u = mysqli_fetch_assoc($users)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $u['nama']; ?></td>
                                        <td><code><?= $u['username']; ?></code></td>
                                        <td>
                                            <span class="eoq-badge <?= $u['role'] == 'admin' ? 'danger' : ($u['role'] == 'owner' ? 'warning' : 'success'); ?>">
                                                <?= strtoupper($u['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($u['id'] != $_SESSION['id']): ?>
                                                <a href="?hapus=<?= $u['id']; ?>" class="table-action-btn delete" onclick="return confirm('Hapus user ini?')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check" style="color:var(--success);"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>