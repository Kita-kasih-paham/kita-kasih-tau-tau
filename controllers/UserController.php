<?php

namespace Controllers;

use Models\UserModel;
use Core\Validator;

class UserController
{
    public function index(array $params): void
    {
        $model = new UserModel();
        $users = $model->all();

        require __DIR__ . '/../pages/users/index.php';
    }

    public function create(array $params): void
    {
        require __DIR__ . '/../pages/users/form.php';
    }

    public function store(array $params): void
    {
        $rules = [
            'username' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|in:admin,karyawan',
            'nama_lengkap' => 'required'
        ];

        $validator = new Validator($_POST, $rules);
        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: /users/create');
            exit;
        }

        $model = new UserModel();

        // Check if username already exists
        if ($model->findByUsername($_POST['username'])) {
            $_SESSION['error'] = 'Username sudah digunakan.';
            $_SESSION['old'] = $_POST;
            header('Location: /users/create');
            exit;
        }

        $data = [
            'username' => trim($_POST['username']),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role'],
            'nama_lengkap' => trim($_POST['nama_lengkap'])
        ];

        if ($model->create($data)) {
            $_SESSION['success'] = 'User berhasil ditambahkan.';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan user.';
        }

        header('Location: /users');
        exit;
    }

    public function edit(array $params): void
    {
        $model = new UserModel();
        $user = $model->find($params['id']);

        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: /users');
            exit;
        }

        require __DIR__ . '/../pages/users/edit.php';
    }

    public function update(array $params): void
    {
        $model = new UserModel();
        $user = $model->find($params['id']);

        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: /users');
            exit;
        }

        $rules = [
            'username' => 'required',
            'role' => 'required|in:admin,karyawan',
            'nama_lengkap' => 'required'
        ];

        // Only validate password if provided
        if (!empty($_POST['password'])) {
            $rules['password'] = 'min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }

        $validator = new Validator($_POST, $rules);
        if ($validator->fails()) {
            $_SESSION['error'] = $validator->firstError();
            $_SESSION['old'] = $_POST;
            header('Location: /users/' . $params['id'] . '/edit');
            exit;
        }

        // Check if username is taken by another user
        if ($model->isUsernameTaken($_POST['username'], $params['id'])) {
            $_SESSION['error'] = 'Username sudah digunakan oleh user lain.';
            $_SESSION['old'] = $_POST;
            header('Location: /users/' . $params['id'] . '/edit');
            exit;
        }

        $data = [
            'username' => trim($_POST['username']),
            'role' => $_POST['role'],
            'nama_lengkap' => trim($_POST['nama_lengkap'])
        ];

        // Update password if provided
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($model->update($params['id'], $data)) {
            $_SESSION['success'] = 'User berhasil diupdate.';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate user.';
        }

        header('Location: /users');
        exit;
    }

    public function delete(array $params): void
    {
        $model = new UserModel();
        $user = $model->find($params['id']);

        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: /users');
            exit;
        }

        // Prevent deleting current logged-in user
        if ($user['id'] == $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Tidak dapat menghapus user yang sedang login.';
            header('Location: /users');
            exit;
        }

        // Prevent deleting the last admin
        if ($user['role'] === 'admin') {
            $admins = $model->countByRole('admin');
            if ($admins <= 1) {
                $_SESSION['error'] = 'Tidak dapat menghapus admin terakhir.';
                header('Location: /users');
                exit;
            }
        }

        if ($model->delete($params['id'])) {
            $_SESSION['success'] = 'User berhasil dihapus.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus user.';
        }

        header('Location: /users');
        exit;
    }
}
