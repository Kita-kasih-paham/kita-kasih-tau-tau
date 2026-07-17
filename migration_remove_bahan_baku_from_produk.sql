-- Migration: Remove bahan_baku_id from produk table
-- Date: 2026-07-15
-- Description: Produk sekarang hanya punya nama dan deskripsi, bahan-bahan diambil dari produk_detail

-- Step 1: Drop foreign key constraint first (if exists)
ALTER TABLE `produk` DROP FOREIGN KEY IF EXISTS `produk_ibfk_1`;
ALTER TABLE `produk` DROP FOREIGN KEY IF EXISTS `fk_produk_bahan_baku`;

-- Step 2: Drop the bahan_baku_id column
ALTER TABLE `produk` DROP COLUMN IF EXISTS `bahan_baku_id`;

-- Step 3: Verify structure
DESCRIBE `produk`;
