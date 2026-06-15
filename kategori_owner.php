<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$pesan = '';

// Tambah Kategori
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_kategori'];
    $query = mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    if ($query) $pesan = "<div class='alert-success'>Kategori berhasil ditambahkan!</div>";
}

// Hapus Kategori
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = '$id'");
    header("Location: kategori_owner.php");
}
// $id = $_GET['id'];

// $cek = mysqli_query($conn, "SELECT * FROM barang WHERE id_kategori='$id'");

// if(mysqli_num_rows($cek) > 0){
//     echo "Kategori tidak bisa dihapus karena masih digunakan pada data barang.";
// } else {
//     mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");
//     echo "Kategori berhasil dihapus";
// }

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kategori - EOQ Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert-success {
            background: #10b981;
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
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
                <div class="sidebar-user-role" style="text-transform: uppercase;"><?= $_SESSION['role']; ?></div>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-title">Menu Utama</li>
                <li><a href="dashboard_owner.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="produk_owner.php"><i class="fa-solid fa-box"></i> Data Barang</a></li>
                <li><a href="kategori_owner.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
                <!-- <li><a href="pos.php"><i class="fa-solid fa-cash-register"></i> Kasir POS</a></li> -->
                <li><a href="eoq.php"><i class="fa-solid fa-calculator"></i> Analisis EOQ</a></li>
                <li><a href="laporan_owner.php"><i class="fa-solid fa-file-lines"></i> Laporan</a></li>
                <li class="sidebar-divider"></li>
                <li class="sidebar-menu-title">Pengaturan</li>
                <li><a href="akun.php"><i class="fa-solid fa-user-gear"></i> Akun Saya</a></li>
                <li><a href="users.php"><i class="fa-solid fa-users"></i> Kelola User</a></li>
                <li><a href="pengaturan.php"><i class="fa-solid fa-gear"></i> Pengaturan</a></li>
                <li class="sidebar-divider"></li>
                <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="content">
            <h2><i class="fa-solid fa-tags"></i> Kategori Barang</h2>
            <?= $pesan; ?>

            <div class="glass-card" style="margin-bottom: 30px;">
                <h3 style="color: white; margin-bottom: 15px;">Tambah Kategori</h3>
                <form method="POST" style="display: flex; gap: 10px;">
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Nama Kategori" required style="flex: 1;">
                    <button type="submit" name="tambah" class="btn-primary">Tambah</button>
                </form>
            </div>

            <div class="glass-card">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($k = mysqli_fetch_assoc($kategori)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $k['nama_kategori']; ?></td>
                                <td>
                                    <a href="?hapus=<?= $k['id_kategori']; ?>" onclick="return confirm('Yakin hapus?')" style="color: #ef4444;"><i class="fa-solid fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>
