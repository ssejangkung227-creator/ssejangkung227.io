<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// Redirect jika bukan kasir
// if ($_SESSION['role'] != 'kasir') { header("Location: dashboard_kasir.php"); exit; }

include 'koneksi.php';
$hari_ini = mysqli_query($conn, "SELECT * FROM transaksi WHERE DATE(tanggal) = CURDATE() AND id_user = ".$_SESSION['id']);
$transaksi_hari_ini = mysqli_num_rows($hari_ini);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Kasir - EOQ Sistem</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-modern">
<div style="max-width:800px;margin:0 auto;padding:20px;">
<!-- Header Kasir -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid var(--glass-border);">
<div>
<h2 style="color:white;"><i class="fa-solid fa-cash-register"></i> Kasir POS</h2>
<p style="color:#94a3b8;"><?= $_SESSION['nama']; ?> - Kasir</p>
</div>
<a href="pos.php" class="btn-primary" style="padding:15px 30px;font-size:1.1rem;">
<i class="fa-solid fa-calculator"></i> BUKA KASIR
</a>
</div>

<!-- Stats -->
<div class="stats-grid">
<div class="glass-card" style="text-align:center;">
<i class="fa-solid fa-receipt" style="font-size:2.5rem;color:var(--accent);margin-bottom:15px;"></i>
<h3 style="font-size:2.5rem;color:white;"><?= $transaksi_hari_ini; ?></h3>
<p style="color:#94a3b8;">Transaksi Hari Ini</p>
</div>

<div class="glass-card" style="text-align:center;">
<i class="fa-solid fa-box" style="font-size:2.5rem;color:var(--success);margin-bottom:15px;"></i>
<h3 style="font-size:2.5rem;color:white;">
<?php
$barang_stok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok) as total FROM barang"));
echo $barang_stok['total'] ?: 0;
?>
</h3>
<p style="color:#94a3b8;">Total Stok Barang</p>
</div>
</div>

<!-- Quick Links -->
<div class="glass-card" style="margin-top:25px;">
<h3 style="color:white;margin-bottom:20px;">Menu Kasir</h3>
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:15px;">
<a href="pos.php" class="btn-primary" style="text-align:center;padding:20px;">
<i class="fa-solid fa-cash-register" style="font-size:1.5rem;margin-bottom:10px;display:block;"></i>
Kasir / Jual
</a>
<a href="logout.php" class="btn-secondary" style="text-align:center;padding:20px;color:var(--danger);">
<i class="fa-solid fa-right-from-bracket" style="font-size:1.5rem;margin-bottom:10px;display:block;"></i>
Selesai Kerja
</a>
</div>
</div>

<footer style="text-align:center;margin-top:40px;color:#64748b;">
<p>&copy; 2026 EOQ Sistem UMKMPerigi Limus</p>
</footer>
</div>
</body>
</html>