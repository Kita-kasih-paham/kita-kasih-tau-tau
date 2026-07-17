<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\StokMasukModel;
use Models\BahanBakuModel;
use Models\ProdukModel;

class StokMasukController
{
    public function index(array $params): void
    {
        $model = new StokMasukModel();
        $bahanBaku = (new BahanBakuModel())->all();
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
        require __DIR__ . '/../pages/stok-masuk/index.php';
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
            header('Location: /stok-masuk');
            exit;
        }

        (new StokMasukModel())->create([
            'bahan_baku_id' => (int) $_POST['bahan_baku_id'],
            'jumlah' => (int) $_POST['jumlah'],
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Stok masuk berhasil disimpan.');
        header('Location: /stok-masuk');
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
            header('Location: /stok-masuk');
            exit;
        }

        (new StokMasukModel())->update($id, [
            'bahan_baku_id' => (int) $_POST['bahan_baku_id'],
            'jumlah' => (int) $_POST['jumlah'],
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Stok masuk berhasil diperbarui.');
        header('Location: /stok-masuk');
        exit;
    }

    public function delete(array $params): void
    {
        (new StokMasukModel())->delete((int) $params['id']);
        Flash::success('Data stok masuk berhasil dihapus.');
        header('Location: /stok-masuk');
        exit;
    }

    public function bulk(array $params): void
    {
        // Validate basic fields
        $v = (new Validator($_POST))->validate([
            'produk_id' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            header('Location: /stok-masuk');
            exit;
        }

        $produkId = (int) $_POST['produk_id'];
        $tanggal = $_POST['tanggal'];
        $keteranganUmum = $_POST['keterangan'] ?? '';
        $items = $_POST['items'] ?? [];

        // Validate items
        if (empty($items)) {
            Flash::error('Tidak ada bahan baku yang diisi.');
            header('Location: /stok-masuk');
            exit;
        }

        $model = new StokMasukModel();
        $produkModel = new ProdukModel();
        $produk = $produkModel->getById($produkId);

        if (!$produk) {
            Flash::error('Produk tidak ditemukan.');
            header('Location: /stok-masuk');
            exit;
        }

        $successCount = 0;
        $errors = [];

        // Insert each item
        foreach ($items as $index => $item) {
            if (empty($item['bahan_baku_id']) || empty($item['jumlah'])) {
                continue;
            }

            $bahanBakuId = (int) $item['bahan_baku_id'];
            $jumlah = (int) $item['jumlah'];

            if ($jumlah < 1) {
                continue;
            }

            // Build keterangan
            $keterangan = "Stok masuk untuk produk: " . $produk['nama_produk'];
            if (!empty($keteranganUmum)) {
                $keterangan .= " | " . $keteranganUmum;
            }
            if (!empty($item['keterangan'])) {
                $keterangan .= " | " . $item['keterangan'];
            }

            try {
                $model->create([
                    'bahan_baku_id' => $bahanBakuId,
                    'jumlah' => $jumlah,
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Error pada bahan baku ID {$bahanBakuId}: " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            Flash::success("Berhasil menyimpan {$successCount} stok masuk untuk produk: {$produk['nama_produk']}.");
        }

        if (!empty($errors)) {
            Flash::error(implode('<br>', $errors));
        }

        header('Location: /stok-masuk');
        exit;
    }
}
