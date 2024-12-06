<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data laptop
$query = "SELECT b.*, k.nama_kategori, m.nama_merk 
          FROM tb_barang b 
          LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
          LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
          WHERE b.stok > 0";

// Filter berdasarkan kategori
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kategori_id = $_GET['kategori'];
    $query .= " AND b.kategori_id = $kategori_id";
}

// Filter berdasarkan merk
if (isset($_GET['merk']) && $_GET['merk'] != '') {
    $merk_id = $_GET['merk'];
    $query .= " AND b.merk_id = $merk_id";
}

$laptops = query($query);

// Ambil data kategori dan merk untuk filter
$categories = query("SELECT * FROM tb_kategori");
$brands = query("SELECT * FROM tb_merk");

// Ambil data user
$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .product-card:hover {
            transform: scale(1.03);
            transition: transform 0.3s ease-in-out;
        }
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .modal-content img {
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Laptop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pesanan Saya</a>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?= $user['nama']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="get" class="row g-3">
                            <div class="col-md-5">
                                <select name="kategori" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category['kategori_id']; ?>" 
                                                <?= (isset($_GET['kategori']) && $_GET['kategori'] == $category['kategori_id']) ? 'selected' : ''; ?>>
                                            <?= $category['nama_kategori']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select name="merk" class="form-select">
                                    <option value="">Semua Merk</option>
                                    <?php foreach ($brands as $brand) : ?>
                                        <option value="<?= $brand['merk_id']; ?>"
                                                <?= (isset($_GET['merk']) && $_GET['merk'] == $brand['merk_id']) ? 'selected' : ''; ?>>
                                            <?= $brand['nama_merk']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($laptops as $laptop) : ?>
            <div class="col">
                <div class="card h-100 product-card shadow-sm border-0">
                    <div class="position-relative">
                        <img src="../assets/img/barang/<?= $laptop['gambar'] ?: 'no-image.jpg'; ?>" 
                             class="card-img-top product-image rounded-top" 
                             alt="<?= $laptop['nama_barang']; ?>" 
                             onclick="showImageModal('<?= $laptop['nama_barang']; ?>', '../assets/img/barang/<?= $laptop['gambar'] ?: 'no-image.jpg'; ?>')">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2 px-3 py-2">
                            Stok: <?= $laptop['stok']; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= $laptop['nama_barang']; ?></h5>
                        <p class="card-text small text-muted">
                            <?= strlen($laptop['jenis_barang']) > 70 ? substr(htmlspecialchars($laptop['jenis_barang']), 0, 70) . '...' : htmlspecialchars($laptop['jenis_barang']); ?>
                        </p>
                        <button 
                            class="btn btn-link text-decoration-none p-0" 
                            onclick="showDescriptionModal(`<?= addslashes($laptop['nama_barang']); ?>`, `<?= addslashes(nl2br(htmlspecialchars($laptop['jenis_barang']))); ?>`)">
                            <small>Lihat Deskripsi Lengkap</small>
                        </button>

                        <h6 class="fw-bold text-primary mt-2">Rp <?= number_format($laptop['harga_jual'], 0, ',', '.'); ?></h6>
                        <?php if ($laptop['stok'] > 0) : ?>
                            <form action="cart.php" method="post" class="d-grid gap-2 mt-3">
                                <input type="hidden" name="barang_id" value="<?= $laptop['barang_id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Jumlah</span>
                                    <input type="number" name="qty" value="1" min="1" max="<?= $laptop['stok']; ?>" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </form>
                        <?php else : ?>
                            <button class="btn btn-secondary btn-sm w-100 mt-3" disabled>Stok Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imageModalImg" src="" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="descriptionModalBody"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImageModal(title, src) {
            document.getElementById('imageModalLabel').textContent = title;
            document.getElementById('imageModalImg').src = src;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        function showDescriptionModal(title, description) {
    // Menghindari masalah parsing tanda kutip dalam JavaScript
        document.getElementById('descriptionModalLabel').textContent = title;
        document.getElementById('descriptionModalBody').innerHTML = description; // Pastikan innerHTML mendukung tag HTML
        const descriptionModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
        descriptionModal.show();
        }


    </script>
</body>
</html>
