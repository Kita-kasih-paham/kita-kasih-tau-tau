-- Migration: Add BOM (Bill of Materials) Feature
-- Date: 2026-07-15

USE sistem_pengelolaan_stok_db;

-- 1. Add 'tipe_barang' column to barang table
ALTER TABLE `barang` 
ADD COLUMN `tipe_barang` ENUM('bahan_baku', 'produk_jadi') NOT NULL DEFAULT 'bahan_baku' AFTER `satuan`;

-- 2. Create resep/recipe table (BOM)
CREATE TABLE `resep` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `produk_id`   BIGINT UNSIGNED NOT NULL,
    `nama_resep`  VARCHAR(255) NOT NULL,
    `deskripsi`   TEXT NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `resep_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
);

-- 3. Create resep_detail table (BOM ingredients)
CREATE TABLE `resep_detail` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `resep_id`          BIGINT UNSIGNED NOT NULL,
    `bahan_baku_id`     BIGINT UNSIGNED NOT NULL,
    `jumlah_dibutuhkan` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `resep_detail_resep_id_foreign` FOREIGN KEY (`resep_id`) REFERENCES `resep` (`id`) ON DELETE CASCADE,
    CONSTRAINT `resep_detail_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
);

-- 4. Create stok_keluar_detail table (track ingredient deductions)
CREATE TABLE `stok_keluar_detail` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `stok_keluar_id`  BIGINT UNSIGNED NOT NULL,
    `bahan_baku_id`   BIGINT UNSIGNED NOT NULL,
    `jumlah_terpakai` DECIMAL(10,2) NOT NULL,
    CONSTRAINT `stok_keluar_detail_stok_keluar_id_foreign` FOREIGN KEY (`stok_keluar_id`) REFERENCES `stok_keluar` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stok_keluar_detail_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
);

-- ── SAMPLE DATA ────────────────────────────────────────────────────────────

-- Update existing barang as bahan_baku
UPDATE `barang` SET `tipe_barang` = 'bahan_baku';

-- Add new ingredients (bahan baku)
INSERT INTO `barang` (`kode_barang`, `nama_barang`, `satuan`, `tipe_barang`, `keterangan`) VALUES
('BAHAN-001', 'Biji Kopi Robusta',  'gram',  'bahan_baku', 'Biji kopi mentah'),
('BAHAN-002', 'Susu Segar',         'ml',    'bahan_baku', 'Susu cair full cream'),
('BAHAN-003', 'Gula Aren',          'gram',  'bahan_baku', 'Gula aren asli'),
('BAHAN-004', 'Air Mineral',        'ml',    'bahan_baku', 'Air mineral kemasan');

-- Add finished products (produk jadi)
INSERT INTO `barang` (`kode_barang`, `nama_barang`, `satuan`, `tipe_barang`, `keterangan`) VALUES
('PROD-001', 'Kopi Latte',          'cup',   'produk_jadi', 'Kopi susu latte'),
('PROD-002', 'Kopi Americano',      'cup',   'produk_jadi', 'Kopi hitam americano'),
('PROD-003', 'Cappuccino',          'cup',   'produk_jadi', 'Cappuccino classic');

-- Add recipes for finished products

-- Recipe 1: Kopi Latte (1 cup)
INSERT INTO `resep` (`produk_id`, `nama_resep`, `deskripsi`) VALUES
(11, 'Resep Kopi Latte', 'Kopi susu dengan rasa creamy');

INSERT INTO `resep_detail` (`resep_id`, `bahan_baku_id`, `jumlah_dibutuhkan`) VALUES
(1, 7, 15.00),   -- Biji Kopi Robusta: 15 gram
(1, 8, 200.00),  -- Susu Segar: 200 ml
(1, 9, 10.00);   -- Gula Aren: 10 gram

-- Recipe 2: Kopi Americano (1 cup)
INSERT INTO `resep` (`produk_id`, `nama_resep`, `deskripsi`) VALUES
(12, 'Resep Kopi Americano', 'Kopi hitam dengan air panas');

INSERT INTO `resep_detail` (`resep_id`, `bahan_baku_id`, `jumlah_dibutuhkan`) VALUES
(2, 7, 20.00),   -- Biji Kopi Robusta: 20 gram
(2, 10, 250.00); -- Air Mineral: 250 ml

-- Recipe 3: Cappuccino (1 cup)
INSERT INTO `resep` (`produk_id`, `nama_resep`, `deskripsi`) VALUES
(13, 'Resep Cappuccino', 'Kopi dengan foam susu tebal');

INSERT INTO `resep_detail` (`resep_id`, `bahan_baku_id`, `jumlah_dibutuhkan`) VALUES
(3, 7, 18.00),   -- Biji Kopi Robusta: 18 gram
(3, 8, 150.00),  -- Susu Segar: 150 ml
(3, 9, 8.00);    -- Gula Aren: 8 gram

-- Add initial stock for new ingredients
INSERT INTO `stok_masuk` (`barang_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(7,  5000,  '2026-07-01', 'Stock awal biji kopi'),
(8,  10000, '2026-07-01', 'Stock awal susu segar'),
(9,  2000,  '2026-07-01', 'Stock awal gula aren'),
(10, 20000, '2026-07-01', 'Stock awal air mineral');

