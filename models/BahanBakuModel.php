<?php

namespace Models;

use Core\Model;

class BahanBakuModel extends Model
{
    protected string $table = 'bahan_baku';

    public function allByNewest(): array
    {
        return $this->db->query("
            SELECT * FROM {$this->table} ORDER BY created_at DESC
        ")->fetchAll();
    }

    public function allWithStok(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) AS total_masuk,
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS total_keluar,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            ORDER BY b.nama_bahan
        ")->fetchAll();
    }

    public function recentAdded(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM bahan_baku
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function allWithStock(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            HAVING stok_tersedia > 0
            ORDER BY b.nama_bahan
        ")->fetchAll();
    }

    public function allWithStockForEdit(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            HAVING stok_tersedia >= 0
            ORDER BY b.nama_bahan
        ")->fetchAll();
    }

    public function allWithStockActiveOnly(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            WHERE b.is_active = 1
            HAVING stok_tersedia > 0
            ORDER BY b.nama_bahan
        ")->fetchAll();
    }

    public function allWithStockForEditActiveOnly(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            WHERE b.is_active = 1
            HAVING stok_tersedia >= 0
            ORDER BY b.nama_bahan
        ")->fetchAll();
    }

    public function isKodeExists(string $kode, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM bahan_baku WHERE kode_bahan = ? AND id != ?");
        $stmt->execute([$kode, $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function isUsedInProducts(int $id): array
    {
        // Check if bahan baku is used as ingredient in any produk
        $stmt = $this->db->prepare("
            SELECT p.id, p.nama_produk 
            FROM produk p
            INNER JOIN produk_detail pd ON p.id = pd.produk_id
            WHERE pd.bahan_baku_id = ?
            LIMIT 5
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function hasStokTransactions(int $id): bool
    {
        // Check if there are any stock transactions
        $stmt = $this->db->prepare("
            SELECT 
                (SELECT COUNT(*) FROM stok_masuk WHERE bahan_baku_id = ?) + 
                (SELECT COUNT(*) FROM stok_keluar WHERE bahan_baku_id = ?) as total
        ");
        $stmt->execute([$id, $id]);
        $result = $stmt->fetch();
        return ($result['total'] ?? 0) > 0;
    }

    public function getUsageInfo(int $id): array
    {
        $products = $this->isUsedInProducts($id);
        $hasTransactions = $this->hasStokTransactions($id);

        return [
            'can_delete' => empty($products) && !$hasTransactions,
            'products' => $products,
            'has_transactions' => $hasTransactions
        ];
    }

    public function lowStock(int $threshold = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            WHERE b.is_active = 1
            HAVING stok_tersedia < ? AND stok_tersedia >= 0
            ORDER BY stok_tersedia ASC, b.nama_bahan
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function inactiveItems(): array
    {
        $stmt = $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = b.id), 0) AS stok_tersedia
            FROM bahan_baku b
            WHERE b.is_active = 0
            ORDER BY b.nama_bahan
        ");
        return $stmt->fetchAll();
    }

    public function toggleActive(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE bahan_baku SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM bahan_baku")->fetchColumn();
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM bahan_baku WHERE is_active = 1")->fetchColumn();
    }

    public function countInactive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM bahan_baku WHERE is_active = 0")->fetchColumn();
    }
}
