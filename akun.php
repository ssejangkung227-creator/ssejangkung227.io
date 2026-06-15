<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$pesan = '';

// Update Nama
if (isset($_POST['update_nama'])) {
    $nama = $_POST['nama'];
    mysqli_query($conn, "UPDATE users SET nama='$nama' WHERE id=".$_SESSION['id']);
    $_SESSION['nama'] = $nama;
    $pesan = '<div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Nama berhasil diupdate!</div>';
}

// Update Password
if (isset($_POST['update_password'])) {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi'];
    
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id=".$_SESSION['id']));
    
    if ($pass_lama != $cek['password']) {
        $pesan = '<div class="alert alert-danger"><i class="fa-solid fa-xmark-circle"></i> Password lama salah!</div>';
    } elseif ($pass_baru != $konfirmasi) {
        $pesan = '<div class="alert alert-danger"><i class="fa-solid fa-xmark-circle"></i> Password baru tidak sama!</div>';
    } else {
        mysqli_query($conn, "UPDATE users SET password='$pass_baru' WHERE id=".$_SESSION['id']);
        $pesan = '<div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Password berhasil diganti!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun Saya - EOQ Sistem</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="grid-container">
<aside class="sidebar">
<div class="sidebar-brand">
<i class="fa-solid fa-cubes-stacked"></i><h3>EOQ SYSTEM KITTE</h3>
</div>
<div class="sidebar-user">
<div class="sidebar-user-avatar"><i class="fa-solid fa-user"></i></div>
<div class="sidebar-user-name"><?= $_SESSION['nama']; ?></div>
<div class="sidebar-user-role"><?= strtoupper($_SESSION['role']); ?></div>
</div>
<ul class="sidebar-menu">
<li><a href="dashboard_owner.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
<li><a href="akun.php" class="active"><i class="fa-solid fa-user-gear"></i> Akun Saya</a></li>
<li><a href="logout.php" style="color:var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
</ul>
</aside>

<main class="main-content">
<div class="page-header">
<div>
<h1 class="page-title"><i class="fa-solid fa-user-gear"></i> Akun Saya</h1>
<p class="page-subtitle">Kelola informasi akun dan password Anda</p>
</div>
</div>

<?= $pesan; ?>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:25px;">
<!-- Info Akun -->
<div class="glass-card">
<h3 class="glass-card-title"><i class="fa-solid fa-user"></i> Informasi Akun</h3>
<form method="POST">
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-user"></i> Nama Lengkap</label>
<input type="text" name="nama" class="form-control" value="<?= $_SESSION['nama']; ?>" required>
</div>
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-at"></i> Username</label>
<input type="text" class="form-control" value="<?= $_SESSION['username']; ?>" disabled>
<small style="color:#64748b;font-size:0.8rem;">Username tidak dapat diubah</small>
</div>
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-user-tag"></i> Role / Jabatan</label>
<input type="text" class="form-control" value="<?= strtoupper($_SESSION['role']); ?>" disabled>
</div>
<button type="submit" name="update_nama" class="btn-primary">
<i class="fa-solid fa-save"></i> Simpan Perubahan
</button>
</form>
</div>

<!-- Ganti Password -->
<div class="glass-card">
<h3 class="glass-card-title"><i class="fa-solid fa-key"></i> Ganti Password</h3>
<form method="POST">
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-lock"></i> Password Lama</label>
<input type="password" name="pass_lama" class="form-control" placeholder="Masukkan password lama" required>
</div>
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-lock-open"></i> Password Baru</label>
<input type="password" name="pass_baru" class="form-control" placeholder="Masukkan password baru" required>
</div>
<div class="form-group">
<label class="form-label"><i class="fa-solid fa-lock"></i> Konfirmasi Password</label>
<input type="password" name="konfirmasi" class="form-control" placeholder="Ulangi password baru" required>
</div>
<button type="submit" name="update_password" class="btn-primary btn-danger">
<i class="fa-solid fa-key"></i> Ganti Password
</button>
</form>
</div>
</div>

<!-- Aktivitas -->
<div class="glass-card" style="margin-top:25px;">
<h3 class="glass-card-title"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Login</h3>
<div style="display:flex;align-items:center;gap:15px;padding:15px;background:var(--primary);border-radius:8px;">
<i class="fa-solid fa-circle-check" style="font-size:1.5rem;color:var(--success);"></i>
<div>
<div style="font-weight:600;color:white;">Login Terakhir</div>
<div style="color:#94a3b8;font-size:0.9rem;"><?= date('d F Y, H:i'); ?></div>
</div>
</div>
</div>
</main>
</div>
</body>
</html>
