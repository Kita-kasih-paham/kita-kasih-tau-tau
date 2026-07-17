<?php

use Controllers\AuthController;
use Controllers\DashboardController;
use Controllers\BahanBakuController;
use Controllers\StokMasukController;
use Controllers\StokKeluarController;
use Controllers\StokTersediaController;
use Controllers\ReportController;
use Controllers\ProfileController;
use Controllers\ProdukController;
use Controllers\UserController;

// Auth (public)
$router->get('/login', [AuthController::class, 'loginPage']);
$router->get('/', [AuthController::class, 'loginPage']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);

// User Management (Admin Only)
$router->get('/users', [UserController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/users/create', [UserController::class, 'create'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/users/store', [UserController::class, 'store'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/users/{id}/edit', [UserController::class, 'edit'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/users/{id}/update', [UserController::class, 'update'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/users/{id}/delete', [UserController::class, 'delete'])->middleware(\Middleware\AdminMiddleware::class);

// Bahan Baku (Admin Only)
$router->get('/bahan-baku', [BahanBakuController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/bahan-baku', [BahanBakuController::class, 'store'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/bahan-baku/{id}/update', [BahanBakuController::class, 'update'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/bahan-baku/{id}/delete', [BahanBakuController::class, 'delete'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/bahan-baku/{id}/toggle-active', [BahanBakuController::class, 'toggleActive'])->middleware(\Middleware\AdminMiddleware::class);

// Produk (Admin Only)
$router->get('/produk', [ProdukController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/produk/create', [ProdukController::class, 'create'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/produk/store', [ProdukController::class, 'store'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/produk/{id}', [ProdukController::class, 'view'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/produk/{id}/edit', [ProdukController::class, 'edit'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/produk/{id}/update', [ProdukController::class, 'update'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/produk/{id}/delete', [ProdukController::class, 'delete'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/produk/{id}/add-ingredient', [ProdukController::class, 'addIngredient'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/produk/{produkId}/ingredient/{ingredientId}/delete', [ProdukController::class, 'deleteIngredient'])->middleware(\Middleware\AdminMiddleware::class);

// Stok Masuk (Admin Only)
$router->get('/stok-masuk', [StokMasukController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/stok-masuk', [StokMasukController::class, 'store'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/stok-masuk/bulk', [StokMasukController::class, 'bulk'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/stok-masuk/{id}/update', [StokMasukController::class, 'update'])->middleware(\Middleware\AdminMiddleware::class);
$router->post('/stok-masuk/{id}/delete', [StokMasukController::class, 'delete'])->middleware(\Middleware\AdminMiddleware::class);

// Stok Keluar (Both Admin & Karyawan)
$router->get('/stok-keluar', [StokKeluarController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar', [StokKeluarController::class, 'store'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar/bulk', [StokKeluarController::class, 'bulk'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar/{id}/update', [StokKeluarController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar/{id}/delete', [StokKeluarController::class, 'delete'])->middleware(\Middleware\AuthMiddleware::class);

// Stok Tersedia (Admin Only)
$router->get('/stok-tersedia', [StokTersediaController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);

// Report (Admin Only)
$router->get('/report', [ReportController::class, 'index'])->middleware(\Middleware\AdminMiddleware::class);
$router->get('/report/export', [ReportController::class, 'export'])->middleware(\Middleware\AdminMiddleware::class);

// Profile / Change Password
$router->get('/profile', [ProfileController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/profile', [ProfileController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
