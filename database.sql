CREATE DATABASE IF NOT EXISTS sistem_pengelolaan_stok_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistem_pengelolaan_stok_db;

CREATE TABLE `users` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(255) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `barang` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `kode_barang` VARCHAR(255) NOT NULL UNIQUE,
    `nama_barang` VARCHAR(255) NOT NULL,
    `satuan`      VARCHAR(255) NOT NULL,
    `keterangan`  VARCHAR(255) NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `stok_masuk` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `barang_id`  BIGINT UNSIGNED NOT NULL,
    `jumlah`     INT NOT NULL,
    `tanggal`    DATE NOT NULL,
    `keterangan` TEXT NULL,
    CONSTRAINT `stok_masuk_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
);

CREATE TABLE `stok_keluar` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `barang_id`  BIGINT UNSIGNED NOT NULL,
    `jumlah`     BIGINT NOT NULL,
    `tanggal`    DATE NOT NULL,
    `keterangan` TEXT NULL,
    CONSTRAINT `stok_keluar_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
);

-- ── USERS ──────────────────────────────────────────────────────────────────
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$10$Y8SkTnUtbNRu8QVhlB0tSubNL3GobdtHBGTienjKSioVv5kNAqxDK');

-- ── BARANG ─────────────────────────────────────────────────────────────────
INSERT INTO `barang` (`kode_barang`, `nama_barang`, `satuan`, `keterangan`) VALUES
('BRG-001', 'Beras Premium',        'kg',     'Beras kualitas premium'),
('BRG-002', 'Minyak Goreng',        'liter',  'Minyak goreng kemasan 1L'),
('BRG-003', 'Gula Pasir',           'kg',     'Gula pasir putih'),
('BRG-004', 'Tepung Terigu',        'kg',     'Tepung terigu serbaguna'),
('BRG-005', 'Kopi Bubuk',           'gram',   'Kopi bubuk robusta'),
('BRG-006', 'Teh Celup',            'box',    'Isi 25 kantong per box'),
('BRG-007', 'Sabun Mandi',          'pcs',    'Sabun batang 85gr'),
('BRG-008', 'Deterjen Bubuk',       'kg',     'Deterjen untuk mesin cuci'),
('BRG-009', 'Mie Instan',           'karton', 'Isi 40 pcs per karton'),
('BRG-010', 'Air Mineral Galon',    'unit',   'Galon 19 liter');

-- ── STOK MASUK ─────────────────────────────────────────────────────────────
INSERT INTO `stok_masuk` (`barang_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(1,  100, '2026-03-01', 'Pembelian awal bulan'),
(2,   50, '2026-03-02', 'Restock minyak goreng'),
(3,   80, '2026-03-05', 'Pembelian gula pasir'),
(4,   60, '2026-03-07', 'Restock tepung terigu'),
(5,  200, '2026-03-10', 'Pembelian kopi bubuk'),
(6,   30, '2026-03-12', 'Restock teh celup'),
(7,  150, '2026-03-15', 'Pembelian sabun mandi'),
(8,   40, '2026-03-18', 'Restock deterjen'),
(9,   20, '2026-03-20', 'Pembelian mie instan'),
(10,  15, '2026-03-22', 'Restock air mineral galon');

-- ── STOK KELUAR ────────────────────────────────────────────────────────────
INSERT INTO `stok_keluar` (`barang_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(1,  20, '2026-03-03', 'Penjualan harian'),
(2,  10, '2026-03-04', 'Penjualan minyak goreng'),
(3,  15, '2026-03-06', 'Penjualan gula pasir'),
(4,  10, '2026-03-08', 'Penjualan tepung terigu'),
(5,  50, '2026-03-11', 'Penjualan kopi bubuk'),
(6,   5, '2026-03-13', 'Penjualan teh celup'),
(7,  30, '2026-03-16', 'Penjualan sabun mandi'),
(8,  10, '2026-03-19', 'Penjualan deterjen'),
(9,   5, '2026-03-21', 'Penjualan mie instan'),
(10,  3, '2026-03-23', 'Penjualan air mineral galon');
