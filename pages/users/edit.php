<?php
require_once __DIR__ . '/../../shared/components.php';

ob_start();
?>

<div class="mb-4">
    <h4 class="mb-1">Edit User</h4>
    <p class="text-muted mb-0" style="font-size:0.9rem">Update data user
        <?= htmlspecialchars($user['username']) ?>
    </p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/users/<?= $user['id'] ?>/update">

            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control"
                    value="<?= htmlspecialchars($_SESSION['old']['nama_lengkap'] ?? $user['nama_lengkap']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($_SESSION['old']['username'] ?? $user['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <?php $currentRole = $_SESSION['old']['role'] ?? $user['role']; ?>
                    <option value="admin" <?= $currentRole === 'admin' ? 'selected' : '' ?>>
                        Admin (Akses Penuh)
                    </option>
                    <option value="karyawan" <?= $currentRole === 'karyawan' ? 'selected' : '' ?>>
                        Karyawan (Akses Stok Keluar)
                    </option>
                </select>
            </div>

            <hr class="my-4">

            <div class="alert alert-info mb-3" style="font-size:0.9rem">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Password:</strong> Kosongkan jika tidak ingin mengubah password
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru (Opsional)</label>
                <input type="password" name="password" class="form-control" minlength="6">
                <div class="form-text">Minimal 6 karakter</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="6">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update
                </button>
                <a href="/users" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </a>
            </div>

        </form>
    </div>
</div>

<?php
unset($_SESSION['old']);
$content = ob_get_clean();
renderLayout('Edit User', $content);
?>