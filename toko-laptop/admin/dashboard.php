<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Statistik Dashboard
$total_produk = query("SELECT COUNT(*) as total FROM tb_barang")[0]['total'];
$total_penjualan = query("SELECT COUNT(*) as total FROM tb_penjualan")[0]['total'];
$total_user = query("SELECT COUNT(*) as total FROM tb_user")[0]['total'];

// Total pendapatan
$pendapatan = query("SELECT SUM(total) as total FROM tb_penjualan")[0]['total'];

// Produk terlaris
$produk_terlaris = query("SELECT b.nama_barang, SUM(dp.jumlah) as total_terjual 
                         FROM tb_detail_penjualan dp 
                         JOIN tb_barang b ON dp.barang_id = b.barang_id 
                         GROUP BY dp.barang_id 
                         ORDER BY total_terjual DESC 
                         LIMIT 5");

// Penjualan terbaru
$penjualan_terbaru = query("SELECT p.*, u.nama as nama_user 
                           FROM tb_penjualan p 
                           JOIN tb_user u ON p.user_id = u.user_id 
                           ORDER BY p.tanggal DESC 
                           LIMIT 5");

// Stok menipis (kurang dari 5)
$stok_menipis = query("SELECT b.*, k.nama_kategori 
                      FROM tb_barang b 
                      JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                      WHERE b.stok < 5");

// Include header template
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Cards -->
    <div class="row">
        <!-- Total Produk -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Produk</div>
                            <div class="display-6"><?= $total_produk ?></div>
                        </div>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="produk/index.php">Lihat Detail</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Penjualan</div>
                            <div class="display-6"><?= $total_penjualan ?></div>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="penjualan/index.php">Lihat Detail</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total User</div>
                            <div class="display-6"><?= $total_user ?></div>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="user/index.php">Lihat Detail</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Pendapatan</div>
                            <div class="h4">Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
                        </div>
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="laporan/index.php">Lihat Laporan</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Produk Terlaris -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    Produk Terlaris
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk_terlaris as $produk) : ?>
                                <tr>
                                    <td><?= $produk['nama_barang'] ?></td>
                                    <td><?= $produk['total_terjual'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Stok Menipis
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_menipis as $stok) : ?>
                                <tr>
                                    <td><?= $stok['nama_barang'] ?></td>
                                    <td><?= $stok['nama_kategori'] ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?= $stok['stok'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Penjualan Terbaru -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-clock-history me-1"></i>
            Penjualan Terbaru
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penjualan_terbaru as $penjualan) : ?>
                        <tr>
                            <td>#<?= $penjualan['penjualan_id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($penjualan['tanggal'])) ?></td>
                            <td><?= $penjualan['nama_user'] ?></td>
                            <td>Rp <?= number_format($penjualan['total'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-success">Selesai</span>
                            </td>
                            <td>
                                <a href="penjualan/detail.php?id=<?= $penjualan['penjualan_id'] ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>