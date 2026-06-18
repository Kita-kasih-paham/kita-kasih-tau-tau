<?php

namespace Models;

use Core\Model;

class BarangModel extends Model
{
    protected string $table = 'barang';

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
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = b.id), 0) AS total_masuk,
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = b.id), 0) AS total_keluar,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = b.id), 0) AS stok_tersedia
            FROM barang b
            ORDER BY b.nama_barang
        ")->fetchAll();
    }

    public function recentAdded(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM barang
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
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = b.id), 0) AS stok_tersedia
            FROM barang b
            HAVING stok_tersedia > 0
            ORDER BY b.nama_barang
        ")->fetchAll();
    }

    public function allWithStockForEdit(): array
    {
        return $this->db->query("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = b.id), 0) AS stok_tersedia
            FROM barang b
            HAVING stok_tersedia >= 0
            ORDER BY b.nama_barang
        ")->fetchAll();
    }

    public function isKodeExists(string $kode, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM barang WHERE kode_barang = ? AND id != ?");
        $stmt->execute([$kode, $excludeId]);
        return (bool) $stmt->fetch();
    }

    public function lowStock(int $threshold = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*,
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = b.id), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = b.id), 0) AS stok_tersedia
            FROM barang b
            HAVING stok_tersedia < ? AND stok_tersedia >= 0
            ORDER BY stok_tersedia ASC, b.nama_barang
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM barang")->fetchColumn();
    }
}
