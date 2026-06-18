<?php

namespace Controllers;

use Models\UserModel;

class AuthController
{
    public function loginPage(array $params): void
    {
        if (!empty($_SESSION['user'])) {
            header('Location: /dashboard'); exit;
        }
        require __DIR__ . '/../pages/auth/login.php';
    }

    public function login(array $params): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $model = new UserModel();
        $user  = $model->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: /dashboard'); exit;
        }

        $_SESSION['error'] = 'Username atau password salah.';
        header('Location: /login'); exit;
    }

    public function logout(array $params): void
    {
        session_destroy();
        header('Location: /login'); exit;
    }
}
