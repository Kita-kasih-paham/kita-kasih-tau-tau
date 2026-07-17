-- Migration: Add produk_id to stok_keluar table
-- This allows tracking which product the stock was used for

ALTER TABLE stok_keluar 
ADD COLUMN produk_id INT NULL AFTER bahan_baku_id,
ADD COLUMN jumlah_produk DECIMAL(10,2) NULL AFTER jumlah,
ADD FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL;

-- Add index for better query performance
CREATE INDEX idx_stok_keluar_produk ON stok_keluar(produk_id);

-- Note: 
-- - produk_id: references which product was produced
-- - jumlah_produk: how many units of product were made
-- - jumlah: amount of ingredient used (existing column)
-- When stok keluar is created by product, both produk_id and jumlah_produk will be filled
-- When created manually (single ingredient), produk_id will be NULL
