<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\StokKeluarModel;
use Models\BahanBakuModel;
use Models\ProdukModel;

class StokKeluarController
{
    public function index(array $params): void
    {
        $model = new StokKeluarModel();
        $bahanBakuModel = new BahanBakuModel();
        $produks = (new ProdukModel())->getAll();

        // Determine active filter type: 'month', 'range', or none (show all)
        $filterType = $_GET['filter'] ?? null;
        $from = $to = null;
        $isFiltered = false;

        if ($filterType === 'month' && !empty($_GET['month'])) {
            $parts = explode('-', $_GET['month']);
            $from = $parts[0] . '-' . $parts[1] . '-01';
            $to = date('Y-m-t', strtotime($from));
            $isFiltered = true;
        } elseif ($filterType === 'range' && !empty($_GET['from']) && !empty($_GET['to'])) {
            $from = $_GET['from'];
            $to = $_GET['to'];
            $isFiltered = true;
        }

        $data = $isFiltered ? $model->filter($from, $to) : $model->allWithBahanBaku();

        // Only show ACTIVE bahan baku in dropdown
        $bahanBakuWithStock = $bahanBakuModel->allWithStockActiveOnly();
        $bahanBakuForEdit = $bahanBakuModel->allWithStockForEditActiveOnly();

        require __DIR__ . '/../pages/stok-keluar/index.php';
    }

