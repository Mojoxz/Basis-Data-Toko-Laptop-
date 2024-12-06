<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Proses tambah ke keranjang
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $barang_id = $_POST['barang_id'];
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    
    // Cek stok
    $barang = query("SELECT stok FROM tb_barang WHERE barang_id = $barang_id")[0];
    if ($qty > $barang['stok']) {
        $_SESSION['error'] = "Stok tidak mencukupi! Stok tersedia: " . $barang['stok'];
    } else {
        // Cek apakah barang sudah ada di keranjang
        if (isset($_SESSION['cart'][$barang_id])) {
            $_SESSION['cart'][$barang_id] += $qty;
        } else {
            $_SESSION['cart'][$barang_id] = $qty;
        }
        
        $_SESSION['success'] = 'Produk berhasil ditambahkan ke keranjang';
    }
    header("Location: cart.php");
    exit;
}

// Proses update jumlah
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $barang_id = $_POST['barang_id'];
    $qty = (int)$_POST['qty'];
    
    // Cek stok
    $barang = query("SELECT stok FROM tb_barang WHERE barang_id = $barang_id")[0];
    if ($qty > $barang['stok']) {
        $_SESSION['error'] = "Stok tidak mencukupi! Stok tersedia: " . $barang['stok'];
    } else {
        if ($qty > 0) {
            $_SESSION['cart'][$barang_id] = $qty;
        } else {
            unset($_SESSION['cart'][$barang_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

// Proses hapus item
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $barang_id = $_GET['id'];
    unset($_SESSION['cart'][$barang_id]);
    
    $_SESSION['success'] = 'Produk berhasil dihapus dari keranjang';
    header("Location: cart.php");
    exit;
}

// Ambil data barang di keranjang
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $barang_ids = array_keys($_SESSION['cart']);
    $barang_ids_str = implode(',', $barang_ids);
    
    $cart_items = query("SELECT b.*, k.nama_kategori, m.nama_merk 
                        FROM tb_barang b 
                        LEFT JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                        LEFT JOIN tb_merk m ON b.merk_id = m.merk_id 
                        WHERE b.barang_id IN ($barang_ids_str)");
    
    // Hitung total
    foreach ($cart_items as $item) {
        $total += $item['harga_jual'] * $_SESSION['cart'][$item['barang_id']];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .table > tbody > tr > td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Laptop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Pesanan Saya</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="bi bi-cart"></i> Keranjang
                            <?php if (count($_SESSION['cart']) > 0) : ?>
                                <span class="badge bg-danger"><?= count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <h2 class="mb-4">Keranjang Belanja</h2>

        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($cart_items)) : ?>
            <div class="alert alert-info">
                Keranjang belanja kosong. <a href="index.php">Belanja sekarang</a>
            </div>
        <?php else : ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['gambar'] && file_exists("../assets/img/barang/" . $item['gambar'])) : ?>
                                                <img src="../assets/img/barang/<?= $item['gambar']; ?>" 
                                                     alt="<?= $item['nama_barang']; ?>"
                                                     class="product-image me-3">
                                            <?php else : ?>
                                                <img src="../assets/img/no-image.jpg" 
                                                     alt="No Image"
                                                     class="product-image me-3">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-0"><?= $item['nama_barang']; ?></h6>
                                                <small class="text-muted">
                                                    <?= $item['nama_merk']; ?> | <?= $item['nama_kategori']; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.'); ?></td>
                                    <td>
                                        <form action="" method="post" class="d-flex align-items-center" style="max-width: 150px;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="barang_id" value="<?= $item['barang_id']; ?>">
                                            <input type="number" class="form-control form-control-sm" name="qty" 
                                                   value="<?= $_SESSION['cart'][$item['barang_id']]; ?>" 
                                                   min="1" max="<?= $item['stok']; ?>"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>Rp <?= number_format($item['harga_jual'] * $_SESSION['cart'][$item['barang_id']], 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="?action=remove&id=<?= $item['barang_id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Lanjut Belanja
                        </a>
                        <a href="checkout.php" class="btn btn-primary">
                            Checkout <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>