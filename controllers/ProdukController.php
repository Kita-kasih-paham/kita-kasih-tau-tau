<?php

namespace Controllers;

use Models\ProdukModel;
use Models\BahanBakuModel;
use Core\Flash;

class ProdukController
{
    private ProdukModel $produkModel;
    private BahanBakuModel $bahanBakuModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->bahanBakuModel = new BahanBakuModel();
    }

    /**
     * Display all produk
     */
    public function index(): void
    {
        $produks = $this->produkModel->getAll();

        // Get ingredient info for each produk
        foreach ($produks as &$produk) {
            $ingredients = $this->produkModel->getIngredients($produk['id']);
            $produk['ingredient_count'] = count($ingredients);

            // Get first 3 ingredients for display
            $displayIngredients = array_slice($ingredients, 0, 3);
            $produk['ingredient_names'] = array_map(function ($ing) {
                return ucwords(strtolower($ing['nama_bahan']));
            }, $displayIngredients);

            // Calculate remaining
            $produk['ingredient_remaining'] = max(0, count($ingredients) - 3);
        }

        require_once __DIR__ . '/../pages/produk/index.php';
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        // Get all bahan baku
        $bahanBakuList = $this->bahanBakuModel->all();

        require_once __DIR__ . '/../pages/produk/form.php';
    }

    /**
     * Store new produk
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /produk');
            exit;
        }

        $namaProduk = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $ingredients = $_POST['ingredients'] ?? [];

        // Validation: nama produk harus diisi
        if (!$namaProduk) {
            Flash::set('error', 'Nama produk harus diisi');
            header('Location: /produk/create');
            exit;
        }

        // Validation: minimal 1 ingredient harus ada
        if (empty($ingredients) || !is_array($ingredients)) {
            Flash::set('error', 'Minimal 1 bahan baku harus ditambahkan');
            header('Location: /produk/create');
            exit;
        }

        // Validation: setiap ingredient harus punya jumlah yang valid
        $validIngredients = [];
        foreach ($ingredients as $bahanId => $data) {
            $bahanIdInt = (int) $bahanId;
            $jumlah = (float) ($data['jumlah'] ?? 0);

            if ($bahanIdInt <= 0) {
                Flash::set('error', 'ID bahan baku tidak valid');
                header('Location: /produk/create');
                exit;
            }

            if ($jumlah <= 0) {
                Flash::set('error', 'Jumlah bahan harus lebih dari 0');
                header('Location: /produk/create');
                exit;
            }

            $validIngredients[] = ['bahan_baku_id' => $bahanIdInt, 'jumlah' => $jumlah];
        }

        // Create produk (without bahan_baku_id)
        $produkId = $this->produkModel->create([
            'nama_produk' => $namaProduk,
            'deskripsi' => $deskripsi
        ]);

        // Add all validated ingredients
        foreach ($validIngredients as $ingredient) {
            $this->produkModel->addIngredient($produkId, $ingredient['bahan_baku_id'], $ingredient['jumlah']);
        }

        Flash::set('success', 'Produk berhasil ditambahkan dengan ' . count($validIngredients) . ' bahan');
        header("Location: /produk/$produkId/edit");
        exit;
    }

    /**
     * Show edit form
     */
    public function edit(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            Flash::set('error', 'Produk tidak ditemukan');
            header('Location: /produk');
            exit;
        }

        $ingredients = $this->produkModel->getIngredients($id);

        // Get bahan baku for adding ingredients
        $bahanBakuList = $this->bahanBakuModel->all();

        require_once __DIR__ . '/../pages/produk/edit.php';
    }

    /**
     * Update produk
     */
    public function update(array $params): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /produk');
            exit;
        }

        $id = (int) ($params['id'] ?? 0);
        $namaProduk = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        if (!$namaProduk) {
            Flash::set('error', 'Nama produk harus diisi');
            header("Location: /produk/$id/edit");
            exit;
        }

        $this->produkModel->updateProduk($id, [
            'nama_produk' => $namaProduk,
            'deskripsi' => $deskripsi
        ]);

        Flash::set('success', 'Produk berhasil diupdate');
        header("Location: /produk/$id/edit");
        exit;
    }

    /**
     * Delete produk
     */
    public function delete(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $this->produkModel->deleteProduk($id);
        Flash::set('success', 'Produk berhasil dihapus');
        header('Location: /produk');
        exit;
    }

    /**
     * Add ingredient to produk
     */
    public function addIngredient(array $params): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $id = (int) ($params['id'] ?? 0);
            header("Location: /produk/$id/edit");
            exit;
        }

        $produkId = (int) ($params['id'] ?? 0);
        $bahanBakuId = (int) ($_POST['bahan_baku_id'] ?? 0);
        $jumlah = (float) ($_POST['jumlah'] ?? 0);

        if (!$bahanBakuId || $jumlah <= 0) {
            Flash::set('error', 'Data ingredient tidak valid');
            header("Location: /produk/$produkId/edit");
            exit;
        }

        $this->produkModel->addIngredient($produkId, $bahanBakuId, $jumlah);
        Flash::set('success', 'Ingredient berhasil ditambahkan');
        header("Location: /produk/$produkId/edit");
        exit;
    }

    /**
     * Delete ingredient from produk
     */
    public function deleteIngredient(array $params): void
    {
        $produkId = (int) ($params['produkId'] ?? 0);
        $ingredientId = (int) ($params['ingredientId'] ?? 0);

        $this->produkModel->deleteIngredient($ingredientId);
        Flash::set('success', 'Ingredient berhasil dihapus');
        header("Location: /produk/$produkId/edit");
        exit;
    }

    /**
     * View produk detail (for reference)
     */
    public function view(array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        $produk = $this->produkModel->getById($id);

        if (!$produk) {
            Flash::set('error', 'Produk tidak ditemukan');
            header('Location: /produk');
            exit;
        }

        $ingredients = $this->produkModel->getIngredients($id);

        require_once __DIR__ . '/../pages/produk/view.php';
    }
}
