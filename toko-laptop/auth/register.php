<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, redirect
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../user/index.php");
    }
    exit;
}

// Proses registrasi
if (isset($_POST['register'])) {
    // Ambil data dari form
    $nama = htmlspecialchars($_POST['nama']);
    $password = $_POST['password'];
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);
    
    $error = false;
    
    // Validasi input
    if (empty($nama) || empty($password) || empty($alamat) || empty($telepon)) {
        $error = true;
        $error_msg = "Semua field harus diisi!";
    }
    
    // Validasi password
    if (strlen($password) < 6) {
        $error = true;
        $error_msg = "Password minimal 6 karakter!";
    }
    
    // Validasi nomor telepon
    if (!preg_match("/^[0-9]{10,15}$/", $telepon)) {
        $error = true;
        $error_msg = "Format nomor telepon tidak valid!";
    }
    
    // Cek username sudah dipakai atau belum
    $check_query = "SELECT user_id FROM tb_user WHERE nama = '$nama'";
    if (mysqli_num_rows(mysqli_query($conn, $check_query)) > 0) {
        $error = true;
        $error_msg = "Username sudah digunakan!";
    }
    
    // Jika tidak ada error, proses registrasi
    if (!$error) {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Data untuk disimpan ke database
        $data = [
            'nama' => $nama,
            'password' => $password_hash,
            'alamat' => $alamat,
            'telepon' => $telepon
        ];
        
        // Simpan ke database
        if (tambah('tb_user', $data)) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $error_msg = "Terjadi kesalahan. Silakan coba lagi!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-register {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('../assets/img/hero.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body class="bg-register">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 my-5">
                <div class="text-center text-white mb-4">
                    <h2>Toko Laptop</h2>
                    <p>Daftar untuk mulai berbelanja</p>
                </div>
                
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">Register</h4>
                        
                        <?php if (isset($error_msg)) : ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= $error_msg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" id="registerForm">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Username</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?= isset($nama) ? $nama : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" 
                                       name="password" required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" 
                                          rows="3" required><?= isset($alamat) ? $alamat : ''; ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label for="telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" 
                                       value="<?= isset($telepon) ? $telepon : ''; ?>" required>
                                <small class="text-muted">Contoh: 081234567890</small>
                            </div>

                            <button type="submit" name="register" class="btn btn-primary w-100 mb-3">Register</button>
                            
                            <div class="text-center">
                                <p class="mb-0">Sudah punya akun? <a href="login.php">Login disini</a></p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="../index.php" class="text-white text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Kembali ke Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validasi form di sisi client
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const telepon = document.getElementById('telepon').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return;
            }
            
            if (!/^[0-9]{10,15}$/.test(telepon)) {
                e.preventDefault();
                alert('Nomor telepon tidak valid!');
                return;
            }
        });
    </script>
</body>
</html>