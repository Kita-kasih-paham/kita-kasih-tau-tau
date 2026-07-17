<?php

namespace Controllers;

use Models\StokMasukModel;
use Models\StokKeluarModel;

class ReportController
{
    private function getRange(): array
    {
        $filterType = $_GET['filter'] ?? null;

        if ($filterType === 'month' && !empty($_GET['month'])) {
            $from = $_GET['month'] . '-01';
            $to = date('Y-m-t', strtotime($from));
        } elseif ($filterType === 'range' && !empty($_GET['from']) && !empty($_GET['to'])) {
            $from = $_GET['from'];
            $to = $_GET['to'];
        } else {
            // No filter — default to current month
            $from = date('Y-m-01');
            $to = date('Y-m-t');
        }

        return [$from, $to, $filterType];
    }

    public function index(array $params): void
    {
        [$from, $to, $filterType] = $this->getRange();
        $isFiltered = in_array($filterType, ['month', 'range']);

        $masuk = (new StokMasukModel())->report(new \DateTime($from), new \DateTime($to));
        $keluar = (new StokKeluarModel())->report(new \DateTime($from), new \DateTime($to));
        $produksi = (new StokKeluarModel())->reportProduksi(new \DateTime($from), new \DateTime($to));

        require __DIR__ . '/../pages/report/index.php';
    }

    public function export(array $params): void
    {
        [$from, $to] = $this->getRange();

        $masuk = (new StokMasukModel())->report(new \DateTime($from), new \DateTime($to));
        $keluar = (new StokKeluarModel())->report(new \DateTime($from), new \DateTime($to));

        $filename = 'Report_Stok_' . $from . '_sd_' . $to . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html><head><meta charset="UTF-8"></head><body>';

        // Stok Masuk sheet
        echo '<table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr><td colspan="5" style="font-weight:bold;font-size:14px;background:#d4edda">STOK MASUK — ' . $from . ' s/d ' . $to . '</td></tr>';
        echo '<tr style="background:#c3e6cb;font-weight:bold"><td>#</td><td>Tanggal</td><td>Nama Bahan</td><td>Jumlah</td><td>Keterangan</td></tr>';
        foreach ($masuk as $i => $row) {
            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td>' . htmlspecialchars($row['tanggal']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nama_bahan']) . '</td>';
            echo '<td>' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
            echo '<td>' . htmlspecialchars($row['keterangan'] ?? '—') . '</td>';
            echo '</tr>';
        }
        $totalMasuk = array_sum(array_column($masuk, 'jumlah'));
        echo '<tr style="font-weight:bold;background:#f0f0f0"><td colspan="3">Total</td><td>' . number_format($totalMasuk, 0, ',', '.') . '</td><td></td></tr>';
        echo '</table>';

        echo '<br><br>';

        // Stok Keluar
        echo '<table border="1" cellpadding="4" cellspacing="0">';
        echo '<tr><td colspan="5" style="font-weight:bold;font-size:14px;background:#f8d7da">STOK KELUAR — ' . $from . ' s/d ' . $to . '</td></tr>';
        echo '<tr style="background:#f5c6cb;font-weight:bold"><td>#</td><td>Tanggal</td><td>Nama Bahan</td><td>Jumlah</td><td>Keterangan</td></tr>';
        foreach ($keluar as $i => $row) {
            echo '<tr>';
            echo '<td>' . ($i + 1) . '</td>';
            echo '<td>' . htmlspecialchars($row['tanggal']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nama_bahan']) . '</td>';
            echo '<td>' . number_format($row['jumlah'], 0, ',', '.') . '</td>';
            echo '<td>' . htmlspecialchars($row['keterangan'] ?? '—') . '</td>';
            echo '</tr>';
        }
        $totalKeluar = array_sum(array_column($keluar, 'jumlah'));
        echo '<tr style="font-weight:bold;background:#f0f0f0"><td colspan="3">Total</td><td>' . number_format($totalKeluar, 0, ',', '.') . '</td><td></td></tr>';
        echo '</table>';

        echo '</body></html>';
        exit;
    }
}
