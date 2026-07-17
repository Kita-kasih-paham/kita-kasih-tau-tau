-- Insert Sample Bahan Baku
-- Date: 2026-07-15

USE sistem_pengelolaan_stok_db;

-- Insert sample bahan baku
INSERT INTO `bahan_baku` (`kode_barang`, `nama_barang`, `satuan`, `keterangan`, `created_at`) VALUES
('BAHAN001', 'Tepung Terigu', 'kg', 'Tepung terigu protein sedang', NOW()),
('BAHAN002', 'Gula Pasir', 'kg', 'Gula pasir putih kristal', NOW()),
('BAHAN003', 'Garam', 'kg', 'Garam halus konsumsi', NOW()),
('BAHAN004', 'Mentega', 'kg', 'Mentega tawar', NOW()),
('BAHAN005', 'Telur', 'kg', 'Telur ayam negeri', NOW()),
('BAHAN006', 'Susu Cair', 'liter', 'Susu segar full cream', NOW()),
('BAHAN007', 'Coklat Bubuk', 'gram', 'Coklat bubuk murni', NOW()),
('BAHAN008', 'Vanilla Extract', 'ml', 'Ekstrak vanila murni', NOW()),
('BAHAN009', 'Baking Powder', 'gram', 'Pengembang kue', NOW()),
('BAHAN010', 'Baking Soda', 'gram', 'Soda kue', NOW()),
('BAHAN011', 'Keju Cheddar', 'gram', 'Keju cheddar parut', NOW()),
('BAHAN012', 'Minyak Goreng', 'liter', 'Minyak goreng sawit', NOW()),
('BAHAN013', 'Kacang Almond', 'gram', 'Kacang almond kupas', NOW()),
('BAHAN014', 'Kismis', 'gram', 'Kismis kering', NOW()),
('BAHAN015', 'Madu', 'ml', 'Madu murni', NOW());

-- Insert sample stok masuk untuk bahan baku
INSERT INTO `stok_masuk` (`bahan_baku_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(1, 50, '2026-07-01', 'Pembelian awal tepung terigu'),
(2, 30, '2026-07-01', 'Pembelian awal gula pasir'),
(3, 20, '2026-07-01', 'Pembelian awal garam'),
(4, 15, '2026-07-02', 'Pembelian awal mentega'),
(5, 25, '2026-07-02', 'Pembelian awal telur'),
(6, 40, '2026-07-03', 'Pembelian awal susu cair'),
(7, 5000, '2026-07-03', 'Pembelian coklat bubuk 5kg'),
(8, 500, '2026-07-04', 'Pembelian vanilla extract'),
(9, 2000, '2026-07-04', 'Pembelian baking powder 2kg'),
(10, 1000, '2026-07-05', 'Pembelian baking soda 1kg'),
(11, 3000, '2026-07-05', 'Pembelian keju cheddar 3kg'),
(12, 25, '2026-07-06', 'Pembelian minyak goreng'),
(13, 2000, '2026-07-06', 'Pembelian kacang almond 2kg'),
(14, 1500, '2026-07-07', 'Pembelian kismis'),
(15, 2000, '2026-07-07', 'Pembelian madu 2 liter');

-- Insert sample stok keluar (contoh penggunaan)
INSERT INTO `stok_keluar` (`bahan_baku_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(1, 10, '2026-07-08', 'Digunakan untuk produksi roti'),
(2, 5, '2026-07-08', 'Digunakan untuk produksi kue'),
(6, 8, '2026-07-09', 'Digunakan untuk produksi puding'),
(7, 500, '2026-07-09', 'Digunakan untuk produksi brownies'),
(11, 300, '2026-07-10', 'Digunakan untuk produksi pizza');
