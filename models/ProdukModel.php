<?php

namespace Models;

use Core\Model;

class ProdukModel extends Model
{
    /**
     * Get all produk with first ingredient as display
     */
    public function getAll(): array
    {
        $sql = "SELECT p.id, p.nama_produk, p.deskripsi, p.created_at
                FROM produk p
                ORDER BY p.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get produk by ID with details
     */
    public function getById(int $id): ?array
    {
        $sql = "SELECT p.* FROM produk p WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get produk by bahan baku ID (for checking if exists)
     */
    public function getByBahanBakuId(int $bahanBakuId): ?array
    {
        $sql = "SELECT p.* FROM produk p
                INNER JOIN produk_detail pd ON p.id = pd.produk_id
                WHERE pd.bahan_baku_id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bahanBakuId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get produk ingredients/bahan baku
     */
    public function getIngredients(int $produkId): array
    {
        $sql = "SELECT pd.*, b.kode_bahan, b.nama_bahan, b.satuan
                FROM produk_detail pd
                INNER JOIN bahan_baku b ON pd.bahan_baku_id = b.id
                WHERE pd.produk_id = ?
                ORDER BY b.nama_bahan";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$produkId]);
        return $stmt->fetchAll();
    }

    /**
     * Create new produk
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO produk (nama_produk, deskripsi) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nama_produk'],
            $data['deskripsi'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update produk
     */
    public function updateProduk(int $id, array $data): void
    {
        $sql = "UPDATE produk SET nama_produk = ?, deskripsi = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nama_produk'],
            $data['deskripsi'] ?? null,
            $id
        ]);
    }

    /**
     * Delete produk
     */
    public function deleteProduk(int $id): void
    {
        $sql = "DELETE FROM produk WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    /**
     * Add ingredient to produk
     */
    public function addIngredient(int $produkId, int $bahanBakuId, float $jumlah): void
    {
        $sql = "INSERT INTO produk_detail (produk_id, bahan_baku_id, jumlah_dibutuhkan) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$produkId, $bahanBakuId, $jumlah]);
    }

    /**
     * Update ingredient
     */
    public function updateIngredient(int $id, float $jumlah): void
    {
        $sql = "UPDATE produk_detail SET jumlah_dibutuhkan = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jumlah, $id]);
    }

    /**
     * Delete ingredient
     */
    public function deleteIngredient(int $id): void
    {
        $sql = "DELETE FROM produk_detail WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    /**
     * Delete all ingredients for a produk
     */
    public function deleteAllIngredients(int $produkId): void
    {
        $sql = "DELETE FROM produk_detail WHERE produk_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$produkId]);
    }

    /**
     * Check if produk name already exists
     */
    public function namaProdukExists(string $namaProduk, ?int $excludeProdukId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM produk WHERE LOWER(nama_produk) = LOWER(?)";
        $params = [$namaProduk];

        if ($excludeProdukId) {
            $sql .= " AND id != ?";
            $params[] = $excludeProdukId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get available stock for ingredients
     */
    public function checkIngredientStock(int $produkId, int $quantity = 1): array
    {
        $ingredients = $this->getIngredients($produkId);
        $stockInfo = [];

        foreach ($ingredients as $ingredient) {
            $bahanId = $ingredient['bahan_baku_id'];
            $needed = $ingredient['jumlah_dibutuhkan'] * $quantity;

            // Get current stock
            $sql = "SELECT 
                        COALESCE(SUM(sm.jumlah), 0) - COALESCE(SUM(sk.jumlah), 0) as stok_tersedia
                    FROM bahan_baku b
                    LEFT JOIN stok_masuk sm ON b.id = sm.bahan_baku_id
                    LEFT JOIN stok_keluar sk ON b.id = sk.bahan_baku_id
                    WHERE b.id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$bahanId]);
            $result = $stmt->fetch();
            $available = $result['stok_tersedia'] ?? 0;

            $stockInfo[] = [
                'bahan_id' => $bahanId,
                'nama_bahan' => $ingredient['nama_bahan'],
                'satuan' => $ingredient['satuan'],
                'needed' => $needed,
                'available' => $available,
                'sufficient' => $available >= $needed
            ];
        }

        return $stockInfo;
    }

    /**
     * Get count of all produk
     */
    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM produk")->fetchColumn();
    }
}
