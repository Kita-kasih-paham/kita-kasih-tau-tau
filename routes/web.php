<?php

use Controllers\AuthController;
use Controllers\DashboardController;
use Controllers\BarangController;
use Controllers\StokMasukController;
use Controllers\StokKeluarController;
use Controllers\StokTersediaController;
use Controllers\ReportController;
use Controllers\ProfileController;

// Auth (public)
$router->get('/login', [AuthController::class, 'loginPage']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'])
       ->middleware(\Middleware\AuthMiddleware::class);

// Barang
$router->get('/barang', [BarangController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/barang', [BarangController::class, 'store'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/barang/{id}/update', [BarangController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/barang/{id}/delete', [BarangController::class, 'delete'])->middleware(\Middleware\AuthMiddleware::class);

// Stok Masuk
$router->get('/stok-masuk', [StokMasukController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-masuk', [StokMasukController::class, 'store'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-masuk/{id}/update', [StokMasukController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-masuk/{id}/delete', [StokMasukController::class, 'delete'])->middleware(\Middleware\AuthMiddleware::class);

// Stok Keluar
$router->get('/stok-keluar', [StokKeluarController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar', [StokKeluarController::class, 'store'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar/{id}/update', [StokKeluarController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/stok-keluar/{id}/delete', [StokKeluarController::class, 'delete'])->middleware(\Middleware\AuthMiddleware::class);

// Stok Tersedia
$router->get('/stok-tersedia', [StokTersediaController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);

// Report
$router->get('/report', [ReportController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->get('/report/export', [ReportController::class, 'export'])->middleware(\Middleware\AuthMiddleware::class);

// Profile / Change Password
$router->get('/profile', [ProfileController::class, 'index'])->middleware(\Middleware\AuthMiddleware::class);
$router->post('/profile', [ProfileController::class, 'update'])->middleware(\Middleware\AuthMiddleware::class);
