<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Filter tanggal
$where = "";
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];
    if (!empty($dari) && !empty($sampai)) {
        $where = "WHERE DATE(p.tanggal) BETWEEN '$dari' AND '$sampai'";
    }
}

// Query untuk mendapatkan data penjualan dengan join ke user dan pembayaran
$query = "SELECT p.*, a.nama as admin_name, u.nama as nama_user, u.telepon, pb.jenis_pembayaran,
          (SELECT SUM(dp.subtotal) FROM tb_detail_penjualan dp WHERE dp.penjualan_id = p.penjualan_id) as total_penjualan 
          FROM tb_penjualan p 
          LEFT JOIN tb_admin a ON p.admin_id = a.admin_id
          LEFT JOIN tb_pembelian pmb ON p.penjualan_id = pmb.id_pembelian
          LEFT JOIN tb_user u ON pmb.user_id = u.user_id
          LEFT JOIN tb_pembayaran pb ON pmb.pembayaran_id = pb.pembayaran_id
          $where
          ORDER BY p.tanggal DESC";
$penjualan = query($query);

include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Penjualan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Penjualan</li>
    </ol>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Filter Tanggal -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="dari" 
                           value="<?= isset($_GET['dari']) ? $_GET['dari'] : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="sampai" 
                           value="<?= isset($_GET['sampai']) ? $_GET['sampai'] : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <?php if (isset($_GET['dari']) || isset($_GET['sampai'])) : ?>
                            <a href="index.php" class="btn btn-secondary">Reset</a>
                            <button type="button" class="btn btn-success" onclick="exportExcel()">
                                <i class="bi bi-file-excel"></i> Export
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ringkasan Penjualan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Transaksi</div>
                            <div class="text-lg fw-bold"><?= count($penjualan); ?></div>
                        </div>
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Pendapatan</div>
                            <div class="text-lg fw-bold">
                                Rp <?= number_format(array_sum(array_column($penjualan, 'total')), 0, ',', '.'); ?>
                            </div>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Produk Terjual</div>
                            <div class="text-lg fw-bold">
                                <?php
                                $total_produk = query("SELECT SUM(jumlah) as total FROM tb_detail_penjualan WHERE penjualan_id IN (SELECT penjualan_id FROM tb_penjualan $where)")[0]['total'] ?? 0;
                                echo $total_produk;
                                ?>
                            </div>
                        </div>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Customer</div>
                            <div class="text-lg fw-bold">
                                <?php
                                $total_customer = query("SELECT COUNT(DISTINCT user_id) as total FROM tb_pembelian $where")[0]['total'] ?? 0;
                                echo $total_customer;
                                ?>
                            </div>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Penjualan -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Data Penjualan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pembeli</th>
                            <th>Telepon</th>
                            <th>Metode Pembayaran</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($penjualan as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['nama_user'] ?? 'User tidak ditemukan'); ?></td>
                            <td><?= htmlspecialchars($row['telepon'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['jenis_pembayaran'] ?? '-'); ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($row['bayar'], 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($row['kembalian'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['admin_name']); ?></td>
                            <td>
                                <a href="detail.php?id=<?= $row['penjualan_id']; ?>" class="btn btn-info btn-sm mb-1">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="cetak.php?id=<?= $row['penjualan_id']; ?>" target="_blank" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-printer"></i> Cetak
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

<script>
// Export to Excel
function exportExcel() {
    let params = new URLSearchParams(window.location.search);
    let dari = params.get('dari') || '';
    let sampai = params.get('sampai') || '';
    window.location.href = `export.php?dari=${dari}&sampai=${sampai}`;
}
</script>

<?php include_once '../includes/footer.php'; ?>