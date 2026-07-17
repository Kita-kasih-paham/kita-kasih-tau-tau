-- Migration: Add user tracking to stok_keluar table
-- This will track which user created each stok keluar transaction

-- Add user_id and created_by columns
ALTER TABLE `stok_keluar` 
ADD COLUMN `user_id` BIGINT UNSIGNED NULL AFTER `keterangan`,
ADD COLUMN `created_by` VARCHAR(255) NULL AFTER `user_id`,
ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`;

-- Add foreign key constraint (optional - will fail if user_id references non-existent user)
-- Uncomment if you want strict referential integrity
-- ALTER TABLE `stok_keluar` 
-- ADD CONSTRAINT `stok_keluar_user_id_foreign` 
-- FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Update existing records with NULL user_id (backward compatibility)
-- These are transactions created before user tracking was implemented
