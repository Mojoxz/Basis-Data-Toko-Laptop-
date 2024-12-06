<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pembelian
$query = "SELECT p.*, pb.jenis_pembayaran 
          FROM tb_pembelian p 
          LEFT JOIN tb_pembayaran pb ON p.pembayaran_id = pb.pembayaran_id 
          WHERE p.user_id = $user_id 
          ORDER BY p.tanggal DESC";
$orders = query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Toko Laptop</title>
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
                        <a class="nav-link active" href="orders.php">Pesanan Saya</a>
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
                        <a class="nav-link" href="profile.php">Profile</a>
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
        <h2 class="mb-4">Pesanan Saya</h2>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (empty($orders)) : ?>
            <div class="alert alert-info">
                Belum ada pesanan. <a href="index.php">Belanja sekarang</a>
            </div>
        <?php else : ?>
            <div class="row">
                <?php foreach ($orders as $order) : ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Order #<?= $order['id_pembelian']; ?></span>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($order['tanggal'])); ?></small>
                            </div>
                            <div class="card-body">
                                <?php
                                // Ambil detail pembelian
                                $id_pembelian = $order['id_pembelian'];
                                $detail_query = "SELECT dp.*, b.nama_barang, b.harga_jual 
                                               FROM tb_detail_pembelian dp 
                                               JOIN tb_barang b ON dp.barang_id = b.barang_id 
                                               WHERE dp.id_pembelian = $id_pembelian";
                                $details = query($detail_query);
                                ?>
                                
                                <div class="mb-3">
                                    <h6>Detail Produk:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($details as $detail) : ?>
                                            <li>
                                                <?= $detail['nama_barang']; ?> 
                                                (<?= $detail['jumlah']; ?> x Rp <?= number_format($detail['harga_jual'], 0, ',', '.'); ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Metode Pembayaran:</strong></p>
                                        <p class="mb-0"><?= $order['jenis_pembayaran']; ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1"><strong>Total Bayar:</strong></p>
                                        <p class="mb-0">Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#orderDetail<?= $order['id_pembelian']; ?>">
                                    Detail Pesanan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Detail Pesanan -->
                    <div class="modal fade" id="orderDetail<?= $order['id_pembelian']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Pesanan #<?= $order['id_pembelian']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Harga</th>
                                                    <th>Jumlah</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($details as $detail) : ?>
                                                    <tr>
                                                        <td><?= $detail['nama_barang']; ?></td>
                                                        <td>Rp <?= number_format($detail['harga_jual'], 0, ',', '.'); ?></td>
                                                        <td><?= $detail['jumlah']; ?></td>
                                                        <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                    <td><strong>Rp <?= number_format($order['jumlah_pembayaran'], 0, ',', '.'); ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end">Bayar:</td>
                                                    <td>Rp <?= number_format($order['bayar'], 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end">Kembalian:</td>
                                                    <td>Rp <?= number_format($order['kembalian'], 0, ',', '.'); ?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>