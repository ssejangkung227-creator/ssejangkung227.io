<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }
include 'koneksi.php';

$pesan = '';

// Tambah Barang
if (isset($_POST['tambah'])) {
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $kat = $_POST['id_kategori'];
    $beli = $_POST['harga_beli'];
    $jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $supplier = $_POST['supplier'];

    $query = mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, id_kategori, harga_beli, harga_jual, stok, supplier) 
    VALUES ('$kode', '$nama', '$kat', '$beli', '$jual', '$stok', '$supplier')");

    if ($query) {
        $pesan = "<div class='alert-success'>Barang berhasil ditambahkan!</div>";
    } else {
        $pesan = "<div class='alert-error'>Gagal menambahkan barang.</div>";
    }
}

// Hapus Barang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");
    header("Location: produk_owner.php");
}

// Ambil data untuk edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'"));
}

// Update Barang
if (isset($_POST['update'])) {
    $id = $_POST['id_barang'];
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $kat = $_POST['id_kategori'];
    $beli = $_POST['harga_beli'];
    $jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $supplier = $_POST['supplier'];

    mysqli_query($conn, "UPDATE barang SET kode_barang='$kode', nama_barang='$nama', id_kategori='$kat', harga_beli='$beli', harga_jual='$jual', stok='$stok', supplier='$supplier' WHERE id_barang='$id'");
    header("Location: produk_owner.php");
}

// Pagination & Search
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;

$where = "";
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $where = "WHERE nama_barang LIKE '%$cari%' OR kode_barang LIKE '%$cari%'";
}

$query_barang = mysqli_query($conn, "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.id_kategori = k.id_kategori $where ORDER BY b.id_barang DESC LIMIT $start, $limit");
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$pages = ceil($total / $limit);

$kategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - EOQ Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert-success { background: #10b981; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        .alert-error { background: #ef4444; color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        .modal { display: <?= $edit ? 'flex' : 'none'; ?>; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #1e293b; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; }
        .search-box { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-box input { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white; }
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
            <h2>Data Barang</h2>
            
            <?= $pesan; ?>

            <!-- Form Tambah -->
            <div class="glass-card" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 15px; color: white;">Tambah Barang Baru</h3>
                <form method="POST">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div class="form-group">
                            <label style="color: #94a3b8;">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" placeholder="BRG001" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Nama Produk" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Kategori</label>
                            <select name="id_kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                                <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Harga Jual</label>
                            <input type="number" name="harga_jual" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #94a3b8;">Supplier</label>
                            <input type="text" name="supplier" class="form-control" placeholder="Nama Supplier">
                        </div>
                    </div>
                    <button type="submit" name="tambah" class="btn-primary" style="margin-top: 15px;">Tambah Barang</button>
                </form>
            </div>

            <!-- Tabel Data -->
            <div class="glass-card">
                <div class="search-box">
                    <form method="GET" style="display: flex; width: 100%; gap: 10px;">
                        <input type="text" name="cari" placeholder="Cari barang..." value="<?= isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
                        <button type="submit" class="btn-primary"><i class="fa-solid fa-search"></i></button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Supplier</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = $start + 1; while ($b = mysqli_fetch_assoc($query_barang)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $b['kode_barang']; ?></td>
                                <td><?= $b['nama_barang']; ?></td>
                                <td><?= $b['nama_kategori']; ?></td>
                                <td>Rp <?= number_format($b['harga_beli'], 0, ',', '.'); ?></td>
                                <td>Rp <?= number_format($b['harga_jual'], 0, ',', '.'); ?></td>
                                <td style="color: <?= $b['stok'] < 10 ? '#ef4444' : 'white'; ?>;"><?= $b['stok']; ?></td>
                                <td><?= $b['supplier']; ?></td>
                                <td>
                                    <a href="?edit=<?= $b['id_barang']; ?>" style="color: #06b6d4; margin-right: 10px;"><i class="fa-solid fa-edit"></i></a>
                                    <a href="?hapus=<?= $b['id_barang']; ?>" style="color: #ef4444;" onclick="return confirm('Yakin hapus?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?page=<?= $i; ?>" style="padding: 8px 15px; background: <?= $page == $i ? '#06b6d4' : '#334155'; ?>; color: white; border-radius: 5px; text-decoration: none;"><?= $i; ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Edit -->
    <div class="modal">
        <div class="modal-content">
            <h3 style="color: white; margin-bottom: 20px;">Edit Barang</h3>
            <?php if ($edit): ?>
            <form method="POST">
                <input type="hidden" name="id_barang" value="<?= $edit['id_barang']; ?>">
                <div class="form-group">
                    <label style="color: #94a3b8;">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" value="<?= $edit['kode_barang']; ?>" required>
                </div>
                <div class="form-group">
                    <label style="color: #94a3b8;">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="<?= $edit['nama_barang']; ?>" required>
                </div>
                <div class="form-group">
                    <label style="color: #94a3b8;">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $edit['stok']; ?>" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="update" class="btn-primary">Update</button>
                    <a href="produk_owner.php" class="btn-primary" style="background: #ef4444;">Batal</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
