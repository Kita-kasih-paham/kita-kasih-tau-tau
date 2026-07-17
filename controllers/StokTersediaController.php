<?php

namespace Controllers;

use Models\BahanBakuModel;

class StokTersediaController
{
    public function index(array $params): void
    {
        $model = new BahanBakuModel();
        $data = $model->allWithStok();
        require __DIR__ . '/../pages/stok-tersedia/index.php';
    }
}
