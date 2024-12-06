<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data kategori
$kategori = query("SELECT * FROM tb_kategori ORDER BY kategori_id DESC");

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Kategori</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Kategori</li>
    </ol>

    <!-- Tombol Tambah & Pesan -->
    <div class="mb-4">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Kategori
        </a>
    </div>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Tabel Kategori -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Data Kategori
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kategori as $row) : 
                            // Hitung jumlah produk per kategori
                            $kategori_id = $row['kategori_id'];
                            $jumlah_produk = query("SELECT COUNT(*) as total FROM tb_barang WHERE kategori_id = $kategori_id")[0]['total'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                            <td>
                                <span class="badge bg-info"><?= $jumlah_produk; ?> Produk</span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $row['kategori_id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="#" onclick="confirmDelete('hapus.php?id=<?= $row['kategori_id']; ?>', '<?= $row['nama_kategori']; ?>')" 
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