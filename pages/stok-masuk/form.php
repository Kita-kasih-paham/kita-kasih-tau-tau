<?php
require_once __DIR__ . '/../../shared/components.php';
$errors = \Core\Flash::getErrors();
$old = \Core\Flash::getOld();
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-box-arrow-in-down me-2 text-primary"></i>Form Stok Masuk</div>
            <div class="card-body p-4">
                <form action="/stok-masuk" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Barang</label>
                        <select name="barang_id" id="barangSelect"
                            class="form-select <?= isset($errors['barang_id']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?= $b['id'] ?>" data-satuan="<?= htmlspecialchars($b['satuan']) ?>"
                                    <?= ($old['barang_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['kode_barang'] . ' — ' . $b['nama_barang']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['barang_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['barang_id'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <div class="input-group">
                            <input type="text" name="jumlah_display" id="jumlahDisplay"
                                class="form-control <?= isset($errors['jumlah']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($old['jumlah'] ?? '') ?>" placeholder="Masukkan jumlah...."
                                required>
                            <span class="input-group-text" id="satuanDisplay"
                                style="min-width:80px;background:#f3f4f6;color:#6b7280;font-weight:500">
                                <i class="bi bi-box me-1"></i>
                                <span id="satuanText">-</span>
                            </span>
                        </div>
                        <input type="hidden" name="jumlah" id="jumlahHidden"
                            value="<?= htmlspecialchars($old['jumlah'] ?? '') ?>">
                        <?php if (isset($errors['jumlah'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['jumlah'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal"
                            class="form-control <?= isset($errors['tanggal']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($old['tanggal'] ?? date('Y-m-d')) ?>" required>
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
                            placeholder="Tambahkan catatan jika perlu..."><?= htmlspecialchars($old['keterangan'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                        <a href="/stok-masuk" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const displayInput = document.getElementById('jumlahDisplay');
        const hiddenInput = document.getElementById('jumlahHidden');
        const barangSelect = document.getElementById('barangSelect');
        const satuanText = document.getElementById('satuanText');

        // Format number with thousand separator
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Remove all dots and return raw number
        function parseNumber(str) {
            return str.replace(/\./g, '');
        }

        // Update satuan display when barang is selected
        function updateSatuan() {
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            if (selectedOption.value) {
                const satuan = selectedOption.getAttribute('data-satuan');
                satuanText.textContent = satuan || '-';
            } else {
                satuanText.textContent = '-';
            }
        }

        // Initialize satuan on page load
        updateSatuan();

        // Update satuan when barang selection changes
        barangSelect.addEventListener('change', updateSatuan);

        // Initialize with existing value if any
        if (displayInput.value) {
            const raw = parseNumber(displayInput.value);
            if (raw) {
                displayInput.value = formatNumber(raw);
                hiddenInput.value = raw;
            }
        }

        // Format on input
        displayInput.addEventListener('input', function (e) {
            let value = e.target.value;
            // Remove non-numeric characters except dots
            value = value.replace(/[^\d.]/g, '');
            // Remove dots to get raw number
            const raw = parseNumber(value);

            // Update hidden input with raw number
            hiddenInput.value = raw;

            // Format display input
            if (raw) {
                // Save cursor position
                const cursorPos = e.target.selectionStart;
                const oldLength = e.target.value.length;

                e.target.value = formatNumber(raw);

                // Adjust cursor position after formatting
                const newLength = e.target.value.length;
                const newCursorPos = cursorPos + (newLength - oldLength);
                e.target.setSelectionRange(newCursorPos, newCursorPos);
            } else {
                e.target.value = '';
            }
        });

        // Validate on form submit
        displayInput.closest('form').addEventListener('submit', function (e) {
            if (!hiddenInput.value || parseInt(hiddenInput.value) < 1) {
                e.preventDefault();
                displayInput.classList.add('is-invalid');
                return false;
            }
        });
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Tambah Stok Masuk', $content); ?>