<?php
session_start();
require_once '../config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di tabel admin
    $query = "SELECT * FROM tb_admin WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password === $row['password']) { // Tidak menggunakan hash untuk admin
            $_SESSION['login'] = true;
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['role'] = 'admin';
            header("Location: ../admin/index.php");
            exit;
        }
    }

    // Cek di tabel user
    $query = "SELECT * FROM tb_user WHERE nama = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) { // Menggunakan hash untuk user
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = 'user';
            header("Location: ../user/index.php");
            exit;
        }
    }

    // Login gagal
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../assets/img/hero.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .back-link {
            margin-top: 10px;
            display: block;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
            color: #6c757d;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger">
                                Username atau password salah!
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
                            <a href="../index.php" class="back-link">Kembali ke Halaman Utama</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
