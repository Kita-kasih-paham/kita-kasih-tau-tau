<?php
require_once __DIR__ . '/../../shared/components.php';
$errors = \Core\Flash::getErrors();
$old    = \Core\Flash::getOld();
ob_start();
?>
<div class="row justify-content-center">
<div class="col-12 col-md-7 col-lg-6">

    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-gear me-2 text-primary"></i>Ganti Username & Password
        </div>
        <div class="card-body p-4">

            <form action="/profile" method="POST">

                <div class="mb-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username"
                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['username'] ?? $user['username']) ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?= $errors['username'][0] ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-1" style="border-top:1px solid #e9ecef;margin-bottom:1.25rem"></div>

                <p class="mb-3" style="font-size:0.8rem;color:#6b7a8d;line-height:1.5">
                    <i class="bi bi-info-circle me-1"></i>
                    Kosongkan field password jika tidak ingin mengubah password.
                </p>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="newPassword"
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               placeholder="Min. 6 karakter">
                        <button type="button" class="input-group-text bg-white border-start-0"
                                onclick="togglePass('newPassword', this)" tabindex="-1" style="cursor:pointer">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= $errors['password'][0] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="confirmPassword"
                               class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                               placeholder="Ulangi password baru">
                        <button type="button" class="input-group-text bg-white border-start-0"
                                onclick="togglePass('confirmPassword', this)" tabindex="-1" style="cursor:pointer">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="invalid-feedback"><?= $errors['password_confirmation'][0] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="/dashboard" class="btn btn-light border">Batal</a>
                </div>

            </form>

            <?php if (!empty($user['updated_at'])): ?>
            <div style="border-top:1px solid #e9ecef;padding-top:1rem">
                <p class="mb-0" style="font-size:0.78rem;color:#9aa5b4;display:flex;align-items:center;gap:0.4rem">
                    <i class="bi bi-clock-history"></i>
                    Password terakhir diubah:
                    <span style="color:#6b7a8d;font-weight:600">
                        <?= date('d M Y, H:i', strtotime($user['updated_at'])) ?>
                    </span>
                </p>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
<?php
$content = ob_get_clean();
renderLayout('Ganti Password', $content);
