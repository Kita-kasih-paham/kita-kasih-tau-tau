<?php

namespace Middleware;

use Core\Middleware;

class AdminMiddleware implements Middleware
{
    public function handle(): void
    {
        // Check if user is logged in
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Check if user is admin
        if ($_SESSION['user']['role'] !== 'admin') {
            $_SESSION['error'] = 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.';
            header('Location: /dashboard');
            exit;
        }
    }
}
