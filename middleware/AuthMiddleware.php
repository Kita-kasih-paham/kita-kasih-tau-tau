<?php

namespace Middleware;

use Core\Middleware;

class AuthMiddleware implements Middleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}
