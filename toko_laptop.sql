-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 09:10 AM
-- Server version: 10.6.15-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`admin_id`, `username`, `password`, `nama`) VALUES
(1, 'admin', 'admin', 'mojo'),
(2, 'firman', 'admin123', 'firman');

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang`
--

CREATE TABLE `tb_barang` (
  `barang_id` int(11) NOT NULL,
  `merk_id` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jenis_barang` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `harga_beli` decimal(10,2) DEFAULT NULL,
  `harga_jual` decimal(10,2) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_barang`
--

INSERT INTO `tb_barang` (`barang_id`, `merk_id`, `kategori_id`, `nama_barang`, `jenis_barang`, `gambar`, `harga_beli`, `harga_jual`, `stok`) VALUES
(1, 1, 1, 'ASUS ROG ZEPHYRUS G16 GA605WV-R946OL7G-OM RYZEN AI 9HX 370', 'Processor Onboard : AMD Ryzen™ AI 9 HX 370 Processor 2.0GHz (36MB Cache, up to 5.1GHz, 12 cores, 24 Threads) AMD Ryzen™ AI up to 81 TOPs\r\nMemori Standar : 32GB LPDDR5X\r\nTipe Grafis : NVIDIA® GeForce RTX™ 4050 Laptop GPU 6GB GDDR6\r\nROG Boost : ROG Boost: 1', '1733467296.jpg', 25000000.00, 30000000.00, 10),
(2, 3, 1, 'HP PAVILION GAMING LAPTOP 15-EC2047AX RYZEN 5-5600H', 'Ryzen™ processor\r\nWindows 10 Home Single Language 64\r\n15.6″ diagonal, FHD (1920 x 1080), 144 Hz, IPS, micro-edge, anti-glare, 250 nits, 45% NTSC\r\n16 GB DDR4-3200 MHz RAM (2 x 8 GB)\r\n512 GB PCIe® NVMe™ M.2 SSD\r\nGame Ready. Performance Ready.\r\nNVIDIA® GeFor', '1733467232.jpg', 9000000.00, 10000000.00, 10),
(3, 2, 1, 'LENOVO LAPTOP IDEAPAD GAMING 3-B7ID RYZEN 7-6800', 'Processor Onboard : AMD Ryzen™ 7 6800H Mobile Processor (8-core/16-thread, 20MB cache, up to 4.7 GHz max boost)\r\nDisplay : 15.6″ FHD (1920×1080) IPS 300nits Anti-glare, 165Hz, 100% sRGB, DC dimmer\r\nMemori Standar : 8GB DDR5 4800Mhz\r\nHard Disk : 512GB SSD ', '1733467131.jpg', 15990000.00, 16499999.00, 9),
(4, 1, 2, 'ASUS ZENBOOK S UX5304MA-OLEDS712', 'Processor Onboard : Intel® Core™ Ultra 7 Processor 155U 1.7 GHz (12MB Cache, up to 4.8 GHz, 12 cores, 14 Threads), Intel® AI Boost NPU\r\nMemori Standar : 32GB LPDDR5X on board\r\nHard Disk : 1TB M.2 NVMe™ PCIe® 4.0 SSD\r\nTipe Grafis : Intel® Graphics\r\nUkuran ', '1733467041.jpg', 10000000.00, 12599900.00, 12),
(6, 4, 2, 'Laptop Xiaomi Redmibook 15', 'Prosesor	Intel Core i3-1115G4 Dual Core up to 4.10 GHz\r\nVGA	Integrated Intel UHD Graphics\r\nRAM	8GB DDR4 3200 MHz\r\nStorage	SSD 256 GB/512 GB\r\nLayar	TN Panel 15.6 inci Full HD 1920 x 1080\r\nSpeaker	Stereo 2x 2W Audio DTS\r\nWebcam	HD 720p\r\nBaterai	46 Wh\r\nKeybo', '1733304650.jpg', 5260000.00, 6000000.00, 11),
(7, 5, 1, 'Axioo Pongo 725', '🌟 Spesifikasi Produk :\r\n✅ Processor: Intel Core I7 12650H (3.50GHz UPTO MAX 4.70GHz)\r\n✅ Ram : 16GB I 32GB I 64GB DDR4\r\n✅ Storage : 512GB I 1TB SSD M.2 2280 PCIe® NVMe®\r\n✅ Graphics : Nvidia Geforce RTX2050-4GB\r\n✅ Display : 15.6 Full HD IPS (1920 x 1080) re', '1733304391.jpg', 8000000.00, 10000000.00, 5),
(8, 1, 1, 'ASUS LAPTOP ROG STRIX-G G513IH-R765B6T', 'Processor Onboard : AMD Ryzen™ 7 4800H Processor 2.9 GHz (8M Cache, up to 4.2 GHz)\r\nMemori Standar : 8 GB DDR4 3200MHz\r\nTipe Grafis : NVIDIA® GeForce RTX™ 1650 Laptop GPU 4GB GDDR6 With ROG Boost up to 1615MHz at 50W (65W with Dynamic Boost)\r\nUkuran Layar', '1733465947.jpg', 10000000.00, 12000000.00, 12),
(11, 3, 3, 'HP LAPTOP 250-G8 [3V356PA] i3-1115G4', 'Processor Onboard : Intel® Core™ i3-1115G4 Processor (6MB Cache, up to 4.1 GHz)\r\nMemori Standar : 4 GB DDR4\r\nTipe Grafis : Intel® HD Graphics 620\r\nDisplay : 15,6″ diagonal HD SVA eDP anti-glare WLED-backlit, 220 cd/m², 67% sRGB (1366 x 768)\r\nAudio : 2 Int', '1733466132.jpg', 6000000.00, 7000000.00, 9),
(12, 2, 2, 'LENOVO THINKPAD E14 GEN6-5BID ULTRA 7', 'Processor Onboard : Intel® Core™ Ultra 7 155U, 12C (2P + 8E + 2LPE) / 14T, Max Turbo up to 4.8GHz, 12MB\r\nMemori Standar : 16GB SO-DIMM DDR5-5600\r\nTipe Grafis : Integrated Intel® Graphics\r\nUkuran Layar : 14″ WUXGA (1920×1200) IPS 300nits Anti-glare, 45% NT', '1733467373.jpg', 20999000.00, 22000000.00, 12),
(13, 10, 1, 'ACER GAMING LAPTOP NITRO AN515-57-921P i9-11900H', 'Processor : Intel® Core™ i9-11900H processor (24MB cache, up to 4.80Ghz)\r\nMemory : 16GB DDR4 3200Mhz\r\nStorage : 512GB SSD NVMe\r\nGraphics : NVIDIA® GeForce® RTX 3060 with 6GB of GDDR6\r\nDisplay : 15.6″ display with IPS (In-Plane Switching) technology, QHD 1', '1733467476.jpg', 20199000.00, 22000000.00, 12);

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_pembelian`
--

