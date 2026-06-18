<?php

namespace Models;

use Core\Model;

class StokKeluarModel extends Model
{
    protected string $table = 'stok_keluar';

    public function filter(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT sk.*, b.nama_barang, b.kode_barang, b.satuan
            FROM stok_keluar sk
            JOIN barang b ON b.id = sk.barang_id
            WHERE sk.tanggal BETWEEN ? AND ?
            ORDER BY sk.tanggal DESC, sk.id DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public function allWithBarang(): array
    {
        return $this->db->query("
            SELECT sk.*, b.nama_barang, b.kode_barang
            FROM stok_keluar sk
            JOIN barang b ON b.id = sk.barang_id
            ORDER BY sk.tanggal DESC, sk.id DESC
        ")->fetchAll();
    }

    public function findWithBarang(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT sk.*, b.nama_barang, b.kode_barang
            FROM stok_keluar sk
            JOIN barang b ON b.id = sk.barang_id
            WHERE sk.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getStokTersedia(int $barangId): int
    {
        // Use subqueries to avoid fan-out from cross-joining masuk and keluar
        $stmt = $this->db->prepare("
            SELECT
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = ?), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = ?), 0)
            AS stok
        ");
        $stmt->execute([$barangId, $barangId]);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? max(0, (int) $row[0]) : 0;
    }

    public function getStokTersediaExcluding(int $barangId, int $excludeKeluarId): int
    {
        // Same as getStokTersedia but excludes one stok_keluar row (for edit: treats it as if not yet deducted)
        $stmt = $this->db->prepare("
            SELECT
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE barang_id = ?), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE barang_id = ? AND id != ?), 0)
            AS stok
        ");
        $stmt->execute([$barangId, $barangId, $excludeKeluarId]);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? max(0, (int) $row[0]) : 0;
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT sk.*, b.nama_barang, b.kode_barang
            FROM stok_keluar sk
            JOIN barang b ON b.id = sk.barang_id
            ORDER BY sk.id DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM stok_keluar")->fetchColumn();
    }

    public function report(\DateTime $from, \DateTime $to): array
    {
        $stmt = $this->db->prepare("
            SELECT sk.*, b.nama_barang, b.kode_barang
            FROM stok_keluar sk
            JOIN barang b ON b.id = sk.barang_id
            WHERE sk.tanggal BETWEEN ? AND ?
            ORDER BY sk.tanggal DESC
        ");
        $stmt->execute([$from->format('Y-m-d'), $to->format('Y-m-d')]);
        return $stmt->fetchAll();
    }
}
