<?php

namespace Controllers;

use Models\BarangModel;
use Models\StokMasukModel;
use Models\StokKeluarModel;

class DashboardController
{
    public function index(array $params): void
    {
        $barangModel = new BarangModel();
        $masukModel = new StokMasukModel();
        $keluarModel = new StokKeluarModel();

        // Efficient counts — no full table fetch
        $totalBarang = $barangModel->count();
        $totalMasuk = $masukModel->count();
        $totalKeluar = $keluarModel->count();

        // Only fetch what we display — LIMIT in SQL
        $recentMasuk = $masukModel->recent(10);
        $recentKeluar = $keluarModel->recent(10);
        $recentBarang = $barangModel->recentAdded(10);

        // Fetch items with low stock (< 5)
        $lowStockItems = $barangModel->lowStock(5);

        require __DIR__ . '/../pages/dashboard/index.php';
    }
}
