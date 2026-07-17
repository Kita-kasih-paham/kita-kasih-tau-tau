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

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tambah Produk Baru</h6>
            </div>
            <div class="card-body">
                <form action="/produk/store" method="POST" id="formProduk">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control"
                            placeholder="Contoh: Kopi Latte Classic" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"
                            placeholder="Deskripsi produk (opsional)"></textarea>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Tambah Bahan-Bahan (Ingredients) <span class="text-danger">*</span></h6>
                    <p class="text-muted" style="font-size:0.9rem">Pilih minimal 1 bahan baku yang dibutuhkan untuk
                        membuat produk ini</p>

                    <div class="mb-3">
                        <label class="form-label">Pilih Bahan Baku</label>
                        <select id="selectBahan" class="form-select">
                            <option value="">-- Pilih untuk menambahkan --</option>
                            <?php foreach ($bahanBakuList as $bahan): ?>
                                <option value="<?= $bahan['id'] ?>"
                                    data-kode="<?= htmlspecialchars($bahan['kode_bahan']) ?>"
                                    data-nama="<?= htmlspecialchars($bahan['nama_bahan']) ?>"
                                    data-satuan="<?= htmlspecialchars($bahan['satuan']) ?>">
                                    <?= htmlspecialchars($bahan['kode_bahan']) ?> -
                                    <?= htmlspecialchars($bahan['nama_bahan']) ?>
                                    (<?= htmlspecialchars($bahan['satuan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="ingredientList" class="mb-3">
                        <!-- Ingredient items will be added here dynamically -->
                    </div>

                    <div id="errorIngredient" class="alert alert-danger d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span id="errorIngredientText"></span>
                    </div>

                    <div class="alert alert-info d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill"></i>
                        <div style="font-size:0.85rem">
                            Anda bisa menambahkan atau mengubah bahan-bahan pada halaman edit setelah produk dibuat.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Produk
                        </button>
                        <a href="/produk" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ingredient-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }

    .ingredient-item:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .ingredient-info {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .ingredient-badge {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .ingredient-name {
        font-weight: 500;
        color: #1f2937;
    }

    .ingredient-input {
        width: 120px;
    }

    .ingredient-satuan {
        color: #6b7280;
        font-size: 0.9rem;
        min-width: 60px;
    }

    .btn-remove {
        color: #dc3545;
        background: transparent;
        border: 1px solid #dc3545;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background: #dc3545;
        color: white;
    }
</style>

<script>
    let ingredientCounter = 0;
    const selectedIngredients = new Set();

    document.getElementById('selectBahan').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const bahanId = selectedOption.value;

        if (!bahanId || selectedIngredients.has(bahanId)) {
            this.value = '';
            return;
        }

        const kode = selectedOption.dataset.kode;
        const nama = selectedOption.dataset.nama;
        const satuan = selectedOption.dataset.satuan;

        addIngredient(bahanId, kode, nama, satuan);
        selectedIngredients.add(bahanId);

        // Hide error if showing
        document.getElementById('errorIngredient').classList.add('d-none');

        // Reset select
        this.value = '';
    });

    function addIngredient(id, kode, nama, satuan) {
        const container = document.getElementById('ingredientList');
        const itemId = `ingredient-${ingredientCounter++}`;

        const itemHtml = `
        <div class="ingredient-item" id="${itemId}">
            <div class="ingredient-info">
                <span class="ingredient-badge">${kode}</span>
                <span class="ingredient-name">${nama}</span>
            </div>
            <input type="number" 
                name="ingredients[${id}][jumlah]" 
                class="form-control ingredient-input" 
                placeholder="Jumlah" 
                step="0.01" 
                min="0.01" 
                required>
            <span class="ingredient-satuan">${satuan}</span>
            <input type="hidden" name="ingredients[${id}][bahan_baku_id]" value="${id}">
            <button type="button" class="btn-remove" onclick="removeIngredient('${itemId}', '${id}')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', itemHtml);
    }

    function removeIngredient(itemId, bahanId) {
        document.getElementById(itemId).remove();
        selectedIngredients.delete(bahanId);
    }

    // Form validation
    document.getElementById('formProduk').addEventListener('submit', function (e) {
        const ingredientList = document.getElementById('ingredientList');
        const errorDiv = document.getElementById('errorIngredient');
        const errorText = document.getElementById('errorIngredientText');

        // Check if at least one ingredient is added
        if (ingredientList.children.length === 0) {
            e.preventDefault();
            errorText.textContent = 'Minimal 1 bahan baku harus ditambahkan!';
            errorDiv.classList.remove('d-none');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Check all jumlah inputs
        const jumlahInputs = ingredientList.querySelectorAll('input[type="number"]');
        let hasError = false;

        jumlahInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!value || value <= 0) {
                hasError = true;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (hasError) {
            e.preventDefault();
            errorText.textContent = 'Semua jumlah bahan harus diisi dan lebih dari 0!';
            errorDiv.classList.remove('d-none');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        return true;
    });
</script>

<?php
$content = ob_get_clean();
renderLayout('Tambah Produk', $content);
