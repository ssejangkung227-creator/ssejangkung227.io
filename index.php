<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EOQ Sistem - Desa Perigi Limus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-modern">
    <nav class="navbar">
        <div class="logo">
            <h2><i class="fa-solid fa-cubes-stacked"></i> EOQ SYSTEM KITTE</h2>
        </div>
        <div class="nav-links">
            <a href="#about">Tentang</a>
            <a href="#eoq">Cara Kerja</a>
            <a href="#contact">Kontak</a>
            <a href="login.php" class="btn-primary" style="padding: 8px 20px;">Login</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Kelola Stok UMKM<br>Lebih Cerdas & Modern</h1>
            <p style="margin: 20px 0; color: #94a3b8;">
                Sistem pendukung keputusan persediaan barang menggunakan metode <strong>Economic Order Quantity (EOQ)</strong> untuk pelaku usaha di Desa Perigi Limus, Kalimantan Barat. Tingkatkan keuntungan, minimalkan gudang berlebih.
            </p>
            <a href="login.php" class="btn-primary">Mulai Sekarang <i class="fa-solid fa-arrow-right"></i></a>
            <a href="#eoq" style="color: #fff; margin-left: 20px; text-decoration: none;">Pelajari EOQ</a>
        </div>
        <div class="hero-img">
            <i class="fa-solid fa-chart-line"></i>
             <!-- <img src="Logo_kitte.png" alt="EOQ SYSTEM KITTE" class="hero-logo"> -->
        </div>
    </section>

    <!-- Section Tentang -->
    <section id="about" style="padding: 80px 5%;">
        <h2 style="text-align: center; margin-bottom: 40px; color: white;">Tentang Sistem</h2>
        <div class="stats-grid">
            <div class="glass-card">
                <i class="fa-solid fa-warehouse" style="font-size: 2.5rem; color: #06b6d4; margin-bottom: 15px;"></i>
                <h3 style="color: white;">Kontrol Gudang</h3>
                <p style="color: #94a3b8;">Monitoring stok real-time agar tidak terjadi kekosongan barang.</p>
            </div>
            <div class="glass-card">
                <i class="fa-solid fa-cash-register" style="font-size: 2.5rem; color: #06b6d4; margin-bottom: 15px;"></i>
                <h3 style="color: white;">Kasir Modern (POS)</h3>
                <p style="color: #94a3b8;">Transaksi cepat dengan scan barcode dan struk digital.</p>
            </div>
            <div class="glass-card">
                <i class="fa-solid fa-microchip" style="font-size: 2.5rem; color: #06b6d4; margin-bottom: 15px;"></i>
                <h3 style="color: white;">Smart EOQ</h3>
                <p style="color: #94a3b8;">Rekomendasi jumlah pemesanan optimal secara otomatis.</p>
            </div>
        </div>
    </section>

    <!-- Cara Kerja EOQ -->
    <section id="eoq" style="padding: 80px 5%; background: #1e293b;">
        <h2 style="text-align: center; margin-bottom: 20px;">Bagaimana EOQ Bekerja?</h2>
        <p style="text-align: center; color: #94a3b8; max-width: 600px; margin: 0 auto 40px;">
            Metode Economic Order Quantity (EOQ) adalah teknik manajemen persediaan yang bertujuan menentukan jumlah kuantitas pesanan yang paling ekonomis.
        </p>
        <div class="glass-card" style="max-width: 800px; margin: 0 auto; text-align: center;">
            <h3 style="color: #06b6d4; margin-bottom: 20px;">Rumus EOQ:</h3>
            <div style="font-size: 2rem; color: white; font-weight: bold; margin-bottom: 20px;">
                EOQ = √(2DS / H)
            </div>
            <div style="display: flex; justify-content: space-around; flex-wrap: wrap; color: #94a3b8;">
                <div><strong>D</strong><br>Permintaan Tahunan</div>
                <div><strong>S</strong><br>Biaya Pemesanan</div>
                <div><strong>H</strong><br>Biaya Penyimpanan</div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" style="background: #0f172a; padding: 40px 5%; text-align: center; border-top: 1px solid #334155;">
        <h2 style="color: white;">EOQ UMKM PERIGI LIMUS</h2>
        <p style="color: #94a3b8; margin: 10px 0;">Desa Perigi Limus, Kalimantan Barat, Indonesia</p>
        <div style="margin-top: 20px;">
            <a href="#" style="color: #06b6d4; margin: 0 10px;"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" style="color: #06b6d4; margin: 0 10px;"><i class="fa-brands fa-facebook"></i></a>
            <a href="#" style="color: #06b6d4; margin: 0 10px;"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
        <p style="margin-top: 30px; color: #64748b;">&copy; 2026 Sistem EOQ UMKM. All rights reserved.</p>
    </footer>
</body>

</html>