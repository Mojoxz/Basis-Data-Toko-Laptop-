<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data barang untuk dropdown
$barang = query("SELECT * FROM tb_barang ORDER BY nama_barang ASC");

// Proses tambah supplier
if (isset($_POST['tambah'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $barang_id = $_POST['barang_id'];
    
    // Validasi
    $error = false;
    
    // Cek nama kosong
    if (empty($nama)) {
        $error = true;
        $error_msg = "Nama supplier harus diisi!";
    }
    
    // Validasi nomor telepon
    if (!preg_match("/^[0-9]{10,15}$/", $telepon)) {
        $error = true;
        $error_msg = "Format nomor telepon tidak valid!";
    }

    if (!$error) {
        $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'telepon' => $telepon,
            'barang_id' => $barang_id
        ];
        
        if (tambah('tb_supplier', $data)) {
            $_SESSION['success'] = "Supplier berhasil ditambahkan!";
            header("Location: index.php");
            exit;
        } else {
            $error_msg = "Gagal menambahkan supplier!";
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Supplier</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Supplier</a></li>
        <li class="breadcrumb-item active">Tambah Supplier</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-plus-circle me-1"></i>
                    Form Tambah Supplier
                </div>
                <div class="card-body">
                    <?php if (isset($error_msg)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= isset($_POST['nama']) ? $_POST['nama'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                Nama supplier harus diisi!
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= isset($_POST['alamat']) ? $_POST['alamat'] : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" 
                                   value="<?= isset($_POST['telepon']) ? $_POST['telepon'] : ''; ?>" required>
                            <div class="invalid-feedback">
                                Nomor telepon harus diisi!
                            </div>
                            <small class="text-muted">Format: 081234567890</small>
                        </div>

                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Produk</label>
                            <select class="form-select" id="barang_id" name="barang_id" required>
                                <option value="">Pilih Produk</option>
                                <?php foreach ($barang as $item) : ?>
                                    <option value="<?= $item['barang_id']; ?>" 
                                            <?= (isset($_POST['barang_id']) && $_POST['barang_id'] == $item['barang_id']) ? 'selected' : ''; ?>>
                                        <?= $item['nama_barang']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Pilih produk yang disupply!
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" name="tambah" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>