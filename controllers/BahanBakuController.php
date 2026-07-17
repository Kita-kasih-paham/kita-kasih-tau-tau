<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\BahanBakuModel;

class BahanBakuController
{
    public function index(array $params): void
    {
        $model = new BahanBakuModel();
        $data = $model->allByNewest();
        require __DIR__ . '/../pages/bahan-baku/index.php';
    }

    public function store(array $params): void
    {
        $v = (new Validator($_POST))->validate([
            'kode_bahan' => 'required|min:4|max:255',
            'nama_bahan' => 'required|max:255',
            'satuan' => 'required|max:255',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /bahan-baku');
            exit;
        }

        $model = new BahanBakuModel();
        $kode = strtoupper(trim($_POST['kode_bahan']));

        if ($model->isKodeExists($kode)) {
            Flash::setErrors(['kode_bahan' => ['Kode bahan baku sudah digunakan.']], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /bahan-baku');
            exit;
        }

        $model->create([
            'kode_bahan' => $kode,
            'nama_bahan' => $_POST['nama_bahan'],
            'satuan' => $_POST['satuan'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Bahan baku berhasil ditambahkan.');
        header('Location: /bahan-baku');
        exit;
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];

        $v = (new Validator($_POST))->validate([
            'kode_bahan' => 'required|min:4|max:255',
            'nama_bahan' => 'required|max:255',
            'satuan' => 'required|max:255',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /bahan-baku');
            exit;
        }

        $model = new BahanBakuModel();
        $kode = strtoupper(trim($_POST['kode_bahan']));

        if ($model->isKodeExists($kode, $id)) {
            Flash::setErrors(['kode_bahan' => ['Kode bahan baku sudah digunakan.']], $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /bahan-baku');
            exit;
        }

        $model->update($id, [
            'kode_bahan' => $kode,
            'nama_bahan' => $_POST['nama_bahan'],
            'satuan' => $_POST['satuan'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Bahan baku berhasil diperbarui.');
        header('Location: /bahan-baku');
        exit;
    }

    public function delete(array $params): void
    {
        $id = (int) $params['id'];
        $model = new BahanBakuModel();

        // Get bahan baku info for error message
        $bahanBaku = $model->find($id);
        if (!$bahanBaku) {
            Flash::error('Bahan baku tidak ditemukan.');
            header('Location: /bahan-baku');
            exit;
        }

        // Check if bahan baku is being used
        $usageInfo = $model->getUsageInfo($id);

        if (!$usageInfo['can_delete']) {
            $errorMessage = 'Bahan baku "' . htmlspecialchars($bahanBaku['nama_bahan']) . '" tidak dapat dihapus karena:';
            $reasons = [];

            if (!empty($usageInfo['products'])) {
                $productNames = array_map(function ($p) {
                    return htmlspecialchars($p['nama_produk']);
                }, $usageInfo['products']);

                $count = count($usageInfo['products']);
                $reasons[] = 'Digunakan sebagai ingredient di ' . $count . ' produk: ' . implode(', ', $productNames);
            }

            if ($usageInfo['has_transactions']) {
                $reasons[] = 'Memiliki riwayat transaksi stok masuk/keluar';
            }

            Flash::error($errorMessage . '<br><br>• ' . implode('<br>• ', $reasons) . '<br><br><strong>Saran:</strong> Nonaktifkan bahan baku ini daripada menghapusnya.');
            header('Location: /bahan-baku');
            exit;
        }

        // Safe to delete
        $model->delete($id);
        Flash::success('Bahan baku berhasil dihapus.');
        header('Location: /bahan-baku');
        exit;
    }

    public function toggleActive(array $params): void
    {
        $id = (int) $params['id'];
        $model = new BahanBakuModel();
        $model->toggleActive($id);
        Flash::success('Status bahan baku berhasil diubah.');
        header('Location: /bahan-baku');
        exit;
    }
}