    public function store(array $params): void
    {
        $v = (new Validator($_POST))->validate([
            'bahan_baku_id' => 'required|numeric',
            'jumlah' => 'required|numeric|min_val:1',
            'tanggal' => 'required|date',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /stok-keluar');
            exit;
        }

        $model = new StokKeluarModel();
        $bahanBakuModel = new BahanBakuModel();
        $bahanBakuId = (int) $_POST['bahan_baku_id'];
        $jumlah = (int) $_POST['jumlah'];

        // Check if bahan baku is active
        $bahanBaku = $bahanBakuModel->find($bahanBakuId);
        if (!$bahanBaku) {
            Flash::setErrors(['bahan_baku_id' => ['Bahan baku tidak ditemukan.']], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /stok-keluar');
            exit;
        }

        if ($bahanBaku['is_active'] == 0) {
            Flash::setErrors(['bahan_baku_id' => ['Bahan baku ini sedang tidak aktif dan tidak bisa digunakan.']], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /stok-keluar');
            exit;
        }

        $stok = $model->getStokTersedia($bahanBakuId);

        if ($stok <= 0) {
            Flash::setErrors(['bahan_baku_id' => ['Bahan baku ini tidak memiliki stok tersedia.']], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /stok-keluar');
            exit;
        }

        if ($jumlah > $stok) {
            Flash::setErrors(['jumlah' => ["Jumlah melebihi stok tersedia (" . number_format($stok, 0, ',', '.') . ")."]], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /stok-keluar');
            exit;
        }

        $model->create([
            'bahan_baku_id' => $bahanBakuId,
            'jumlah' => $jumlah,
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan'] ?? '',
            'user_id' => $_SESSION['user']['id'] ?? null,
            'created_by' => $_SESSION['user']['nama_lengkap'] ?? $_SESSION['user']['username'] ?? null,
        ]);

        Flash::success('Stok keluar berhasil disimpan.');
        header('Location: /stok-keluar');
        exit;
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];

        $v = (new Validator($_POST))->validate([
            'bahan_baku_id' => 'required|numeric',
            'jumlah' => 'required|numeric|min_val:1',
            'tanggal' => 'required|date',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /stok-keluar');
            exit;
        }

        $model = new StokKeluarModel();
        $bahanBakuId = (int) $_POST['bahan_baku_id'];
        $jumlah = (int) $_POST['jumlah'];

        // Stok available = total masuk - all OTHER keluar rows for this bahan_baku
        // (excludes the record being edited so its old value doesn't double-count)
        $stok = $model->getStokTersediaExcluding($bahanBakuId, $id);

        if ($jumlah > $stok) {
            Flash::setErrors(['jumlah' => ["Jumlah melebihi stok tersedia (" . number_format($stok, 0, ',', '.') . ")."]], $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /stok-keluar');
            exit;
        }

        $model->update($id, [
            'bahan_baku_id' => $bahanBakuId,
            'jumlah' => $jumlah,
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Stok keluar berhasil diperbarui.');
        header('Location: /stok-keluar');
        exit;
    }

    public function delete(array $params): void
    {
        (new StokKeluarModel())->delete((int) $params['id']);
        Flash::success('Data stok keluar berhasil dihapus.');
        header('Location: /stok-keluar');
        exit;
    }

    public function bulk(array $params): void
    {
        // Validate basic fields
        $v = (new Validator($_POST))->validate([
            'produk_id' => 'required|numeric',
            'jumlah_produk' => 'required|numeric|min_val:1',
            'tanggal' => 'required|date',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            header('Location: /stok-keluar');
            exit;
        }

        $produkId = (int) $_POST['produk_id'];
        $jumlahProduk = (float) $_POST['jumlah_produk'];
        $tanggal = $_POST['tanggal'];
        $keterangan = $_POST['keterangan'] ?? '';

        $model = new StokKeluarModel();
        $produkModel = new ProdukModel();

        $produk = $produkModel->getById($produkId);
        if (!$produk) {
            Flash::error('Produk tidak ditemukan.');
            header('Location: /stok-keluar');
            exit;
        }

        $ingredients = $produkModel->getIngredients($produkId);
        if (empty($ingredients)) {
            Flash::error('Produk ini belum memiliki bahan baku.');
            header('Location: /stok-keluar');
            exit;
        }

        // Check if any ingredient is inactive
        $bahanBakuModel = new BahanBakuModel();
        $inactiveIssues = [];
        foreach ($ingredients as $ing) {
            $bahanBaku = $bahanBakuModel->find($ing['bahan_baku_id']);
            if ($bahanBaku && $bahanBaku['is_active'] == 0) {
                $inactiveIssues[] = $ing['nama_bahan'] . ' (tidak aktif)';
            }
        }

        if (!empty($inactiveIssues)) {
            Flash::error('Produk ini tidak bisa diproduksi karena bahan berikut sedang tidak aktif:<br>' . implode('<br>', $inactiveIssues));
            header('Location: /stok-keluar');
            exit;
        }

        // Check stock availability for all ingredients
        $stockIssues = [];
        foreach ($ingredients as $ing) {
            $needed = $ing['jumlah_dibutuhkan'] * $jumlahProduk;
            $available = $model->getStokTersedia($ing['bahan_baku_id']);

            if ($available < $needed) {
                $stockIssues[] = "{$ing['nama_bahan']}: butuh " . number_format($needed, 0, ',', '.')
                    . " {$ing['satuan']}, tersedia " . number_format($available, 0, ',', '.') . " {$ing['satuan']}";
            }
        }

        if (!empty($stockIssues)) {
            Flash::error('Stok tidak mencukupi:<br>' . implode('<br>', $stockIssues));
            header('Location: /stok-keluar');
            exit;
        }

        // Check if produk_id column exists
        $stmt = $model->getDb()->prepare("SHOW COLUMNS FROM stok_keluar LIKE 'produk_id'");
        $stmt->execute();
        $hasProdukColumn = $stmt->rowCount() > 0;

        // All checks passed, insert all ingredients
        $successCount = 0;
        $keteranganFull = "Stok Keluar: {$produk['nama_produk']} ({$jumlahProduk} unit)";
        if (!empty($keterangan)) {
            $keteranganFull .= " | " . $keterangan;
        }

        foreach ($ingredients as $ing) {
            $jumlahDibutuhkan = $ing['jumlah_dibutuhkan'] * $jumlahProduk;

            try {
                $data = [
                    'bahan_baku_id' => $ing['bahan_baku_id'],
                    'jumlah' => $jumlahDibutuhkan,
                    'tanggal' => $tanggal,
                    'keterangan' => $keteranganFull,
                    'user_id' => $_SESSION['user']['id'] ?? null,
                    'created_by' => $_SESSION['user']['nama_lengkap'] ?? $_SESSION['user']['username'] ?? null,
                ];

                // Only add produk_id columns if they exist
                if ($hasProdukColumn) {
                    $data['produk_id'] = $produkId;
                    $data['jumlah_produk'] = $jumlahProduk;
                }

                $model->create($data);
                $successCount++;
            } catch (\Exception $e) {
                Flash::error("Error pada {$ing['nama_bahan']}: " . $e->getMessage());
            }
        }

        if ($successCount > 0) {
            Flash::success("Berhasil mencatat stok keluar untuk {$successCount} bahan baku. Produk: {$produk['nama_produk']} ({$jumlahProduk} unit).");
        }

        header('Location: /stok-keluar');
        exit;
    }
}
