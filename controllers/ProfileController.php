<?php

namespace Controllers;

use Core\Flash;
use Core\Validator;
use Models\UserModel;

class ProfileController
{
    public function index(array $params): void
    {
        $model = new UserModel();
        $user  = $model->find((int) $_SESSION['user']['id']);
        require __DIR__ . '/../pages/profile/index.php';
    }

    public function update(array $params): void
    {
        $rules = [
            'username' => 'required|min:3|max:50',
        ];

        // Only validate password fields if user is changing password
        $changingPassword = !empty($_POST['password']);
        if ($changingPassword) {
            $rules['password']              = 'required|min:6';
            $rules['password_confirmation'] = 'required';
        }

        $v = (new Validator($_POST))->validate($rules);

        // Manual password confirmation check
        if ($changingPassword && $_POST['password'] !== ($_POST['password_confirmation'] ?? '')) {
            Flash::setErrors(['password_confirmation' => ['Konfirmasi password tidak cocok.']], $_POST);
            header('Location: /profile'); exit;
        }

        if ($v->fails()) {
            Flash::setErrors($v->errors(), $_POST);
            header('Location: /profile'); exit;
        }

        $model = new UserModel();
        $id    = (int) $_SESSION['user']['id'];

        // Check username not taken by another user
        if ($model->isUsernameTaken($_POST['username'], $id)) {
            Flash::setErrors(['username' => ['Username sudah digunakan.']], $_POST);
            header('Location: /profile'); exit;
        }

        $data = ['username' => $_POST['username']];
        if ($changingPassword) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $model->update($id, $data);

        // Refresh session
        $_SESSION['user'] = $model->find($id);

        Flash::success('Profil berhasil diperbarui.');
        header('Location: /profile'); exit;
    }
}
