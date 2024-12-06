<?php
session_start();
require_once 'config/koneksi.php';

// Ambil data laptop terbaru
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

$query .= " ORDER BY b.barang_id DESC";
$laptops = query($query);

// Ambil data kategori dan merk untuk filter
$categories = query("SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
$brands = query("SELECT * FROM tb_merk ORDER BY nama_merk ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Laptop - Pusat Penjualan Laptop Terpercaya</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/img/hero.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            padding: 10px;
            transition: transform 0.3s ease;
        }
        
        .product-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .description-container {
            min-height: 80px;
            margin-bottom: 1rem;
        }
        
        .description {
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        
        .modal-product-image {
            max-height: 80vh;
            object-fit: contain;
        }

        .btn-filter {
            min-width: 120px;
        }

        .filter-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .price-tag {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0d6efd;
        }

        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }

        .category-card {
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Unesa Laptop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Kategori</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['login'])) : ?>
                        <?php if ($_SESSION['role'] === 'admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/index.php">Dashboard Admin</a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="user/cart.php">
                                    <i class="bi bi-cart"></i> Keranjang
                                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) : ?>
                                        <span class="badge bg-danger"><?= count($_SESSION['cart']); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="user/orders.php">Pesanan Saya</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">Logout</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 mb-4">Selamat Datang di Unesa Laptop</h1>
            <p class="lead mb-5">Temukan laptop impian Anda dengan harga terbaik dan kualitas terjamin</p>
            <a href="#products" class="btn btn-primary btn-lg">Lihat Produk</a>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3>Produk Original</h3>
                    <p>Garansi resmi dan produk berkualitas</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h3>Pengiriman Cepat</h3>
                    <p>Layanan pengiriman ke seluruh Indonesia</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h3>Layanan 24/7</h3>
                    <p>Dukungan pelanggan setiap saat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Produk Kami</h2>
            
            <!-- Filter -->
            <div class="filter-section">
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

            <!-- Products Grid -->
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($laptops as $laptop) : ?>
                <div class="col">
                    <div class="card h-100 product-card">
                        <?php if ($laptop['stok'] <= 5) : ?>
                            <div class="stock-badge">
                                <span class="badge bg-warning">Stok Terbatas</span>
                            </div>
                        <?php endif; ?>

                        <?php if ($laptop['gambar'] && file_exists("assets/img/barang/" . $laptop['gambar'])) : ?>
                            <img src="assets/img/barang/<?= $laptop['gambar']; ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?= $laptop['nama_barang']; ?>"
                                 style="cursor: pointer;"
                                 onclick="showImage('<?= $laptop['nama_barang']; ?>', 'assets/img/barang/<?= $laptop['gambar']; ?>')">
                        <?php else : ?>
                            <img src="assets/img/no-image.jpg" 
                                 class="card-img-top product-image" 
                                 alt="No Image Available">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $laptop['nama_barang']; ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?= $laptop['nama_merk']; ?> | <?= $laptop['nama_kategori']; ?>
                                </small>
                            </p>
                            
                            <div class="description-container">
                                <?php
                                $deskripsi = $laptop['jenis_barang'];
                                $max_length = 100;
                                
                                if (strlen($deskripsi) > $max_length) {
                                    $short_desc = substr($deskripsi, 0, $max_length) . '...';
                                    echo '<p class="card-text description" id="short_'.$laptop['barang_id'].'">' . $short_desc . ' 
                                          <a href="javascript:void(0)" onclick="toggleDescription('.$laptop['barang_id'].')" 
                                             class="text-primary">Selengkapnya</a></p>';
                                    echo '<p class="card-text description" id="full_'.$laptop['barang_id'].'" style="display:none">' 
                                         . $deskripsi . ' <a href="javascript:void(0)" 
                                         onclick="toggleDescription('.$laptop['barang_id'].')" 
                                         class="text-primary">Sembunyikan</a></p>';
                                } else {
                                    echo '<p class="card-text">' . $deskripsi . '</p>';
                                }
                                ?>
                            </div>

                            <div class="mt-auto">
                                <p class="price-tag mb-3">Rp <?= number_format($laptop['harga_jual'], 0, ',', '.'); ?></p>
                                
                                <?php if (isset($_SESSION['login']) && $_SESSION['role'] === 'user') : ?>
                                    <?php if ($laptop['stok'] > 0) : ?>
                                        <form action="user/cart.php" method="post">
                                            <input type="hidden" name="barang_id" value="<?= $laptop['barang_id']; ?>">
                                            <input type="hidden" name="action" value="add">
                                            <div class="d-flex align-items-center mb-3">
                                                <label class="me-2">Jumlah:</label>
                                                <input type="number" name="qty" value="1" min="1" max="<?= $laptop['stok']; ?>" 
                                                       class="form-control form-control-sm" style="width: 70px;">
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                                    <?php endif; ?>
                        
                                    <?php else : ?>
                                    <a href="auth/login.php" class="btn btn-primary w-100">Login untuk Membeli</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Kategori Laptop</h2>
            <div class="row g-4">
                <?php foreach ($categories as $category) : 
                    // Hitung jumlah produk per kategori
                    $kategori_id = $category['kategori_id'];
                    $jumlah_produk = query("SELECT COUNT(*) as total FROM tb_barang WHERE kategori_id = $kategori_id")[0]['total'];
                ?>
                <div class="col-md-4">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $category['nama_kategori']; ?></h5>
                            <p class="text-muted"><?= $jumlah_produk; ?> Produk</p>
                            <a href="?kategori=<?= $category['kategori_id']; ?>#products" class="btn btn-outline-primary">
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Tentang Unesa Laptop</h5>
                    <p>Unesa Laptop adalah destinasi terpercaya untuk membeli laptop berkualitas dengan harga terbaik.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Home</a></li>
                        <li><a href="#products" class="text-white">Produk</a></li>
                        <li><a href="#categories" class="text-white">Kategori</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-telephone"></i> +62 123 456 789</li>
                        <li><i class="bi bi-envelope"></i> unesa@mhs.unesa.ac.id</li>
                        <li><i class="bi bi-geo-alt"></i> Jl. Contoh No. 123</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y'); ?> Unesa Laptop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid modal-product-image">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
    function showImage(title, src) {
        document.getElementById('imageModalLabel').textContent = title;
        document.getElementById('modalImage').src = src;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    function toggleDescription(id) {
        const shortDesc = document.getElementById('short_' + id);
        const fullDesc = document.getElementById('full_' + id);
        
        if (shortDesc.style.display === 'none') {
            shortDesc.style.display = 'block';
            fullDesc.style.display = 'none';
        } else {
            shortDesc.style.display = 'none';
            fullDesc.style.display = 'block';
        }
    }

    // Auto submit form when select changes
    document.querySelectorAll('select[name="kategori"], select[name="merk"]').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>