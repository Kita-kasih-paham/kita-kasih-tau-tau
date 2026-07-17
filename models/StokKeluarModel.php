<?php

namespace Models;

use Core\Model;

class StokKeluarModel extends Model
{
    protected string $table = 'stok_keluar';

    public function filter(string $from, string $to): array
    {
        // Check if produk_id column exists
        $hasColumn = $this->hasColumn('stok_keluar', 'produk_id');

        if ($hasColumn) {
            $stmt = $this->db->prepare("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, p.nama_produk
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                LEFT JOIN produk p ON p.id = sk.produk_id
                WHERE sk.tanggal BETWEEN ? AND ?
                ORDER BY sk.tanggal DESC, sk.id DESC
            ");
        } else {
            $stmt = $this->db->prepare("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, NULL as nama_produk
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                WHERE sk.tanggal BETWEEN ? AND ?
                ORDER BY sk.tanggal DESC, sk.id DESC
            ");
        }

        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public function allWithBahanBaku(): array
    {
        // Check if columns exist
        $hasProdukId = $this->hasColumn('stok_keluar', 'produk_id');
        $hasUserId = $this->hasColumn('stok_keluar', 'user_id');

        $selectUser = $hasUserId
            ? ', u.username, u.nama_lengkap as user_nama_lengkap'
            : ', NULL as username, NULL as user_nama_lengkap';

        $joinUser = $hasUserId
            ? 'LEFT JOIN users u ON u.id = sk.user_id'
            : '';

        if ($hasProdukId) {
            return $this->db->query("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, p.nama_produk{$selectUser}
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                LEFT JOIN produk p ON p.id = sk.produk_id
                {$joinUser}
                ORDER BY sk.tanggal DESC, sk.id DESC
            ")->fetchAll();
        } else {
            return $this->db->query("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, NULL as nama_produk{$selectUser}
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                {$joinUser}
                ORDER BY sk.tanggal DESC, sk.id DESC
            ")->fetchAll();
        }
    }

    public function findWithBahanBaku(int $id): array|false
    {
        // Check if columns exist
        $hasProdukId = $this->hasColumn('stok_keluar', 'produk_id');
        $hasUserId = $this->hasColumn('stok_keluar', 'user_id');

        $selectUser = $hasUserId
            ? ', u.username, u.nama_lengkap as user_nama_lengkap'
            : ', NULL as username, NULL as user_nama_lengkap';

        $joinUser = $hasUserId
            ? 'LEFT JOIN users u ON u.id = sk.user_id'
            : '';

        if ($hasProdukId) {
            $stmt = $this->db->prepare("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, p.nama_produk{$selectUser}
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                LEFT JOIN produk p ON p.id = sk.produk_id
                {$joinUser}
                WHERE sk.id = ?
            ");
        } else {
            $stmt = $this->db->prepare("
                SELECT sk.*, b.nama_bahan, b.kode_bahan, b.satuan, NULL as nama_produk{$selectUser}
                FROM stok_keluar sk
                JOIN bahan_baku b ON b.id = sk.bahan_baku_id
                {$joinUser}
                WHERE sk.id = ?
            ");
        }

        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$table} LIKE ?");
            $stmt->execute([$column]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStokTersedia(int $bahanBakuId): int
    {
        // Use subqueries to avoid fan-out from cross-joining masuk and keluar
        $stmt = $this->db->prepare("
            SELECT
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = ?), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = ?), 0)
            AS stok
        ");
        $stmt->execute([$bahanBakuId, $bahanBakuId]);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? max(0, (int) $row[0]) : 0;
    }

    public function getStokTersediaExcluding(int $bahanBakuId, int $excludeKeluarId): int
    {
        // Same as getStokTersedia but excludes one stok_keluar row (for edit: treats it as if not yet deducted)
        $stmt = $this->db->prepare("
            SELECT
                COALESCE((SELECT SUM(jumlah) FROM stok_masuk  WHERE bahan_baku_id = ?), 0) -
                COALESCE((SELECT SUM(jumlah) FROM stok_keluar WHERE bahan_baku_id = ? AND id != ?), 0)
            AS stok
        ");
        $stmt->execute([$bahanBakuId, $bahanBakuId, $excludeKeluarId]);
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? max(0, (int) $row[0]) : 0;
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT sk.*, b.nama_bahan, b.kode_bahan
            FROM stok_keluar sk
            JOIN bahan_baku b ON b.id = sk.bahan_baku_id
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
            SELECT sk.*, b.nama_bahan, b.kode_bahan, sk.keterangan
            FROM stok_keluar sk
            JOIN bahan_baku b ON b.id = sk.bahan_baku_id
            WHERE sk.tanggal BETWEEN ? AND ?
            ORDER BY sk.tanggal DESC
        ");
        $stmt->execute([$from->format('Y-m-d'), $to->format('Y-m-d')]);
        return $stmt->fetchAll();
    }

    public function reportProduksi(\DateTime $from, \DateTime $to): array
    {
        // Check if produk_id column exists
        $hasColumn = $this->hasColumn('stok_keluar', 'produk_id');

        if (!$hasColumn) {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT 
                p.id as produk_id,
                p.nama_produk,
                SUM(sk.jumlah_produk) as total_unit,
                COUNT(DISTINCT DATE(sk.tanggal)) as jumlah_hari_produksi,
                MIN(sk.tanggal) as tanggal_pertama,
                MAX(sk.tanggal) as tanggal_terakhir
            FROM stok_keluar sk
            INNER JOIN produk p ON p.id = sk.produk_id
            WHERE sk.tanggal BETWEEN ? AND ?
              AND sk.produk_id IS NOT NULL
            GROUP BY p.id, p.nama_produk
            ORDER BY total_unit DESC, p.nama_produk ASC
        ");
        $stmt->execute([$from->format('Y-m-d'), $to->format('Y-m-d')]);
        return $stmt->fetchAll();
    }

    public function reportProduksiDetail(\DateTime $from, \DateTime $to): array
    {
        // Check if produk_id column exists
        $hasColumn = $this->hasColumn('stok_keluar', 'produk_id');

        if (!$hasColumn) {
            return [];
        }

        // Get production summary with ingredients breakdown
        $stmt = $this->db->prepare("
            SELECT 
                p.id as produk_id,
                p.nama_produk,
                SUM(sk.jumlah_produk) as total_unit,
                b.kode_bahan,
                b.nama_bahan,
                b.satuan,
                pd.jumlah_dibutuhkan as per_unit,
                SUM(sk.jumlah) as total_bahan_terpakai
            FROM stok_keluar sk
            INNER JOIN produk p ON p.id = sk.produk_id
            INNER JOIN bahan_baku b ON b.id = sk.bahan_baku_id
            LEFT JOIN produk_detail pd ON pd.produk_id = p.id AND pd.bahan_baku_id = b.id
            WHERE sk.tanggal BETWEEN ? AND ?
              AND sk.produk_id IS NOT NULL
            GROUP BY p.id, p.nama_produk, b.id, b.kode_bahan, b.nama_bahan, b.satuan, pd.jumlah_dibutuhkan
            ORDER BY p.nama_produk ASC, b.nama_bahan ASC
        ");
        $stmt->execute([$from->format('Y-m-d'), $to->format('Y-m-d')]);
        return $stmt->fetchAll();
    }

    public function getDb(): \PDO
    {
        return $this->db;
    }
}
