-- Migration: Add is_active column to bahan_baku table
-- Date: 2026-07-15
-- Description: Tambah kolom is_active untuk menandai bahan baku aktif/non-aktif

-- Step 1: Add is_active column (default TRUE = active)
ALTER TABLE `bahan_baku` 
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `keterangan`;

-- Step 2: Add index for better query performance
ALTER TABLE `bahan_baku` 
ADD INDEX `idx_is_active` (`is_active`);

-- Step 3: Verify structure
DESCRIBE `bahan_baku`;

-- Step 4: Set all existing records to active
UPDATE `bahan_baku` SET `is_active` = 1;
