<?php
session_start(); 
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';


// Ambil statistik
$total_barang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi WHERE DATE(tanggal) = CURDATE()"));
$stok_low = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10"));

// Ambil 5 transaksi terakhir
$query_transaksi = mysqli_query($conn, "SELECT t.*, u.nama FROM transaksi t JOIN users u ON t.id_user = u.id ORDER BY t.id_transaksi DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EOQ Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="grid-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fa-solid fa-cubes-stacked" style="font-size: 2rem; color: #06b6d4;"></i>
                <h3 style="margin-top: 10px; color: white;">EOQ SYSTEM KITTE</h3>
                <p style="font-size: 0.8rem; color: #94a3b8;"><?= $_SESSION['role']; ?></p>
            </div>
            
            <ul>
                <li><a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="produk.php"><i class="fa-solid fa-box"></i> Data Barang</a></li>
                <li><a href="kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a></li>
                <!-- <li><a href="pos.php"><i class="fa-solid fa-cash-register"></i> Kasir (POS)</a></li> -->
                <li><a href="eoq.php"><i class="fa-solid fa-calculator"></i> Analisis EOQ</a></li>
                <li><a href="laporan.php"><i class="fa-solid fa-file-lines"></i> Laporan</a></li>
                <li><a href="logout.php" style="color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2>Dashboard Overview</h2>
                <div style="color: #94a3b8;">Halo, <strong style="color: white;"><?= $_SESSION['nama']; ?></strong></div>
            </div>

            <!-- Statistik Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <p style="color: #94a3b8;">Total Barang</p>
                            <div class="stat-number"><?= $total_barang; ?></div>
                        </div>
                        <i class="fa-solid fa-box" style="font-size: 2rem; color: #06b6d4;"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <p style="color: #94a3b8;">Transaksi Hari Ini</p>
                            <div class="stat-number"><?= $total_transaksi; ?></div>
                        </div>
                        <i class="fa-solid fa-shop" style="font-size: 2rem; color: #10b981;"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <p style="color: #94a3b8;">Stok Menipis</p>
                            <div class="stat-number" style="color: <?= $stok_low > 0 ? '#ef4444' : 'white'; ?>"><?= $stok_low; ?></div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2rem; color: #f59e0b;"></i>
                    </div>
                </div>
            </div>

            <!-- Grafik & Tabel -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Grafik Penjualan -->
                <div class="glass-card">
                    <h3 style="margin-bottom: 20px; color: white;">Grafik Penjualan</h3>
                    <canvas id="salesChart"></canvas>
                </div>

                <!-- Transaksi Terakhir -->
                <div class="glass-card">
                    <h3 style="margin-bottom: 20px; color: white;">Transaksi Terakhir</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Total</th>
                                <th>Kasir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query_transaksi)): ?>
                            <tr>
                                <td>#<?= $row['id_transaksi']; ?></td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td><?= $row['nama']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: [1200000, 1900000, 1500000, 2100000, 1800000, 2500000, 2200000],
                    backgroundColor: '#06b6d4',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#334155' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { labels: { color: 'white' } }
                }
            }
        });
    </script>
</body>
</html>