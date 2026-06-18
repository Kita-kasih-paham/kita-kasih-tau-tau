<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\StokKeluarModel;
use Models\BarangModel;

class StokKeluarController
{
    public function index(array $params): void
    {
        $model = new StokKeluarModel();
        $barangModel = new BarangModel();

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

        $data = $isFiltered ? $model->filter($from, $to) : $model->allWithBarang();
        $barangWithStock = $barangModel->allWithStock();
        $barangForEdit = $barangModel->allWithStockForEdit();
        require __DIR__ . '/../pages/stok-keluar/index.php';
    }

    public function store(array $params): void
    {
        $v = (new Validator($_POST))->validate([
            'barang_id' => 'required|numeric',
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
        $barangId = (int) $_POST['barang_id'];
        $jumlah = (int) $_POST['jumlah'];
        $stok = $model->getStokTersedia($barangId);

        if ($stok <= 0) {
            Flash::setErrors(['barang_id' => ['Barang ini tidak memiliki stok tersedia.']], $_POST);
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
            'barang_id' => $barangId,
            'jumlah' => $jumlah,
            'tanggal' => $_POST['tanggal'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Stok keluar berhasil disimpan.');
        header('Location: /stok-keluar');
        exit;
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];

        $v = (new Validator($_POST))->validate([
            'barang_id' => 'required|numeric',
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
        $barangId = (int) $_POST['barang_id'];
        $jumlah = (int) $_POST['jumlah'];

        // Stok available = total masuk - all OTHER keluar rows for this barang
        // (excludes the record being edited so its old value doesn't double-count)
        $stok = $model->getStokTersediaExcluding($barangId, $id);

        if ($jumlah > $stok) {
            Flash::setErrors(['jumlah' => ["Jumlah melebihi stok tersedia (" . number_format($stok, 0, ',', '.') . ")."]], $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /stok-keluar');
            exit;
        }

        $model->update($id, [
            'barang_id' => $barangId,
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
}
