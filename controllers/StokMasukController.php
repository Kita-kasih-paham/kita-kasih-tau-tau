<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\StokMasukModel;
use Models\BarangModel;

class StokMasukController
{
    public function index(array $params): void
    {
        $model = new StokMasukModel();
        $barang = (new BarangModel())->all();

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
        require __DIR__ . '/../pages/stok-masuk/index.php';
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
            header('Location: /stok-masuk');
            exit;
        }

        (new StokMasukModel())->create([
            'barang_id' => (int) $_POST['barang_id'],
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
            'barang_id' => 'required|numeric',
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
            'barang_id' => (int) $_POST['barang_id'],
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
}
