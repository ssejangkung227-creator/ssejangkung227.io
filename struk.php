<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['nota'])) {
    header("Location: pos.php");
    exit;
}

$nota = $_SESSION['nota'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi - EOQ Sistem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { font-family: 'Courier New', monospace; background: white; color: black; padding: 20px; }
        .struk { max-width: 300px; margin: 0 auto; border: 1px dashed #333; padding: 20px; }
        .header { text-align: center; border-bottom: 1px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
        .total { font-weight: bold; font-size: 14px; border-top: 1px dashed #333; padding-top: 10px; margin-top: 10px; }
        .btn-print { padding: 10px 20px; background: #06b6d4; color: white; border: none; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Cetak / Simpan PDF</button>
        <a href="pos.php" class="btn-print" style="background: #10b981; text-decoration: none; margin-left: 10px;">Kembali ke Kasir</a>
    </div>

    <div class="struk">
        <div class="header">
            <h2>EOQ SYSTEM KITTE</h2>
            <p>Desa Perigi Limus, Kalbar</p>
            <p>Tgl: <?= $nota['tanggal']; ?></p>
            <p>No: #<?= str_pad($nota['id'], 6, '0', STR_PAD_LEFT); ?></p>
        </div>

        <?php foreach ($nota['items'] as $item): ?>
        <div class="item">
            <span><?= $item['nama']; ?> (x<?= $item['qty']; ?>)</span>
            <span>Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.'); ?></span>
        </div>
        <?php endforeach; ?>

        <div class="total">
            <div class="item">
                <span>TOTAL</span>
                <span>Rp <?= number_format($nota['total'], 0, ',', '.'); ?></span>
            </div>
            <div class="item">
                <span>BAYAR</span>
                <span>Rp <?= number_format($nota['bayar'], 0, ',', '.'); ?></span>
            </div>
            <div class="item">
                <span>KEMBALIAN</span>
                <span>Rp <?= number_format($nota['kembalian'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 10px;">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Simpan struk sebagai bukti pembayaran</p>
        </div>
    </div>
    
    <?php unset($_SESSION['nota']); ?>
</body>
</html>