CREATE TABLE `tb_detail_pembelian` (
  `barang_id` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `jumlah` int(10) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail_pembelian`
--

INSERT INTO `tb_detail_pembelian` (`barang_id`, `id_pembelian`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 10000000.00),
(1, 2, 2, 20000000.00),
(1, 3, 4, 40000000.00),
(1, 5, 2, 20000000.00),
(1, 8, 1, 30000000.00),
(1, 9, 1, 30000000.00),
(2, 7, 2, 20000000.00),
(3, 4, 3, 36000000.00),
(6, 10, 1, 6000000.00),
(8, 6, 1, 12000000.00),
(11, 10, 1, 7000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail_penjualan`
--

CREATE TABLE `tb_detail_penjualan` (
  `penjualan_id` int(11) DEFAULT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail_penjualan`
--

INSERT INTO `tb_detail_penjualan` (`penjualan_id`, `barang_id`, `id`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 2, 20000000.00),
(2, 1, 2, 4, 40000000.00),
(3, 3, 3, 3, 36000000.00),
(4, 1, 4, 2, 20000000.00),
(5, 8, 5, 1, 12000000.00),
(6, 2, 6, 2, 20000000.00),
(7, 1, 7, 1, 30000000.00),
(8, 1, 8, 1, 30000000.00),
(9, 6, 9, 1, 6000000.00),
(9, 11, 10, 1, 7000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kategori`
--

INSERT INTO `tb_kategori` (`kategori_id`, `nama_kategori`) VALUES
(1, 'laptop gaming'),
(2, 'Laptop Kantor'),
(3, 'Laptop Sekolah');

-- --------------------------------------------------------

--
-- Table structure for table `tb_merk`
--

CREATE TABLE `tb_merk` (
  `merk_id` int(11) NOT NULL,
  `nama_merk` varchar(255) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_merk`
--

INSERT INTO `tb_merk` (`merk_id`, `nama_merk`, `deskripsi`) VALUES
(1, 'Asus', 'merk asus'),
(2, 'Lenovo', 'lenovo anjay'),
(3, 'HP', 'HP laptop'),
(4, 'Xiaomi', 'Laptop China'),
(5, 'Axio', 'Laptop baru'),
(10, 'Acer', 'Laptop Acer');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran`
--

CREATE TABLE `tb_pembayaran` (
  `pembayaran_id` int(11) NOT NULL,
  `jenis_pembayaran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembayaran`
--

INSERT INTO `tb_pembayaran` (`pembayaran_id`, `jenis_pembayaran`) VALUES
(1, 'BCA'),
(2, 'BRI');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembelian`
--

CREATE TABLE `tb_pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pembayaran_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `bayar` decimal(10,2) DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `kembalian` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembelian`
--

INSERT INTO `tb_pembelian` (`id_pembelian`, `user_id`, `pembayaran_id`, `tanggal`, `bayar`, `jumlah_pembayaran`, `kembalian`) VALUES
(1, 5, 1, '2024-12-03', 20000000.00, 10000000.00, 10000000.00),
(2, 5, 1, '2024-12-03', 30000000.00, 20000000.00, 10000000.00),
(3, 5, 1, '2024-12-03', 50000000.00, 40000000.00, 10000000.00),
(4, 5, 2, '2024-12-04', 36000000.00, 36000000.00, 0.00),
(5, 6, 2, '2024-12-04', 25000000.00, 20000000.00, 5000000.00),
(6, 5, 1, '2024-12-04', 12500000.00, 12000000.00, 500000.00),
(7, 5, 1, '2024-12-06', 23000000.00, 20000000.00, 3000000.00),
(8, 5, 1, '2024-12-06', 32000000.00, 30000000.00, 2000000.00),
(9, 5, 1, '2024-12-06', 31000000.00, 30000000.00, 1000000.00),
(10, 5, 1, '2024-12-06', 14000000.00, 13000000.00, 1000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_penjualan`
--

CREATE TABLE `tb_penjualan` (
  `penjualan_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `bayar` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `kembalian` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_penjualan`
--

INSERT INTO `tb_penjualan` (`penjualan_id`, `admin_id`, `tanggal`, `bayar`, `total`, `kembalian`) VALUES
(1, 1, '2024-12-03 02:18:47', 30000000.00, 20000000.00, 10000000.00),
(2, 1, '2024-12-03 02:22:14', 50000000.00, 40000000.00, 10000000.00),
(3, 1, '2024-12-03 17:12:11', 36000000.00, 36000000.00, 0.00),
(4, 1, '2024-12-03 18:53:23', 25000000.00, 20000000.00, 5000000.00),
(5, 1, '2024-12-03 20:28:33', 12500000.00, 12000000.00, 500000.00),
(6, 1, '2024-12-05 20:09:22', 23000000.00, 20000000.00, 3000000.00),
(7, 1, '2024-12-06 00:46:09', 32000000.00, 30000000.00, 2000000.00),
(8, 1, '2024-12-06 00:56:36', 31000000.00, 30000000.00, 1000000.00),
(9, 1, '2024-12-06 01:35:01', 14000000.00, 13000000.00, 1000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_supplier`
--

CREATE TABLE `tb_supplier` (
  `supplier_id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_supplier`
--

INSERT INTO `tb_supplier` (`supplier_id`, `barang_id`, `nama`, `alamat`, `telepon`) VALUES
(1, 1, 'dony', 'nganjoek pusat', '0867653752671'),
(2, 2, 'dapa', 'nganjoek pusat', '087657472747'),
(3, 7, 'cimok', 'krian', '081820820808');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` longtext DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`user_id`, `nama`, `password`, `alamat`, `telepon`) VALUES
(1, 'mojo', '', 'sini', '0909090'),
(2, 'dhani', 'akudhani', 'sini', '09090909'),
(3, 'dhani', '$2y$10$nRn9LMZzLbl3x6omOsrRMOlCrmQWQZnRtpZL6/ACI/ltuC07OquJ.', 'sini', '12122121'),
(4, 'mojo', '$2y$10$cRc8EhVH0M7RLOD//h1NWe2tBgUKaaSZ2IYF8DR1d/y0jupUnMjv6', 'sini', '89898989'),
(5, 'firman', '$2y$10$lGmD.SjjrNrgnLLa2R7.wuViiNbHo/M4VzyNZKnpf/gJiKpv5fkYO', 'sini sana soino', '085784777172'),
(6, 'cimok', '$2y$10$dtsqNEVyAfbLnUKIcYuNJuqqvKr1yq378AbdsqA6m2Mk6fNlAmM6q', 'krian', '08998983983'),
(7, 'tes1', '$2y$10$64DCMcoZtOobtCn/qjgB5ubzkeCuUjhoaXJuM7iSbaaffflFRx79G', 'sanaa', '0897798788'),
(8, 'tes2', '$2y$10$YppCU4A2raE14YderKB3UOzZkIfIP2BYUahnjrEq5DqrlCKpxR.cG', 'sinisiansinaisnsain', '0823232323232'),
(9, 'tes5', '$2y$10$.qLvg6cRDow6r/srtHUxCufBlEVqfCo27wEWhUoDZ6w1Azu6Kv3ay', 'sini sana', '0876276327362'),
(10, 'tes4', '$2y$10$OEkSO2vAjQdFyUId3468I.FXG1mo7gVZVzvAn6QMDxuaX9i4yDR/S', 'sini', '085755654486'),
(11, 'daffa', '$2y$10$I1wf6AeqwkuMePehyvGwiuO2iA.cB48VECkekFIOhjzpOUJr0.Vji', 'nganjuk pusat', '0874525698');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`barang_id`),
  ADD KEY `merk_id` (`merk_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `tb_detail_pembelian`
--
ALTER TABLE `tb_detail_pembelian`
  ADD PRIMARY KEY (`barang_id`,`id_pembelian`),
  ADD KEY `id_pembelian` (`id_pembelian`);

--
-- Indexes for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `tb_merk`
--
ALTER TABLE `tb_merk`
  ADD PRIMARY KEY (`merk_id`);

--
-- Indexes for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD PRIMARY KEY (`pembayaran_id`);

--
-- Indexes for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pembayaran_id` (`pembayaran_id`);

--
-- Indexes for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  ADD PRIMARY KEY (`penjualan_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_barang`
--
ALTER TABLE `tb_barang`
  MODIFY `barang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_merk`
--
ALTER TABLE `tb_merk`
  MODIFY `merk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  MODIFY `pembayaran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  MODIFY `penjualan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD CONSTRAINT `tb_barang_ibfk_1` FOREIGN KEY (`merk_id`) REFERENCES `tb_merk` (`merk_id`),
  ADD CONSTRAINT `tb_barang_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `tb_kategori` (`kategori_id`);

--
-- Constraints for table `tb_detail_pembelian`
--
ALTER TABLE `tb_detail_pembelian`
  ADD CONSTRAINT `tb_detail_pembelian_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`),
  ADD CONSTRAINT `tb_detail_pembelian_ibfk_2` FOREIGN KEY (`id_pembelian`) REFERENCES `tb_pembelian` (`id_pembelian`);

--
-- Constraints for table `tb_detail_penjualan`
--
ALTER TABLE `tb_detail_penjualan`
  ADD CONSTRAINT `tb_detail_penjualan_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `tb_penjualan` (`penjualan_id`),
  ADD CONSTRAINT `tb_detail_penjualan_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`);

--
-- Constraints for table `tb_pembelian`
--
ALTER TABLE `tb_pembelian`
  ADD CONSTRAINT `tb_pembelian_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_user` (`user_id`),
  ADD CONSTRAINT `tb_pembelian_ibfk_2` FOREIGN KEY (`pembayaran_id`) REFERENCES `tb_pembayaran` (`pembayaran_id`);

--
-- Constraints for table `tb_penjualan`
--
ALTER TABLE `tb_penjualan`
  ADD CONSTRAINT `tb_penjualan_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tb_admin` (`admin_id`);

--
-- Constraints for table `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD CONSTRAINT `tb_supplier_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;