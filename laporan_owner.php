<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$where = "";
if ($filter == 'tanggal') {
    $where = "WHERE DATE(t.tanggal) BETWEEN '$start_date' AND '$end_date'";
}

$laporan = mysqli_query($conn, "
    SELECT t.id_transaksi, t.tanggal, t.total_harga, u.nama as kasir
    FROM transaksi t
    JOIN users u ON t.id_user = u.id
    $where
    ORDER BY t.tanggal DESC
");

$total_pendapatan = 0;
$total_transaksi = mysqli_num_rows($laporan);
while ($l = mysqli_fetch_assoc($laporan)) {
    $total_pendapatan += $l['total_harga'];
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=laporan_penjualan_'.date('Ymd').'.csv');
    $output = fopen("php://output", "w");
    fputcsv($output, ['No', 'Tanggal', 'Kode Transaksi', 'Total', 'Kasir']);
    
    mysqli_data_seek($laporan, 0);
    $no = 1;
    while ($row = mysqli_fetch_assoc($laporan)) {
        fputcsv($output, [$no++, $row['tanggal'], '#'.$row['id_transaksi'], $row['total_harga'], $row['kasir']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - EOQ Sistem</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@media print { .no-print { display: none; } body { background: white; color: black; } }</style>
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
                <li><a href="../logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2><i class="fa-solid fa-file-lines"></i> Laporan Penjualan</h2>
                <div class="no-print">
                    <!-- <button onclick="window.print()" class="btn-primary"><i class="fa-solid fa-print"></i> Print</button> -->
                    <a href="?export=csv" class="btn-primary" style="margin-left: 10px; background: #10b981; text-decoration: none;"><i class="fa-solid fa-file-excel"></i> Export</a>
                </div>
            </div>

            <div class="glass-card no-print" style="margin-bottom: 30px;">
                <form method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
                    <div>
                        <label style="color: #94a3b8;">Filter</label>
                        <select name="filter" class="form-control" onchange="this.form.submit()">
                            <option value="semua">Semua</option>
                            <option value="tanggal" <?= $filter == 'tanggal' ? 'selected' : ''; ?>>Berdasarkan Tanggal</option>
                        </select>
                    </div>
                    <?php if ($filter == 'tanggal'): ?>
                    <div>
                        <label style="color: #94a3b8;">Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date; ?>">
                    </div>
                    <div>
                        <label style="color: #94a3b8;">Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date; ?>">
                    </div>
                    <button type="submit" class="btn-primary">Terapkan</button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <p style="color: #94a3b8;">Total Transaksi</p>
                    <div class="stat-number"><?= $total_transaksi; ?></div>
                </div>
                <div class="stat-card">
                    <p style="color: #94a3b8;">Total Pendapatan</p>
                    <div class="stat-number" style="color: #10b981;">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="glass-card" style="margin-top: 30px;">
                <h3 style="color: white; margin-bottom: 20px;">Detail Transaksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Total</th>
                            <th>Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($laporan, 0);
                        $no = 1;
                        while ($l = mysqli_fetch_assoc($laporan)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($l['tanggal'])); ?></td>
                            <td>#<?= str_pad($l['id_transaksi'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td style="font-weight: bold; color: #10b981;">Rp <?= number_format($l['total_harga'], 0, ',', '.'); ?></td>
                            <td><?= $l['kasir']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>