<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];

// Proses update profile
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    
    // Jika ada password baru
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $data = [
            'nama' => $nama,
            'password' => $password,
            'alamat' => $alamat,
            'telepon' => $telepon
        ];
    } else {
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'telepon' => $telepon
        ];
    }
    
    if (ubah('tb_user', $data, "user_id = $user_id")) {
        $_SESSION['success'] = 'Profile berhasil diupdate!';
        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Laptop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pesanan Saya</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Keranjang
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                                <span class="badge bg-danger"><?= count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Saya</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $_SESSION['success']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= $user['nama']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Kosongkan jika tidak ingin mengubah password">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $user['alamat']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?= $user['telepon']; ?>" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Pembelian Terbaru -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Pembelian Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Ambil 5 pembelian terakhir
                        $recent_orders = query("SELECT p.*, pb.jenis_pembayaran 
                                             FROM tb_pembelian p 
                                             LEFT JOIN tb_pembayaran pb ON p.pembayaran_id = pb.pembayaran_id 
                                             WHERE p.user_id = $user_id 
                                             ORDER BY p.tanggal DESC LIMIT 5");
                        ?>

                        <?php if (empty($recent_orders)) : ?>
                            <p class="text-muted mb-0">Belum ada riwayat pembelian</p>
                        <?php else : ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Pembayaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order) : ?>
                                            <tr>
                                                <td>#<?= $order['id_pembelian']; ?></td>
                                                <td><?= date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                                <td>Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?></td>
                                                <td><?= $order['jenis_pembayaran']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <a href="orders.php" class="btn btn-outline-primary btn-sm">Lihat Semua Pesanan</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistik Pembelian -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Pembelian</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Total pembelian
                        $total_orders = query("SELECT COUNT(*) as total FROM tb_pembelian WHERE user_id = $user_id")[0]['total'];
                        
                        // Total produk dibeli
                        $total_products = query("SELECT SUM(dp.jumlah) as total 
                                               FROM tb_pembelian p 
                                               JOIN tb_detail_pembelian dp ON p.id_pembelian = dp.id_pembelian 
                                               WHERE p.user_id = $user_id")[0]['total'];
                        
                        // Total pengeluaran
                        $total_spent = query("SELECT SUM(jumlah_pembayaran) as total 
                                            FROM tb_pembelian 
                                            WHERE user_id = $user_id")[0]['total'];
                        ?>

                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4><?= $total_orders; ?></h4>
                                <p class="text-muted">Total Pesanan</p>
                            </div>
                            <div class="col-md-4">
                                <h4><?= $total_products ?? 0; ?></h4>
                                <p class="text-muted">Produk Dibeli</p>
                            </div>
                            <div class="col-md-4">
                                <h4>Rp <?= number_format($total_spent ?? 0, 0, ',', '.'); ?></h4>
                                <p class="text-muted">Total Pengeluaran</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Konfirmasi Password -->
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            if (password !== '' && password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
            }
        });
    </script>
</body>
</html>