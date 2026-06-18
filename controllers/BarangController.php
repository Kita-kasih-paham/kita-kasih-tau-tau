<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\BarangModel;

class BarangController
{
    public function index(array $params): void
    {
        $model = new BarangModel();
        $data = $model->allByNewest();
        require __DIR__ . '/../pages/barang/index.php';
    }

    public function store(array $params): void
    {
        $v = (new Validator($_POST))->validate([
            'kode_barang' => 'required|min:4|max:255',
            'nama_barang' => 'required|max:255',
            'satuan' => 'required|max:255',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /barang');
            exit;
        }

        $model = new BarangModel();
        $kode = strtoupper(trim($_POST['kode_barang']));

        if ($model->isKodeExists($kode)) {
            Flash::setErrors(['kode_barang' => ['Kode barang sudah digunakan.']], $_POST);
            Flash::set('modal_open', 'tambah');
            header('Location: /barang');
            exit;
        }

        $model->create([
            'kode_barang' => $kode,
            'nama_barang' => $_POST['nama_barang'],
            'satuan' => $_POST['satuan'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Barang berhasil ditambahkan.');
        header('Location: /barang');
        exit;
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];

        $v = (new Validator($_POST))->validate([
            'kode_barang' => 'required|min:4|max:255',
            'nama_barang' => 'required|max:255',
            'satuan' => 'required|max:255',
        ]);

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /barang');
            exit;
        }

        $model = new BarangModel();
        $kode = strtoupper(trim($_POST['kode_barang']));

        if ($model->isKodeExists($kode, $id)) {
            Flash::setErrors(['kode_barang' => ['Kode barang sudah digunakan.']], $_POST);
            Flash::set('modal_open', 'edit');
            Flash::set('edit_id', (string) $id);
            header('Location: /barang');
            exit;
        }

        $model->update($id, [
            'kode_barang' => $kode,
            'nama_barang' => $_POST['nama_barang'],
            'satuan' => $_POST['satuan'],
            'keterangan' => $_POST['keterangan'] ?? '',
        ]);

        Flash::success('Barang berhasil diperbarui.');
        header('Location: /barang');
        exit;
    }

    public function delete(array $params): void
    {
        (new BarangModel())->delete((int) $params['id']);
        Flash::success('Barang berhasil dihapus.');
        header('Location: /barang');
        exit;
    }
}
