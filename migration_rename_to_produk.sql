-- Migration: Rename Resep to Produk & Barang to Bahan Baku
-- Date: 2026-07-15

USE sistem_pengelolaan_stok_db;

-- 1. Drop foreign keys first
ALTER TABLE `resep` DROP FOREIGN KEY `resep_produk_id_foreign`;
ALTER TABLE `resep_detail` DROP FOREIGN KEY `resep_detail_resep_id_foreign`;
ALTER TABLE `resep_detail` DROP FOREIGN KEY `resep_detail_bahan_baku_id_foreign`;
ALTER TABLE `stok_keluar_detail` DROP FOREIGN KEY `stok_keluar_detail_bahan_baku_id_foreign`;

-- 2. Rename tables
RENAME TABLE `resep` TO `produk`;
RENAME TABLE `resep_detail` TO `produk_detail`;
RENAME TABLE `barang` TO `bahan_baku`;

-- 3. Update column names in produk table
ALTER TABLE `produk` CHANGE `produk_id` `bahan_baku_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `produk` CHANGE `nama_resep` `nama_produk` VARCHAR(255) NOT NULL;
ALTER TABLE `bahan_baku` CHANGE `nama_barang` `nama_bahan_baku` VARCHAR(255) NOT NULL;

-- 4. Update column names in produk_detail table
ALTER TABLE `produk_detail` CHANGE `resep_id` `produk_id` BIGINT UNSIGNED NOT NULL;

-- 5. Remove tipe_barang column from bahan_baku
ALTER TABLE `bahan_baku` DROP COLUMN `tipe_barang`;

-- 6. Re-add foreign keys with new names
ALTER TABLE `produk` 
ADD CONSTRAINT `produk_bahan_baku_id_foreign` 
FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

ALTER TABLE `produk_detail` 
ADD CONSTRAINT `produk_detail_produk_id_foreign` 
FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

ALTER TABLE `produk_detail` 
ADD CONSTRAINT `produk_detail_bahan_baku_id_foreign` 
FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

ALTER TABLE `stok_keluar_detail` 
ADD CONSTRAINT `stok_keluar_detail_bahan_baku_id_foreign` 
FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

-- 7. Update existing foreign keys in stok_masuk and stok_keluar
ALTER TABLE `stok_masuk` DROP FOREIGN KEY `stok_masuk_barang_id_foreign`;
ALTER TABLE `stok_keluar` DROP FOREIGN KEY `stok_keluar_barang_id_foreign`;

ALTER TABLE `stok_masuk` CHANGE `barang_id` `bahan_baku_id` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `stok_keluar` CHANGE `barang_id` `bahan_baku_id` BIGINT UNSIGNED NOT NULL;

ALTER TABLE `stok_masuk` 
ADD CONSTRAINT `stok_masuk_bahan_baku_id_foreign` 
FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

ALTER TABLE `stok_keluar` 
ADD CONSTRAINT `stok_keluar_bahan_baku_id_foreign` 
FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

-- 8. Clean up sample data that are "produk_jadi" (id 11, 12, 13 from migration_bom.sql)
-- Keep only bahan baku
DELETE FROM `bahan_baku` WHERE id IN (11, 12, 13);
