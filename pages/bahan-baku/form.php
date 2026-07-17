<?php
require_once __DIR__ . '/../../shared/components.php';
$errors = \Core\Flash::getErrors();
$old = \Core\Flash::getOld();
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-box-seam me-2 text-primary"></i>Form Tambah Barang</div>
            <div class="card-body p-4">
                <form action="/barang" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" name="kode_barang"
                            class="form-control <?= isset($errors['kode_barang']) ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars($old['kode_barang'] ?? '') ?>"
                            placeholder="Masukkan kode barang..." style="text-transform:uppercase" autocomplete="off"
                            required>
                        <?php if (isset($errors['kode_barang'])): ?>
                            <div class="invalid-feedback"><?= $errors['kode_barang'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" autocomplete="off"
                            class="form-control <?= isset($errors['nama_barang']) ? 'is-invalid' : '' ?>"
                            placeholder="Masukkan nama barang..."
                            value="<?= htmlspecialchars($old['nama_barang'] ?? '') ?>" required>
                        <?php if (isset($errors['nama_barang'])): ?>
                            <div class="invalid-feedback"><?= $errors['nama_barang'][0] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <div class="satuan-wrapper" style="position:relative">
                            <input type="text" name="satuan" id="satuanInput"
                                class="form-control <?= isset($errors['satuan']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($old['satuan'] ?? '') ?>"
                                placeholder="Ketik atau pilih satuan..." autocomplete="off" required>
                            <div id="satuanDropdown" style="
                        display:none;
                        position:absolute;
                        top:calc(100% + 4px);
                        left:0; right:0;
                        background:#fff;
                        border:1px solid #dde3ea;
                        border-radius:8px;
                        box-shadow:0 4px 16px rgba(0,0,0,0.1);
                        z-index:999;
                        max-height:220px;
                        overflow-y:auto;
                    "></div>
                        </div>
                        <?php if (isset($errors['satuan'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['satuan'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <script>
                        (function () {
                            const units = [
                                'pcs', 'kg', 'gram', 'liter', 'ml', 'meter', 'cm',
                                'box', 'lusin', 'karton', 'roll', 'lembar',
                                'unit', 'set', 'botol', 'kaleng', 'pak', 'buah'
                            ];

                            const input = document.getElementById('satuanInput');
                            const dropdown = document.getElementById('satuanDropdown');

                            function render(filter) {
                                const q = (filter || '').toLowerCase();
                                const filtered = units.filter(u => u.includes(q));
                                if (!filtered.length) { dropdown.style.display = 'none'; return; }

                                dropdown.innerHTML = filtered.map(u => `
                        <div class="satuan-item" data-val="${u}"
                             style="padding:0.5rem 0.85rem;cursor:pointer;font-size:0.875rem;color:#1a2332">
                            ${u}
                        </div>`).join('');

                                dropdown.querySelectorAll('.satuan-item').forEach(item => {
                                    item.addEventListener('mouseenter', () => item.style.background = '#f0f4f8');
                                    item.addEventListener('mouseleave', () => item.style.background = '');
                                    item.addEventListener('mousedown', (e) => {
                                        e.preventDefault();
                                        input.value = item.dataset.val;
                                        dropdown.style.display = 'none';
                                    });
                                });

                                dropdown.style.display = 'block';
                            }

                            input.addEventListener('focus', () => render(input.value));
                            input.addEventListener('input', () => render(input.value));
                            input.addEventListener('blur', () => setTimeout(() => dropdown.style.display = 'none', 150));
                        })();
                    </script>
                    <div class="mb-4">
                        <label class="form-label">
                            Keterangan
                            <span class="text-muted" style="font-size:0.78rem;font-weight:400">(opsional)</span>
                        </label>
                        <textarea name="keterangan" class="form-control" rows="4"
                            placeholder="Tambahkan catatan jika perlu..."><?= htmlspecialchars($old['keterangan'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                        <a href="/barang" class="btn btn-light border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean();
renderLayout('Tambah Barang', $content); ?>