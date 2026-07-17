<?php
require_once __DIR__ . '/../../shared/components.php';
$errors = \Core\Flash::getErrors();
$old = \Core\Flash::getOld();
$val = fn(string $field) => htmlspecialchars($old[$field] ?? $stokKeluar[$field] ?? '');
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Stok Keluar</div>
            <div class="card-body p-4">
                <form action="/stok-keluar/<?= $stokKeluar['id'] ?>/update" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Bahan Baku</label>
                        <select name="bahan_baku_id"
                            class="form-select <?= isset($errors['bahan_baku_id']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Bahan Baku --</option>
                            <?php foreach ($bahanBaku as $b): ?>
                                <?php $selected = ($old['bahan_baku_id'] ?? $stokKeluar['bahan_baku_id']) == $b['id'] ? 'selected' : ''; ?>
                                <option value="<?= $b['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($b['kode_bahan'] . ' — ' . $b['nama_bahan']) ?>
                                    (stok: <?= $b['stok_tersedia'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['bahan_baku_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['bahan_baku_id'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" min="1"
                            class="form-control <?= isset($errors['jumlah']) ? 'is-invalid' : '' ?>"
                            value="<?= $val('jumlah') ?>" required>
                        <?php if (isset($errors['jumlah'])): ?>
                            <div class="invalid-feedback"><?= $errors['jumlah'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal"
                            class="form-control <?= isset($errors['tanggal']) ? 'is-invalid' : '' ?>"
                            value="<?= $val('tanggal') ?>" required>
                        <?php if (isset($errors['tanggal'])): ?>
                            <div class="invalid-feedback"><?= $errors['tanggal'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">
                            Keterangan
                            <span class="text-muted" style="font-size:0.78rem;font-weight:400">(opsional)</span>
                        </label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan jika perlu..."><?= $val('keterangan') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                        <a href="/stok-keluar" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean();
renderLayout('Edit Stok Keluar', $content); ?>