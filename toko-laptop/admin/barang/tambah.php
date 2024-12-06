<?php
session_start();
require_once '../../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil data kategori dan merk untuk dropdown
$kategori = query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
$merk = query("SELECT * FROM tb_merk ORDER BY nama_merk ASC");

// Proses tambah barang
// Proses tambah barang
if (isset($_POST['tambah'])) {
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $merk_id = $_POST['merk_id'];
    $kategori_id = $_POST['kategori_id'];
    $jenis_barang = htmlspecialchars($_POST['jenis_barang']);
    $harga_beli = str_replace(['Rp', '.', ','], '', $_POST['harga_beli']);
    $harga_jual = str_replace(['Rp', '.', ','], '', $_POST['harga_jual']);
    $stok = $_POST['stok'];

    // Upload gambar
    $gambar = '';
    if ($_FILES['gambar']['error'] != UPLOAD_ERR_NO_FILE) {
        $target_dir = "../../assets/img/barang/";
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $file_name = time() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Validasi file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            $error = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan!";
        } elseif ($_FILES["gambar"]["size"] > 2000000) { // Maks 2MB
            $error = "File terlalu besar! Maksimal 2MB.";
        } else {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $file_name;
            } else {
                $error = "Gagal mengupload gambar! Coba lagi.";
            }
        }
    }

    // Validasi input
    if (empty($nama_barang)) {
        $error = "Nama laptop harus diisi!";
    } elseif (empty($merk_id)) {
        $error = "Merk harus dipilih!";
    } elseif (empty($kategori_id)) {
        $error = "Kategori harus dipilih!";
    } elseif (empty($harga_beli) || $harga_beli <= 0) {
        $error = "Harga beli tidak valid!";
    } elseif (empty($harga_jual) || $harga_jual <= 0) {
        $error = "Harga jual tidak valid!";
    } elseif ($harga_jual <= $harga_beli) {
        $error = "Harga jual harus lebih besar dari harga beli!";
    } elseif (empty($stok) || $stok < 0) {
        $error = "Stok tidak valid!";
    }

    if (!isset($error)) {
        // Simpan data ke dalam database
        $data = [
            'nama_barang' => $nama_barang,
            'merk_id' => $merk_id,
            'kategori_id' => $kategori_id,
            'jenis_barang' => $jenis_barang,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'stok' => $stok,
            'gambar' => $gambar
        ];

        if (tambah('tb_barang', $data)) {
            $_SESSION['success'] = "Data laptop berhasil ditambahkan!";
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menambahkan data laptop!";
        }
    }
}


include_once '../includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Laptop</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php">Laptop</a></li>
        <li class="breadcrumb-item active">Tambah Laptop</li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-plus-circle me-1"></i>
                    Form Tambah Laptop
                </div>
                <div class="card-body">
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_barang" class="form-label">Nama Laptop</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                                           value="<?= isset($_POST['nama_barang']) ? $_POST['nama_barang'] : ''; ?>" required>
                                    <div class="invalid-feedback">
                                        Nama laptop harus diisi!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="merk_id" class="form-label">Merk</label>
                                    <select class="form-select" id="merk_id" name="merk_id" required>
                                        <option value="">Pilih Merk</option>
                                        <?php foreach ($merk as $m) : ?>
                                            <option value="<?= $m['merk_id']; ?>" 
                                                <?= (isset($_POST['merk_id']) && $_POST['merk_id'] == $m['merk_id']) ? 'selected' : ''; ?>>
                                                <?= $m['nama_merk']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Pilih merk laptop!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="kategori_id" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori_id" name="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori as $k) : ?>
                                            <option value="<?= $k['kategori_id']; ?>" 
                                                <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $k['kategori_id']) ? 'selected' : ''; ?>>
                                                <?= $k['nama_kategori']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Pilih kategori laptop!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_barang" class="form-label">Spesifikasi</label>
                                    <textarea class="form-control" id="jenis_barang" name="jenis_barang" rows="4"
                                              required><?= isset($_POST['jenis_barang']) ? $_POST['jenis_barang'] : ''; ?></textarea>
                                    <div class="invalid-feedback">
                                        Spesifikasi laptop harus diisi!
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="harga_beli" class="form-label">Harga Beli</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control rupiah-input" id="harga_beli" 
                                               name="harga_beli" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Harga beli harus diisi!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="harga_jual" class="form-label">Harga Jual</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control rupiah-input" id="harga_jual" 
                                               name="harga_jual" required>
                                    </div>
                                    <div class="invalid-feedback">
                                        Harga jual harus diisi!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="number" class="form-control" id="stok" name="stok" 
                                           value="<?= isset($_POST['stok']) ? $_POST['stok'] : ''; ?>" 
                                           min="0" required>
                                    <div class="invalid-feedback">
                                        Stok harus diisi!
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Produk</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" 
                                           accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</div>
                                    <div class="mt-2">
                                        <img id="preview" src="#" alt="Preview" 
                                             class="img-thumbnail" style="max-height: 200px; display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
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

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Format Rupiah
document.querySelectorAll('.rupiah-input').forEach(function(input) {
    input.addEventListener('keyup', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        this.value = formatRupiah(value);
    });
});

function formatRupiah(angka) {
    let number_string = angka.toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}
</script>

<?php include_once '../includes/footer.php'; ?>