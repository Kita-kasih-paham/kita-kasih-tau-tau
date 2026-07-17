<?php

namespace Models;

use Core\Model;

class StokMasukModel extends Model
{
    protected string $table = 'stok_masuk';

    public function filter(string $from, string $to): array
    {
        $stmt = $this->db->prepare("
            SELECT sm.*, b.nama_bahan, b.kode_bahan, b.satuan
            FROM stok_masuk sm
            JOIN bahan_baku b ON b.id = sm.bahan_baku_id
            WHERE sm.tanggal BETWEEN ? AND ?
            ORDER BY sm.tanggal DESC, sm.id DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public function allWithBahanBaku(): array
    {
        return $this->db->query("
            SELECT sm.*, b.nama_bahan, b.kode_bahan, b.satuan
            FROM stok_masuk sm
            JOIN bahan_baku b ON b.id = sm.bahan_baku_id
            ORDER BY sm.tanggal DESC, sm.id DESC
        ")->fetchAll();
    }

    public function findWithBahanBaku(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT sm.*, b.nama_bahan, b.kode_bahan, b.satuan
            FROM stok_masuk sm
            JOIN bahan_baku b ON b.id = sm.bahan_baku_id
            WHERE sm.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT sm.*, b.nama_bahan, b.kode_bahan
            FROM stok_masuk sm
            JOIN bahan_baku b ON b.id = sm.bahan_baku_id
            ORDER BY sm.id DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM stok_masuk")->fetchColumn();
    }

    public function report(\DateTime $from, \DateTime $to): array
    {
        $stmt = $this->db->prepare("
            SELECT sm.*, b.nama_bahan, b.kode_bahan, sm.keterangan
            FROM stok_masuk sm
            JOIN bahan_baku b ON b.id = sm.bahan_baku_id
            WHERE sm.tanggal BETWEEN ? AND ?
            ORDER BY sm.tanggal DESC
        ");
        $stmt->execute([$from->format('Y-m-d'), $to->format('Y-m-d')]);
        return $stmt->fetchAll();
    }
}
