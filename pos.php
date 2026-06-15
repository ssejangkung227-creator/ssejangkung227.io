<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$pesan = '';

// Tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $id = intval($_POST['id_barang']);
    $qty = intval($_POST['qty']);
    
    if ($id > 0 && $qty > 0) {
        $brg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'"));
        
        if ($brg) {
            if ($brg['stok'] < $qty) {
                $pesan = "<div class='alert-error'>Stok tidak mencukupi! Stok tersedia: ".$brg['stok']."</div>";
            } else {
                if (isset($keranjang[$id])) {
                    $keranjang[$id]['qty'] += $qty;
                } else {
                    $keranjang[$id] = [
                        'kode' => $brg['kode_barang'],
                        'nama' => $brg['nama_barang'],
                        'harga' => $brg['harga_jual'],
                        'qty' => $qty
                    ];
                }
                $_SESSION['keranjang'] = $keranjang;
            }
        }
    }
}

// Hapus item
if (isset($_GET['hapus_item'])) {
    $id = intval($_GET['hapus_item']);
    unset($keranjang[$id]);
    $_SESSION['keranjang'] = $keranjang;
    header("Location: pos.php");
}

// Checkout
if (isset($_POST['checkout'])) {
    $total = 0;
    foreach ($keranjang as $item) {
        $total += $item['harga'] * $item['qty'];
    }
    
    $bayar = intval($_POST['bayar']);
    $kembalian = $bayar - $total;

    if ($bayar < $total) {
        $pesan = "<div class='alert-error'>Uang pembayaran kurang!</div>";
    } else if (count($keranjang) == 0) {
        $pesan = "<div class='alert-error'>Keranjang kosong!</div>";
    } else {
        // Insert Transaksi
        mysqli_query($conn, "INSERT INTO transaksi (id_user, total_harga, status) VALUES ('$_SESSION[id]', '$total', 'selesai')");
        $id_transaksi = mysqli_insert_id($conn);

        foreach ($keranjang as $id => $item) {
            mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_barang, qty, harga_saat_ini) VALUES ('$id_transaksi', '$id', '".$item['qty']."', '".$item['harga']."')");
            mysqli_query($conn, "UPDATE barang SET stok = stok - ".$item['qty']." WHERE id_barang = '$id'");
        }

        // Simpan Nota
        $_SESSION['nota'] = [
            'id' => $id_transaksi,
            'total' => $total,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'items' => $keranjang,
            'tanggal' => date('Y-m-d H:i:s')
        ];

        // Clear
        $_SESSION['keranjang'] = [];
        header("Location: struk.php");
    }
}

$barang = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Kasir - EOQ Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pos-container { display: grid; grid-template-columns: 1fr 350px; gap: 20px; height: 100vh; }
        .product-list { overflow-y: auto; padding-right: 10px; }
        .product-card { background: #1e293b; padding: 15px; border-radius: 10px; cursor: pointer; transition: 0.3s; text-align: center; }
        .product-card:hover { background: #334155; transform: translateY(-3px); }
        .product-card h4 { color: white; font-size: 0.9rem; margin: 10px 0 5px; }
        .product-card p { color: #06b6d4; font-weight: bold; }
        .cart-panel { background: #1e293b; padding: 20px; border-radius: 15px; display: flex; flex-direction: column; }
        .cart-items { flex: 1; overflow-y: auto; margin: 15px 0; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #334155; }
        .cart-item-info h5 { color: white; font-size: 0.9rem; }
        .cart-item-info span { color: #94a3b8; font-size: 0.8rem; }
        .total-section { background: #0f172a; padding: 15px; border-radius: 10px; margin-bottom: 15px; }
        .total-price { font-size: 1.8rem; color: #06b6d4; font-weight: bold; }
    </style>
</head>
<body style="background: #0f172a; overflow: hidden;">
    <!-- Navbar Mini -->
    <nav style="background: #1e293b; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-cash-register" style="color: #06b6d4; font-size: 1.5rem;"></i>
            <h2 style="color: white; margin: 0;">KASIR POS</h2>
        </div>
        <a href="dashboard_kasir.php" style="color: white; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
    </nav>

    <?= $pesan; ?>

    <div class="pos-container" style="padding: 20px;">
        <!-- Daftar Produk -->
        <div class="product-list">
            <h3 style="color: white; margin-bottom: 15px;">Pilih Produk</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px;">
                <?php while ($b = mysqli_fetch_assoc($barang)): ?>
                <div class="product-card" onclick="addCart(<?= $b['id_barang']; ?>, '<?= addslashes($b['nama_barang']); ?>', <?= $b['harga_jual']; ?>)">
                    <div style="background: #0f172a; padding: 10px; border-radius: 8px;">
                        <i class="fa-solid fa-box" style="font-size: 2rem; color: #06b6d4;"></i>
                    </div>
                    <h4><?= $b['nama_barang']; ?></h4>
                    <p>Rp <?= number_format($b['harga_jual'], 0, ',', '.'); ?></p>
                    <small style="color: #94a3b8;">Stok: <?= $b['stok']; ?></small>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Keranjang -->
        <div class="cart-panel">
            <h3 style="color: white; border-bottom: 1px solid #334155; padding-bottom: 10px;">Keranjang</h3>
            
            <div class="cart-items">
                <?php $total = 0; foreach ($keranjang as $id => $item): $total += $item['harga'] * $item['qty']; ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h5><?= $item['nama']; ?></h5>
                        <span>Rp <?= number_format($item['harga'], 0, ',', '.'); ?> x <?= $item['qty']; ?></span>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: white; font-weight: bold;">Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.'); ?></div>
                        <a href="?hapus_item=<?= $id; ?>" style="color: #ef4444; font-size: 0.8rem;">Hapus</a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($keranjang) == 0): ?>
                <div style="text-align: center; color: #64748b; margin-top: 50px;">
                    <i class="fa-solid fa-cart-shopping" style="font-size: 3rem; margin-bottom: 10px;"></i>
                    <p>Keranjang kosong</p>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST">
                <div class="total-section">
                    <div style="display: flex; justify-content: space-between; color: #94a3b8;">
                        <span>Total</span>
                        <span class="total-price">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label style="color: #94a3b8;">Uang Bayar</label>
                    <input type="number" name="bayar" class="form-control" placeholder="0" required style="font-size: 1.2rem; padding: 15px;">
                </div>

                <button type="submit" name="checkout" class="btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; margin-top: 10px;">
                    <i class="fa-solid fa-print"></i> CETAK STruk & CHECKOUT
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Cepat -->
    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: #1e293b; padding: 30px; border-radius: 15px; width: 300px;">
            <h3 style="color: white; margin-bottom: 20px;">Tambah ke Keranjang</h3>
            <form method="POST">
                <input type="hidden" name="id_barang" id="modalId">
                <p id="modalNama" style="color: white; margin-bottom: 15px; font-weight: bold;"></p>
                <p id="modalHarga" style="color: #06b6d4; margin-bottom: 15px;"></p>
                
                <div class="form-group">
                    <label style="color: #94a3b8;">Jumlah</label>
                    <input type="number" name="qty" class="form-control" value="1" min="1" required>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" name="add_to_cart" class="btn-primary" style="flex: 1;">Tambah</button>
                    <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn-primary" style="background: #ef4444; flex: 1;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addCart(id, nama, harga) {
            document.getElementById('modalId').value = id;
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('modalHarga').innerText = 'Rp ' + harga.toLocaleString();
            document.getElementById('addModal').style.display = 'flex';
        }
    </script>
</body>
</html>