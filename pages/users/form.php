<?php
require_once __DIR__ . '/../../shared/components.php';

ob_start();
?>

<div class="mb-4">
    <h4 class="mb-1">Tambah User Baru</h4>
    <p class="text-muted mb-0" style="font-size:0.9rem">Buat akun user baru untuk sistem</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="/users/store">

            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control"
                    value="<?= htmlspecialchars($_SESSION['old']['nama_lengkap'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>" required>
                <div class="form-text">Username untuk login ke sistem</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" <?= ($_SESSION['old']['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                        Admin (Akses Penuh)
                    </option>
                    <option value="karyawan" <?= ($_SESSION['old']['role'] ?? '') === 'karyawan' ? 'selected' : '' ?>>
                        Karyawan (Akses Stok Keluar)
                    </option>
                </select>
                <div class="form-text">
                    <strong>Admin:</strong> Akses penuh ke semua fitur<br>
                    <strong>Karyawan:</strong> Hanya dapat mengakses Stok Keluar
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" minlength="6" required>
                <div class="form-text">Minimal 6 karakter</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
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
renderLayout('Tambah User', $content);
?>