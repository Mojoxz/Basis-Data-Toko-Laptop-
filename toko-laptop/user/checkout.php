<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek keranjang
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$user = query("SELECT * FROM tb_user WHERE user_id = $user_id")[0];

// Ambil data metode pembayaran
$payments = query("SELECT * FROM tb_pembayaran");

// Hitung total dan ambil detail barang di keranjang
$total = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $barang_id => $qty) {
    $barang = query("SELECT * FROM tb_barang WHERE barang_id = $barang_id")[0];
    $subtotal = $barang['harga_jual'] * $qty;
    $total += $subtotal;
    
    $cart_items[] = [
        'barang' => $barang,
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $pembayaran_id = $_POST['pembayaran_id'];
    $bayar = str_replace(['Rp', '.', ','], '', $_POST['bayar']);
    $kembalian = $bayar - $total;

    // Validasi pembayaran
    if ($bayar < $total) {
        $error = "Pembayaran kurang dari total belanja!";
    } else {
        // Mulai transaction
        mysqli_begin_transaction($conn);

        try {
            // 1. Insert ke tb_pembelian
            $data_pembelian = [
                'user_id' => $user_id,
                'pembayaran_id' => $pembayaran_id,
                'tanggal' => date('Y-m-d'),
                'bayar' => $bayar,
                'jumlah_pembayaran' => $total,
                'kembalian' => $kembalian
            ];

            if (tambah('tb_pembelian', $data_pembelian)) {
                $id_pembelian = mysqli_insert_id($conn);

                // 2. Insert ke tb_penjualan untuk admin
                $data_penjualan = [
                    'admin_id' => 1, // ID admin default
                    'tanggal' => date('Y-m-d H:i:s'),
                    'bayar' => $bayar,
                    'total' => $total,
                    'kembalian' => $kembalian
                ];

                if (tambah('tb_penjualan', $data_penjualan)) {
                    $penjualan_id = mysqli_insert_id($conn);

                    // 3. Insert detail dan update stok
                    foreach ($cart_items as $item) {
                        $barang = $item['barang'];
                        $qty = $item['qty'];
                        $subtotal = $item['subtotal'];

                        // Insert ke tb_detail_pembelian
                        $data_detail_pembelian = [
                            'barang_id' => $barang['barang_id'],
                            'id_pembelian' => $id_pembelian,
                            'jumlah' => $qty,
                            'subtotal' => $subtotal
                        ];
                        tambah('tb_detail_pembelian', $data_detail_pembelian);

                        // Insert ke tb_detail_penjualan
                        $data_detail_penjualan = [
                            'penjualan_id' => $penjualan_id,
                            'barang_id' => $barang['barang_id'],
                            'jumlah' => $qty,
                            'subtotal' => $subtotal
                        ];
                        tambah('tb_detail_penjualan', $data_detail_penjualan);

                        // Update stok
                        $stok_baru = $barang['stok'] - $qty;
                        if ($stok_baru < 0) {
                            throw new Exception("Stok barang {$barang['nama_barang']} tidak mencukupi!");
                        }
                        ubah('tb_barang', ['stok' => $stok_baru], "barang_id = {$barang['barang_id']}");
                    }

                    // Commit transaction
                    mysqli_commit($conn);
                    
                    // Kosongkan keranjang
                    unset($_SESSION['cart']);

                    $_SESSION['success'] = "Pembelian berhasil! Order ID: #$id_pembelian";
                    header("Location: orders.php");
                    exit;
                }
            }
            
            // Rollback jika ada yang gagal
            mysqli_rollback($conn);
            $error = "Gagal memproses pembelian!";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toko Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Toko Laptop</a>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-4">
        <h2 class="mb-4">Checkout</h2>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <!-- Detail Pengiriman -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Detail Pengiriman</h5>
                        <p class="card-text">
                            <strong>Nama:</strong> <?= $user['nama']; ?><br>
                            <strong>Alamat:</strong> <?= $user['alamat']; ?><br>
                            <strong>Telepon:</strong> <?= $user['telepon']; ?>
                        </p>
                    </div>
                </div>

                <!-- Detail Pesanan -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Pesanan</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td><?= $item['barang']['nama_barang']; ?></td>
                                        <td>Rp <?= number_format($item['barang']['harga_jual'], 0, ',', '.'); ?></td>
                                        <td><?= $item['qty']; ?></td>
                                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pembayaran</h5>
                        <form action="" method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="total" value="<?= $total; ?>">

                            <div class="mb-3">
                                <label for="pembayaran_id" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="pembayaran_id" name="pembayaran_id" required>
                                    <option value="">Pilih metode pembayaran</option>
                                    <?php foreach ($payments as $payment): ?>
                                        <option value="<?= $payment['pembayaran_id']; ?>">
                                            <?= $payment['jenis_pembayaran']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih metode pembayaran
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="bayar" class="form-label">Jumlah Bayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control rupiah-input" id="bayar" name="bayar" 
                                           required data-min="<?= $total ?>">
                                    <div class="invalid-feedback">
                                        Jumlah bayar harus minimal Rp <?= number_format($total, 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="checkout" class="btn btn-primary w-100">
                                Proses Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Format Rupiah
    document.querySelectorAll('.rupiah-input').forEach(function(input) {
        input.addEventListener('keyup', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = formatRupiah(value);
            
            // Validasi minimum
            const min = parseInt(this.dataset.min);
            const current = parseInt(value);
            
            if (current < min) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });

    function formatRupiah(angka) {
        var number_string = angka.toString(),
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
</body>
</html>