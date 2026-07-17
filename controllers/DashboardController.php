<?php

namespace Controllers;

use Models\BahanBakuModel;
use Models\ProdukModel;
use Models\StokMasukModel;
use Models\StokKeluarModel;

class DashboardController
{
    public function index(array $params): void
    {
        // Karyawan role redirect directly to stok keluar
        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'karyawan') {
            header('Location: /stok-keluar');
            exit;
        }

        $bahanBakuModel = new BahanBakuModel();
        $produkModel = new ProdukModel();
        $masukModel = new StokMasukModel();
        $keluarModel = new StokKeluarModel();

        // Efficient counts — no full table fetch
        $totalBahanBaku = $bahanBakuModel->count();
        $totalProduk = $produkModel->count();
        $totalMasuk = $masukModel->count();
        $totalKeluar = $keluarModel->count();

        // Only fetch what we display — LIMIT in SQL
        $recentMasuk = $masukModel->recent(10);
        $recentKeluar = $keluarModel->recent(10);
        $recentBahanBaku = $bahanBakuModel->recentAdded(10);

        // Fetch items with low stock (< 5) - only active items
        $lowStockItems = $bahanBakuModel->lowStock(5);

        // Fetch inactive items
        $inactiveItems = $bahanBakuModel->inactiveItems();

        require __DIR__ . '/../pages/dashboard/index.php';
    }
}
