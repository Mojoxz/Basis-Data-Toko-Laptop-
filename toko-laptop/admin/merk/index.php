<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data merk
$merk = query("SELECT * FROM tb_merk ORDER BY merk_id DESC");

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Merk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Merk</li>
    </ol>

    <!-- Tombol Tambah & Pesan -->
    <div class="mb-4">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Merk
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

    <!-- Tabel Merk -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Data Merk
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Merk</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($merk as $row) : 
                            // Hitung jumlah produk per merk
                            $merk_id = $row['merk_id'];
                            $jumlah_produk = query("SELECT COUNT(*) as total FROM tb_barang WHERE merk_id = $merk_id")[0]['total'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_merk']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <td>
                                <span class="badge bg-info"><?= $jumlah_produk; ?> Produk</span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $row['merk_id']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="#" onclick="confirmDelete('hapus.php?id=<?= $row['merk_id']; ?>', '<?= $row['nama_merk']; ?>')" 
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