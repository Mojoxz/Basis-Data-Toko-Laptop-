<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data admin
$admin_id = $_SESSION['admin_id'];
$admin = query("SELECT * FROM tb_admin WHERE admin_id = $admin_id")[0];

// Statistik dashboard
$total_produk = query("SELECT COUNT(*) as total FROM tb_barang")[0]['total'];
$total_kategori = query("SELECT COUNT(*) as total FROM tb_kategori")[0]['total'];
$total_penjualan = query("SELECT COUNT(*) as total FROM tb_penjualan")[0]['total'];
$total_user = query("SELECT COUNT(*) as total FROM tb_user")[0]['total'];

// Query penjualan terbaru dengan info user
$recent_sales = query("SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon,
                      pb.jenis_pembayaran, SUM(dp.subtotal) as total,
                      GROUP_CONCAT(b.nama_barang SEPARATOR ', ') as produk_dibeli,
                      COUNT(dp.barang_id) as jumlah_item 
                      FROM tb_penjualan p 
                      LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
                      LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
                      LEFT JOIN tb_user u ON pmb.user_id = u.user_id
                      LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
                      LEFT JOIN tb_detail_penjualan dp ON p.penjualan_id = dp.penjualan_id
                      LEFT JOIN tb_barang b ON dp.barang_id = b.barang_id
                      GROUP BY p.penjualan_id
                      ORDER BY p.tanggal DESC 
                      LIMIT 5");

// Produk dengan stok menipis (kurang dari 5)
$low_stock = query("SELECT b.*, k.nama_kategori, m.nama_merk 
                   FROM tb_barang b
                   LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id
                   LEFT JOIN tb_merk m ON b.merk_id = m.merk_id
                   WHERE b.stok < 5
                   ORDER BY b.stok ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Unesa Laptop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .feather {
            width: 16px;
            height: 16px;
            vertical-align: text-bottom;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }

        .stat-card {
            border-left: 4px solid;
        }

        .stat-card.primary {
            border-left-color: #0d6efd;
        }

        .stat-card.success {
            border-left-color: #198754;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Unesa Laptop</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="../auth/logout.php">Sign out</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="barang/index.php">
                                <i class="bi bi-laptop"></i>
                                Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="kategori/index.php">
                                <i class="bi bi-tags"></i>
                                Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="merk/index.php">
                                <i class="bi bi-bookmark"></i>
                                Merk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="supplier/index.php">
                                <i class="bi bi-truck"></i>
                                Supplier
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="penjualan/index.php">
                                <i class="bi bi-cart"></i>
                                Penjualan
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Statistik Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Produk</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_produk; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-laptop fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Kategori</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_kategori; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-tags fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Penjualan</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_penjualan; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-cart fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card danger h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Total User</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_user; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penjualan Terbaru & Stok Menipis -->
                <div class="row">
                    <!-- Penjualan Terbaru -->
                    <div class="col-lg-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Penjualan Terbaru</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Customer</th>
                                                <th>Produk</th>
                                                <th>Total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_sales as $sale) : ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($sale['tanggal'])); ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($sale['nama_user'] ?? 'User tidak ditemukan'); ?></strong><br>
                                                    <small class="text-muted">Telp: <?= htmlspecialchars($sale['telepon'] ?? '-'); ?></small>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($sale['produk_dibeli']); ?><br>
                                                    <small class="text-muted"><?= $sale['jumlah_item']; ?> item</small>
                                                </td>
                                                <td>Rp <?= number_format($sale['total'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <a href="penjualan/detail.php?id=<?= $sale['penjualan_id']; ?>" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
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

                    <!-- Stok Menipis -->
                    <div class="col-lg-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Stok Menipis</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Kategori</th>
                                                <th>Merk</th>
                                                <th>Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($low_stock as $item) : ?>
                                            <tr>
                                                <td><?= $item['nama_barang']; ?></td>
                                                <td><?= $item['nama_kategori']; ?></td>
                                                <td><?= $item['nama_merk']; ?></td>
                                                <td>
                                                    <span class="badge bg-danger"><?= $item['stok']; ?></span>
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

                <!-- Grafik atau Info Tambahan bisa ditambahkan di sini -->
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Informasi Sistem</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Admin:</strong> <?= $admin['nama']; ?></p>
                                        <p><strong>Login Terakhir:</strong> <?= date('d/m/Y H:i'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Transaksi Hari Ini:</strong> 
                                            <?php 
                                            $today = date('Y-m-d');
                                            $transaksi_hari_ini = query("SELECT COUNT(*) as total FROM tb_penjualan WHERE DATE(tanggal) = '$today'")[0]['total'];
                                            echo $transaksi_hari_ini;
                                            ?>
                                        </p>
                                        <p><strong>Pendapatan Hari Ini:</strong> 
                                            <?php
                                            $pendapatan_hari_ini = query("SELECT SUM(total) as total FROM tb_penjualan WHERE DATE(tanggal) = '$today'")[0]['total'];
                                            echo "Rp " . number_format($pendapatan_hari_ini ?? 0, 0, ',', '.');
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk auto refresh setiap 5 menit -->
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 menit = 300000 ms
    </script>
</body>
</html>