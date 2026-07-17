-- Migration: Add role to users table
-- Run this migration to add role support

-- Add role column to users table
ALTER TABLE `users` 
ADD COLUMN `role` ENUM('admin', 'karyawan') NOT NULL DEFAULT 'karyawan' AFTER `password`,
ADD COLUMN `nama_lengkap` VARCHAR(255) NULL AFTER `username`;

-- Update existing admin user
UPDATE `users` 
SET `role` = 'admin', `nama_lengkap` = 'Administrator'
WHERE `username` = 'admin';

-- Add sample karyawan user
-- Username: karyawan
-- Password: karyawan123
-- Note: Run generate_password_hash.php to generate a new hash if needed
INSERT INTO `users` (`username`, `password`, `role`, `nama_lengkap`) VALUES
('karyawan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'karyawan', 'Karyawan Demo');
