<?php

namespace Controllers;

use Models\BarangModel;

class StokTersediaController
{
    public function index(array $params): void
    {
        $model = new BarangModel();
        $data  = $model->allWithStok();
        require __DIR__ . '/../pages/stok-tersedia/index.php';
    }
}
