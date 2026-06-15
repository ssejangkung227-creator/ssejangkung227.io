<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: ../login.php"); exit; }
include 'koneksi.php';

// Ambil data transaksi untuk analisis
$transaksi = mysqli_query($conn, "
    SELECT b.id_barang, b.nama_barang, b.stok,
    COALESCE(SUM(dt.qty), 0) as total_qty 
    FROM barang b
    LEFT JOIN detail_transaksi dt ON b.id_barang = dt.id_barang 
    GROUP BY b.id_barang
    ORDER BY total_qty DESC
");

// Inisialisasi array analisis
$analisis = [];
while ($t = mysqli_fetch_assoc($transaksi)) {
    if($t['total_qty'] > 0) {
        // DATA UNTUK PERHITUNGAN EOQ
        // D = Demand (Permintaan Tahunan) - diasumsikan data transaksi 1 bulan, jadi dikali 12
        $D = $t['total_qty'] * 12; 
        // S = Biaya Pemesanan (Order Cost) - perkiraan
        $S = 50000; 
        // H = Biaya Penyimpanan (Holding Cost) - perkiraan 10% dari harga beli rata2
        $H = 5000; 

        // Rumus EOQ: sqrt(2DS / H)
        $eoq = sqrt((2 * $D * $S) / $H);
        $eoq = round($eoq);

        // Safety Stock: (Permintaan Max x Lead Time) - diasumsikan lead time 7 hari
        $safety_stock = $D / 365 * 7;

        // Reorder Point: (Demand/Hari x Lead Time)
        $reorder_point = ($D / 365) * 7;

        // Frekuensi Pemesanan per Tahun
        $frekuensi = $D / $eoq;

        $analisis[] = [
            'nama' => $t['nama_barang'],
            'demand' => $D,
            'stok_saat_ini' => $t['stok'],
            'eoq' => $eoq,
            'safety_stock' => round($safety_stock),
            'reorder_point' => round($reorder_point),
            'frekuensi' => round($frekuensi),
            'rekomendasi' => ($t['stok'] < $reorder_point) ? 'Segera Pesan' : 'Stok Aman'
        ];
    }
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis EOQ - EOQ Sistem</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .eoq-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; }
        .badge-danger { background: #ef4444; color: white; }
        .badge-success { background: #10b981; color: white; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px; }
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
                <li><a href="../logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>


        <main class="content">
            <h2><i class="fa-solid fa-calculator"></i> Analisis EOQ (Economic Order Quantity)</h2>
            
            <!-- Penjelasan Rumus -->
            <div class="glass-card" style="margin-bottom: 30px;">
                <h3 style="color: white; margin-bottom: 15px;">Tentang Metode EOQ</h3>
                <p style="color: #94a3b8; margin-bottom: 10px;">
                    <strong>EOQ (Economic Order Quantity)</strong> adalah jumlah kuantitas pesanan yang optimal untuk memperbaiki biaya persediaan. Metode ini membantu menentukan kapan harus memesan dan berapa banyak jumlah pesanan yang tepat untuk meminimalkan biaya total.
                </p>
                <div style="background: #0f172a; padding: 15px; border-radius: 8px;">
                    <code style="color: #06b6d4; font-size: 1.2rem;">
                        EOQ = √(2DS / H)
                    </code>
                    <div style="margin-top: 10px; color: #94a3b8; font-size: 0.9rem;">
                        D = Demand/Permintaan Tahunan | S = Biaya Pemesanan (Rp 50.000) | H = Biaya Penyimpanan (Rp 5.000)
                    </div>
                </div>
            </div>

            <!-- Tabel Analisis EOQ -->
            <div class="glass-card">
                <h3 style="color: white; margin-bottom: 20px;">Hasil Analisis & Rekomendasi Pemesanan</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Demand Tahunan</th>
                                <th>Stok Saat Ini</th>
                                <th>EOQ Optimal</th>
                                <th>Safety Stock</th>
                                <th>Reorder Point</th>
                                <th>Frekuensi Order/Tahun</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analisis as $data): ?>
                            <tr>
                                <td><?= $data['nama']; ?></td>
                                <td><?= number_format($data['demand']); ?></td>
                                <td style="color: <?= $data['stok_saat_ini'] < $data['reorder_point'] ? '#ef4444' : 'white'; ?>;"><?= $data['stok_saat_ini']; ?></td>
                                <td style="font-weight: bold; color: #06b6d4;"><?= $data['eoq']; ?></td>
                                <td><?= $data['safety_stock']; ?></td>
                                <td><?= $data['reorder_point']; ?></td>
                                <td><?= $data['frekuensi']; ?>x</td>
                                <td>
                                    <?php if($data['rekomendasi'] == 'Segera Pesan'): ?>
                                    <span class="eoq-badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= $data['rekomendasi']; ?></span>
                                    <?php else: ?>
                                    <span class="eoq-badge badge-success"><i class="fa-solid fa-check"></i> <?= $data['rekomendasi']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Grafik Visualisasi -->
            <div class="card-grid">
                <div class="glass-card">
                    <h3 style="color: white; margin-bottom: 15px;">Visualisasi EOQ</h3>
                    <canvas id="eoqChart"></canvas>
                </div>
                <div class="glass-card">
                    <h3 style="color: white; margin-bottom: 15px;">Status Stok</h3>
                    <canvas id="stokChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Data untuk Grafik
        const eoqLabels = <?= json_encode(array_column($analisis, 'nama')); ?>;
        const eoqData = <?= json_encode(array_column($analisis, 'eoq')); ?>;
        const stokData = <?= json_encode(array_column($analisis, 'stok_saat_ini')); ?>;

        // Grafik EOQ
        new Chart(document.getElementById('eoqChart'), {
            type: 'bar',
            data: {
                labels: eoqLabels,
                datasets: [{
                    label: 'Jumlah EOQ Optimal',
                    data: eoqData,
                    backgroundColor: '#06b6d4',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: 'white' } } },
                scales: { y: { grid: { color: '#334155' }, ticks: { color: 'white' } } }
            }
        });

        // Grafik Stok
        new Chart(document.getElementById('stokChart'), {
            type: 'doughnut',
            data: {
                labels: ['Stok Aman', 'Stok Menipis'],
                datasets: [{
                    data: [
                        <?= count(array_filter($analisis, fn($a) => $a['rekomendasi'] == 'Stok Aman')); ?>,
                        <?= count(array_filter($analisis, fn($a) => $a['rekomendasi'] == 'Segera Pesan')); ?>
                    ],
                    backgroundColor: ['#10b981', '#ef4444']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: 'white' } } }
            }
        });
    </script>
</body>
</html>