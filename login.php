<?php
session_start();
include 'koneksi.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['login'] = true;
        $_SESSION['id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['username'] = $row['username'];

        // Redirect sesuai role
        if ($row['role'] == 'admin') {
            header("Location: dashboard.php");
        } elseif ($row['role'] == 'owner') {
            header("Location: owner/dashboard_owner.php");
        } else {
            header("Location: dashboard_kasir.php");
        }
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EOQ Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header i {
            font-size: 3rem;
            color: #06b6d4;
        }

        .login-header h2 {
            margin-top: 10px;
            color: white;
        }

        .login-header p {
            color: #94a3b8;
        }

        .login-credits {
            margin-top: 20px;
            padding: 15px;
            background: rgba(6, 182, 212, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-credits p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 5px 0;
        }
    </style>
</head>

<body class="bg-modern">
    <div class="login-container">
        <div class="glass-card login-box">
            <div class="login-header">
                <i class="fa-solid fa-cubes-stacked"></i>
                <h2>LOGIN</h2>
                <p>EOQ Sistem UMKMPerigi Limus</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" style="text-align: center;">
                    <i class="fa-solid fa-xmark-circle"></i> <?= $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" name="login" class="btn-primary" style="width: 100%; margin-top: 10px;">
                    <i class="fa-solid fa-right-to-bracket"></i> LOGIN
                </button>
            </form>

            <!-- <div class="login-credits">
                <p><strong>Akun Di coba :</strong></p>
                <p>Admin: admin / admin123</p>
                <p>Owner: owner / owner123</p>
                <p>Kasir: kasir / kasir123</p>
            </div> -->

            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="color: #94a3b8;">&larr; Kembali</a>
            </div>
        </div>
    </div>
</body>

</html>