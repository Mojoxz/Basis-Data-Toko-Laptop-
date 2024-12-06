<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data barang dengan join ke kategori dan merk
$query = "SELECT b.*, k.nama_kategori, m.nama_merk 
          FROM tb_barang b 
          LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
          LEFT JOIN tb_merk m ON b.merk_id = m.merk_id
          ORDER BY b.barang_id DESC";
$barang = query($query);

include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Laptop</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Laptop</li>
    </ol>

    <!-- Tombol Tambah & Pesan -->
    <div class="mb-4">
        <a href="tambah.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Laptop
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

    <!-- Tabel Barang -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Data Laptop
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Laptop</th>
                            <th>Merk</th>
                            <th>Kategori</th>
                            <th>Spesifikasi</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($barang as $row) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <?php if ($row['gambar'] && file_exists("../../assets/img/barang/" . $row['gambar'])) : ?>
                                    <img src="../../assets/img/barang/<?= $row['gambar']; ?>" 
                                         alt="<?= htmlspecialchars($row['nama_barang']); ?>" 
                                         class="img-thumbnail"
                                         style="max-width: 100px;"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#imageModal<?= $row['barang_id']; ?>">
                                    
                                    <!-- Modal Preview Gambar -->
                                    <div class="modal fade" id="imageModal<?= $row['barang_id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><?= htmlspecialchars($row['nama_barang']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="../../assets/img/barang/<?= $row['gambar']; ?>" 
                                                         alt="<?= htmlspecialchars($row['nama_barang']); ?>" 
                                                         class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <img src="../../assets/img/no-image.jpg" 
                                         alt="No Image" 
                                         class="img-thumbnail"
                                         style="max-width: 100px;">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?= htmlspecialchars($row['nama_merk']); ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                            <td><?= htmlspecialchars($row['jenis_barang']); ?></td>
                            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                            <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php if ($row['stok'] <= 5) : ?>
                                    <span class="badge bg-danger"><?= $row['stok']; ?></span>
                                <?php else : ?>
                                    <span class="badge bg-success"><?= $row['stok']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="edit.php?id=<?= $row['barang_id']; ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['barang_id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus laptop <?= htmlspecialchars($row['nama_barang']); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Script -->
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>