<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}
include 'koneksi.php';

// Redirect jika kasir
// if ($_SESSION['role'] == 'owner') { header("Location: dashboard_owner.php"); exit; }

$total_barang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi WHERE DATE(tanggal) = CURDATE()"));
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()"));
$stok_low = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10"));

$bulan_ini = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE MONTH(tanggal) = MONTH(NOW())");
$pendapatan_bulan = mysqli_fetch_assoc($bulan_ini);

// Pengguna
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner - EOQ Sistem</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="fa-solid fa-chart-line"></i> Dashboard Owner</h1>
                    <p class="page-subtitle">Selamat datang, <?= $_SESSION['nama']; ?>! Inilah overview bisnis toko Anda.</p>
                </div>
                <div style="display:flex;gap:10px;">
                    <!-- <a href="pos.php" class="btn-primary"><i class="fa-solid fa-cash-register"></i> Kasir</a> -->
                    <a href="laporan_owner.php" class="btn-primary"><i class="fa-solid fa-chart-column"></i> Laporan</a> <br><br>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <p style="color:#94a3b8;">Total Barang</p>
                            <div class="stat-card-value"><?= $total_barang; ?></div>
                        </div>
                        <i class="fa-solid fa-box" style="font-size:2.5rem;color:var(--accent);opacity:0.5;"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <p style="color:#94a3b8;">Transaksi Hari Ini</p>
                            <div class="stat-card-value text-success"><?= $total_transaksi; ?></div>
                        </div>
                        <i class="fa-solid fa-receipt" style="font-size:2.5rem;color:var(--success);opacity:0.5;"></i>
                    </div>
                </div>

                <div class="stat-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <p style="color:#94a3b8;">Pendapatan Hari Ini</p>
                            <div class="stat-card-value text-success">Rp <?= number_format($total_pendapatan['total'] ?: 0, 0, ',', '.'); ?></div>
                        </div>
                        <i class="fa-solid fa-money-bill" style="font-size:2.5rem;color:var(--success);opacity:0.5;"></i>
                    </div>
                </div>

                <div class="stat-card" style="border-left-color: var(--danger);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <p style="color:#94a3b8;">Stok Menipis</p>
                            <div class="stat-card-value text-danger"><?= $stok_low; ?></div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:2.5rem;color:var(--danger);opacity:0.5;"></i>
                    </div>
                </div>
            </div>

            <!-- Charts & Tables -->
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:25px;margin-top:25px;">
                <div class="glass-card">
                    <h3 class="glass-card-title"><i class="fa-solid fa-chart-bar"></i> Grafik Penjualan Mingguan</h3>
                    <canvas id="salesChart" height="100"></canvas>
                </div>

                <div class="glass-card">
                    <h3 class="glass-card-title"><i class="fa-solid fa-trophy"></i> Produk Terlaris</h3>
                    <?php
                    $terlaris = mysqli_query($conn, "SELECT b.nama_barang, SUM(dt.qty) as terjual FROM detail_transaksi dt JOIN barang b ON dt.id_barang = b.id_barang GROUP BY b.id_barang ORDER BY terjual DESC LIMIT 5");
                    while ($t = mysqli_fetch_assoc($terlaris)):
                    ?>
                        <div style="display:flex;justify-content:space-between;padding:12px;border-bottom:1px solid var(--glass-border);">
                            <span><?= $t['nama_barang']; ?></span>
                            <span style="color:var(--accent);font-weight:600;"><?= $t['terjual']; ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card" style="margin-top:25px;">
                <h3 class="glass-card-title"><i class="fa-solid fa-bolt"></i> Aksi Cepat</h3>
                <div style="display:flex;gap:15px;flex-wrap:wrap;margin-top:15px;">
                    <a href="produk.php?tambah=1" class="btn-primary"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
                    <a href="pos.php" class="btn-primary"><i class="fa-solid fa-cash-register"></i> Kasir POS</a>
                    <a href="eoq.php" class="btn-primary"><i class="fa-solid fa-calculator"></i> Cari EOQ</a>
                    <a href="laporan_owner.php" class="btn-secondary"><i class="fa-solid fa-download"></i> Export Laporan</a>
                </div>
            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [1200000, 1500000, 1100000, 1800000, 2000000, 2500000, 2200000],
                    backgroundColor: '#06b6d4',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#334155'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>