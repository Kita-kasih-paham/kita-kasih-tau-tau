<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../shared/components.php';

ob_start();
?>

<div class="mb-4">
    <a href="/produk" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Left Column: Produk Info -->
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Produk</h6>
            </div>
            <div class="card-body">
                <form action="/produk/<?= $produk['id'] ?>/update" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control"
                            value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control"
                            rows="3"><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Update Produk
                    </button>
                </form>
            </div>
        </div>

        <!-- Add Ingredient Form -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tambah Bahan</h6>
            </div>
            <div class="card-body">
                <form action="/produk/<?= $produk['id'] ?>/add-ingredient" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                        <select name="bahan_baku_id" id="selectBahanBaku" class="form-select" required>
                            <option value="">-- Pilih Bahan --</option>
                            <?php foreach ($bahanBakuList as $bahan): ?>
                                <option value="<?= $bahan['id'] ?>" data-satuan="<?= htmlspecialchars($bahan['satuan']) ?>"
                                    data-nama="<?= htmlspecialchars($bahan['nama_bahan']) ?>">
                                    <?= htmlspecialchars($bahan['nama_bahan']) ?>
                                    (<?= htmlspecialchars($bahan['satuan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Dibutuhkan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah" id="inputJumlah" class="form-control" step="0.01"
                                min="0.01" placeholder="Contoh: 15.5" required>
                            <span class="input-group-text" id="satuanDisplay" style="min-width:80px;background:#f8f9fa">
                                <span class="text-muted" id="satuanText">satuan</span>
                            </span>
                        </div>
                        <small class="text-muted" id="satuanHelper">
                            Per 1 unit produk
                        </small>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-lg"></i> Tambah Bahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Ingredients List -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Bahan (Ingredients)</h6>
                <span class="badge bg-primary">
                    <?= count($ingredients) ?> Bahan
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($ingredients)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size:3rem"></i>
                        <div class="mt-3">Belum ada bahan ditambahkan</div>
                        <small>Tambahkan bahan melalui form di sebelah kiri</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:40%">Nama Bahan</th>
                                    <th style="width:25%">Jumlah</th>
                                    <th style="width:20%">Satuan</th>
                                    <th style="width:10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingredients as $i => $ingredient): ?>
                                    <tr>
                                        <td>
                                            <?= $i + 1 ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($ingredient['nama_bahan']) ?>
                                        </td>
                                        <td><strong>
                                                <?= number_format($ingredient['jumlah_dibutuhkan'], 2, ',', '.') ?>
                                            </strong></td>
                                        <td>
                                            <?= htmlspecialchars($ingredient['satuan']) ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete(this)"
                                                data-form="deleteIngredient<?= $ingredient['id'] ?>" data-title="Hapus Bahan?"
                                                data-message="Bahan <?= htmlspecialchars($ingredient['nama_bahan']) ?> akan dihapus"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="deleteIngredient<?= $ingredient['id'] ?>"
                                                action="/produk/<?= $produk['id'] ?>/ingredient/<?= $ingredient['id'] ?>/delete"
                                                method="POST" style="display:none"></form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-success mt-3 mb-0">
                        <strong>Total:
                            <?= count($ingredients) ?> bahan baku
                        </strong> dibutuhkan untuk membuat 1 unit produk "<?= htmlspecialchars($produk['nama_produk']) ?>"
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Update satuan display when bahan baku is selected
    document.getElementById('selectBahanBaku').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const satuan = selectedOption.dataset.satuan || 'satuan';
        const nama = selectedOption.dataset.nama || '';

        const satuanText = document.getElementById('satuanText');
        const satuanHelper = document.getElementById('satuanHelper');
        const inputJumlah = document.getElementById('inputJumlah');

        if (this.value) {
            // Update satuan display
            satuanText.textContent = satuan;
            satuanText.className = 'fw-bold text-primary';

            // Update helper text
            satuanHelper.innerHTML = `<i class="bi bi-info-circle me-1"></i>Jumlah <strong>${satuan}</strong> yang dibutuhkan untuk 1 unit produk`;

            // Focus on input
            inputJumlah.focus();

            // Update placeholder
            inputJumlah.placeholder = `Contoh: 2.5 ${satuan}`;
        } else {
            // Reset to default
            satuanText.textContent = 'satuan';
            satuanText.className = 'text-muted';
            satuanHelper.innerHTML = 'Per 1 unit produk';
            inputJumlah.placeholder = 'Contoh: 15.5';
        }
    });
</script>

<?php
$content = ob_get_clean();
renderLayout('Edit Produk - ' . $produk['nama_produk'], $content);
