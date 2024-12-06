<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data supplier beserta jumlah barang yang disupply
$supplier = query("SELECT s.*, COUNT(b.barang_id) as jumlah_produk 
                  FROM tb_supplier s 
                  LEFT JOIN tb_barang b ON s.barang_id = b.barang_id 
                  GROUP BY s.supplier_id 
                  ORDER BY s.supplier_id DESC");

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Supplier</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Supplier</li>
    </ol>

    <!-- Tombol Tambah & Pesan -->
    <div class="mb-4">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Supplier
        </a>
    </div>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tabel Supplier -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Data Supplier
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Supplier</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($supplier as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td><?= htmlspecialchars($row['telepon']); ?></td>
                            <td>
                                <span class="badge bg-info"><?= $row['jumlah_produk']; ?> Produk</span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $row['supplier_id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="#" onclick="confirmDelete('hapus.php?id=<?= $row['supplier_id']; ?>', '<?= $row['nama']; ?>')" 
                                   class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
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