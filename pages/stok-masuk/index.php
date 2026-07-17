<?php
require_once __DIR__ . '/../../shared/components.php';

$errors = \Core\Flash::getErrors();
$old = \Core\Flash::getOld();
$modalOpen = \Core\Flash::get('modal_open');
$editId = \Core\Flash::get('edit_id');

$filterType = $_GET['filter'] ?? null; // 'month' | 'range' | null
$isFiltered = in_array($filterType, ['month', 'range']);

ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">

        <!-- Filter type tabs -->
        <div class="d-flex gap-2 mb-3">
            <button type="button"
                class="btn btn-sm <?= $filterType === 'month' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                onclick="setFilterType('month')">
                <i class="bi bi-calendar-month me-1"></i>Per Bulan
            </button>
            <button type="button"
                class="btn btn-sm <?= $filterType === 'range' ? 'btn-primary' : 'btn-outline-secondary' ?>"
                onclick="setFilterType('range')">
                <i class="bi bi-calendar-range me-1"></i>Rentang Tanggal
            </button>
            <?php if ($isFiltered): ?>
                <a href="/stok-masuk" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-x-circle me-1"></i>Batalkan Filter
                </a>
            <?php endif; ?>
        </div>

        <!-- Month filter -->
        <form method="GET" id="formMonth" <?= $filterType !== 'month' ? 'style="display:none"' : '' ?>>
            <input type="hidden" name="filter" value="month">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-4">
                    <label class="form-label">Pilih Bulan</label>
                    <input type="month" name="month" class="form-control"
                        value="<?= htmlspecialchars($_GET['month'] ?? date('Y-m')) ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Range filter -->
        <form method="GET" id="formRange" <?= $filterType !== 'range' ? 'style="display:none"' : '' ?>>
            <input type="hidden" name="filter" value="range">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-4">
                    <label class="form-label">Dari</label>
                    <input type="date" name="from" class="form-control"
                        value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                </div>
                <div class="col-12 col-sm-4">
                    <label class="form-label">Sampai</label>
                    <input type="date" name="to" class="form-control"
                        value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <?php if (!$isFiltered): ?>
            <p class="text-muted mb-0 mt-1" style="font-size:0.82rem">
                <i class="bi bi-info-circle me-1"></i>Pilih filter di atas untuk menyaring data berdasarkan periode
                tertentu.
            </p>
        <?php endif; ?>

    </div>
</div>

<!-- Active filter info -->
<?php if ($isFiltered): ?>
    <p class="text-muted mb-3" style="font-size:0.82rem">
        <i class="bi bi-funnel-fill me-1 text-primary"></i>
        <?php if ($filterType === 'month'): ?>
            Menampilkan bulan: <strong><?= date('F Y', strtotime($from)) ?></strong>
        <?php else: ?>
            Rentang: <strong><?= fmtDate($from) ?></strong> s/d <strong><?= fmtDate($to) ?></strong>
        <?php endif; ?>
        &nbsp;·&nbsp; <strong><?= count($data) ?></strong> transaksi
    </p>
<?php else: ?>
    <p class="text-muted mb-3" style="font-size:0.82rem">
        <i class="bi bi-list-ul me-1"></i>
        Menampilkan <strong>semua data</strong> &nbsp;·&nbsp; <strong><?= count($data) ?></strong> transaksi
    </p>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div></div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm" onclick="openTambahByProduk()">
            <i class="bi bi-box-seam me-1"></i> Tambah by Produk
        </button>
        <button type="button" class="btn btn-primary btn-sm" onclick="openTambah()">
            <i class="bi bi-plus-lg me-1"></i> Tambah Stok Masuk
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tblStokMasuk" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Kode Bahan</th>
                    <th>Nama Bahan</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $i => $row): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td style="white-space:nowrap"><?= fmtDate($row['tanggal']) ?></td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_bahan']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_bahan']) ?></td>
                        <td style="color:#0e9f6e;font-weight:600"><?= fmt($row['jumlah']) ?> <span class="text-muted"
                                style="font-size:0.82rem;font-weight:400"><?= htmlspecialchars($row['satuan']) ?></span>
                        </td>
                        <td class="text-muted">
                            <?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '<span style="font-size:0.78rem">—</span>' ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm"
                                    style="color:#0f6cbd;background:rgba(15,108,189,0.08);border:none"
                                    onclick="openEdit(<?= $row['id'] ?>, <?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form id="del-sm-<?= $row['id'] ?>" action="/stok-masuk/<?= $row['id'] ?>/delete"
                                    method="POST">
                                    <button type="button" class="btn btn-sm"
                                        style="color:#e02424;background:rgba(224,36,36,0.08);border:none"
                                        onclick="confirmDelete(this)" data-form="del-sm-<?= $row['id'] ?>"
                                        data-message="Data stok masuk ini akan dihapus permanen.">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===================== MODAL TAMBAH ===================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title">
                    <i class="bi bi-box-arrow-in-down me-2 text-primary"></i>Tambah Stok Masuk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/stok-masuk" method="POST">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Bahan Baku</label>
                        <select name="bahan_baku_id" id="t_bahan_baku_id"
                            class="form-select <?= ($modalOpen === 'tambah' && isset($errors['bahan_baku_id'])) ? 'is-invalid' : '' ?>"
                            required>
                            <option value="">-- Pilih Bahan Baku --</option>
                            <?php foreach ($bahanBaku as $b): ?>
                                <option value="<?= $b['id'] ?>" data-satuan="<?= htmlspecialchars($b['satuan']) ?>"
                                    <?= ($modalOpen === 'tambah' && ($old['bahan_baku_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($b['kode_bahan'] . ' — ' . $b['nama_bahan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($modalOpen === 'tambah' && isset($errors['bahan_baku_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['bahan_baku_id'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <div class="input-group">
                            <input type="text" name="jumlah_display" id="t_jumlah_display"
                                class="form-control <?= ($modalOpen === 'tambah' && isset($errors['jumlah'])) ? 'is-invalid' : '' ?>"
                                value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['jumlah'] ?? '') : '' ?>"
                                placeholder="Masukkan jumlah..." required>
                            <span class="input-group-text"
                                style="min-width:80px;background:#f3f4f6;color:#6b7280;font-weight:500">
                                <i class="bi bi-box me-1"></i><span id="t_satuanText">-</span>
                            </span>
                        </div>
                        <input type="hidden" name="jumlah" id="t_jumlah_hidden"
                            value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['jumlah'] ?? '') : '' ?>">
                        <?php if ($modalOpen === 'tambah' && isset($errors['jumlah'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['jumlah'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal"
                            class="form-control <?= ($modalOpen === 'tambah' && isset($errors['tanggal'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['tanggal'] ?? date('Y-m-d')) : date('Y-m-d') ?>"
                            required>
                        <?php if ($modalOpen === 'tambah' && isset($errors['tanggal'])): ?>
                            <div class="invalid-feedback"><?= $errors['tanggal'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Keterangan
                            <span class="text-muted" style="font-size:0.78rem;font-weight:400">(opsional)</span>
                        </label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan jika perlu..."><?= ($modalOpen === 'tambah') ? htmlspecialchars($old['keterangan'] ?? '') : '' ?></textarea>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== MODAL EDIT ===================== -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Stok Masuk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Bahan Baku</label>
                        <select name="bahan_baku_id" id="e_bahan_baku_id"
                            class="form-select <?= ($modalOpen === 'edit' && isset($errors['bahan_baku_id'])) ? 'is-invalid' : '' ?>"
                            required>
                            <option value="">-- Pilih Bahan Baku --</option>
                            <?php foreach ($bahanBaku as $b): ?>
                                <option value="<?= $b['id'] ?>" data-satuan="<?= htmlspecialchars($b['satuan']) ?>">
                                    <?= htmlspecialchars($b['kode_bahan'] . ' — ' . $b['nama_bahan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($modalOpen === 'edit' && isset($errors['bahan_baku_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['bahan_baku_id'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <div class="input-group">
                            <input type="text" name="jumlah_display" id="e_jumlah_display"
                                class="form-control <?= ($modalOpen === 'edit' && isset($errors['jumlah'])) ? 'is-invalid' : '' ?>"
                                value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['jumlah'] ?? '') : '' ?>"
                                placeholder="Masukkan jumlah..." required>
                            <span class="input-group-text"
                                style="min-width:80px;background:#f3f4f6;color:#6b7280;font-weight:500">
                                <i class="bi bi-box me-1"></i><span id="e_satuanText">-</span>
                            </span>
                        </div>
                        <input type="hidden" name="jumlah" id="e_jumlah_hidden"
                            value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['jumlah'] ?? '') : '' ?>">
                        <?php if ($modalOpen === 'edit' && isset($errors['jumlah'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['jumlah'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="e_tanggal"
                            class="form-control <?= ($modalOpen === 'edit' && isset($errors['tanggal'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['tanggal'] ?? '') : '' ?>"
                            required>
                        <?php if ($modalOpen === 'edit' && isset($errors['tanggal'])): ?>
                            <div class="invalid-feedback"><?= $errors['tanggal'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Keterangan
                            <span class="text-muted" style="font-size:0.78rem;font-weight:400">(opsional)</span>
                        </label>
                        <textarea name="keterangan" id="e_keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== MODAL TAMBAH BY PRODUK ===================== -->
<div class="modal fade" id="modalTambahByProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam me-2 text-success"></i>Tambah Stok Masuk by Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/stok-masuk/bulk" method="POST" id="formBulk">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Pilih Produk</label>
                        <select name="produk_id" id="produk_select" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($produks as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama_produk']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="bulk_tanggal" class="form-control"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan <span class="text-muted"
                                style="font-size:0.78rem;font-weight:400">(opsional)</span></label>
                        <textarea name="keterangan" id="bulk_keterangan" class="form-control" rows="2"
                            placeholder="Catatan untuk semua bahan..."></textarea>
                    </div>

                    <div id="ingredient_container" style="display:none">
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-list-ul me-2"></i>Bahan-Bahan yang Dibutuhkan</h6>
                        <div id="ingredient_list"></div>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btn_save_bulk"><i
                            class="bi bi-save me-1"></i>Simpan Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function fmtNum(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
    function parseNum(s) { return s.replace(/\./g, ''); }

    // Data produk dan ingredientsnya
    const produkData = <?= json_encode(array_map(function ($p) use ($produks) {
        $ingredients = (new \Models\ProdukModel())->getIngredients($p['id']);
        return [
            'id' => $p['id'],
            'nama' => $p['nama_produk'],
            'ingredients' => array_map(function ($ing) {
                return [
                    'id' => $ing['bahan_baku_id'],
                    'nama' => $ing['nama_bahan'],
                    'kode' => $ing['kode_bahan'],
                    'satuan' => $ing['satuan'],
                    'jumlah' => $ing['jumlah_dibutuhkan']
                ];
            }, $ingredients)
        ];
    }, $produks)) ?>;

    function openTambahByProduk() {
        const modal = new bootstrap.Modal(document.getElementById('modalTambahByProduk'));
        modal.show();

        // Reset form
        document.getElementById('produk_select').value = '';
        document.getElementById('ingredient_container').style.display = 'none';
        document.getElementById('ingredient_list').innerHTML = '';
    }

    // Listen to produk selection
    document.addEventListener('DOMContentLoaded', function () {
        const produkSelect = document.getElementById('produk_select');
        const ingredientContainer = document.getElementById('ingredient_container');
        const ingredientList = document.getElementById('ingredient_list');

        if (produkSelect) {
            produkSelect.addEventListener('change', function () {
                const produkId = parseInt(this.value);

                if (!produkId) {
                    ingredientContainer.style.display = 'none';
                    ingredientList.innerHTML = '';
                    return;
                }

                // Find produk data
                const produk = produkData.find(p => p.id === produkId);
                if (!produk || !produk.ingredients || produk.ingredients.length === 0) {
                    ingredientContainer.style.display = 'none';
                    ingredientList.innerHTML = '<p class="text-muted">Produk ini belum memiliki bahan baku.</p>';
                    return;
                }

                // Show ingredients
                ingredientContainer.style.display = 'block';
                let html = '';

                produk.ingredients.forEach((ing, index) => {
                    html += `
                        <div class="card mb-3" style="border:1px solid #e5e7eb">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary me-2">${ing.kode}</span>
                                        <strong>${ing.nama}</strong>
                                    </div>
                                    <small class="text-muted">Per 1 unit produk: ${fmtNum(ing.jumlah)} ${ing.satuan}</small>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Jumlah</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" 
                                                   class="form-control jumlah-display" 
                                                   data-index="${index}"
                                                   placeholder="Masukkan jumlah..." 
                                                   required>
                                            <span class="input-group-text" style="min-width:70px;background:#f3f4f6;color:#6b7280">
                                                <i class="bi bi-box me-1"></i>${ing.satuan}
                                            </span>
                                        </div>
                                        <input type="hidden" name="items[${index}][bahan_baku_id]" value="${ing.id}">
                                        <input type="hidden" name="items[${index}][jumlah]" class="jumlah-hidden" data-index="${index}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Keterangan Khusus <span class="text-muted" style="font-size:0.7rem">(opsional)</span></label>
                                        <input type="text" name="items[${index}][keterangan]" class="form-control form-control-sm" placeholder="Catatan khusus...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                ingredientList.innerHTML = html;

                // Init number formatting for each jumlah input
                document.querySelectorAll('.jumlah-display').forEach(input => {
                    const index = input.dataset.index;
                    const hiddenInput = document.querySelector(`.jumlah-hidden[data-index="${index}"]`);

                    input.addEventListener('input', function (e) {
                        const raw = parseNum(e.target.value.replace(/[^\d.]/g, ''));
                        hiddenInput.value = raw;
                        if (raw) {
                            const pos = e.target.selectionStart, ol = e.target.value.length;
                            e.target.value = fmtNum(raw);
                            const np = pos + (e.target.value.length - ol);
                            e.target.setSelectionRange(np, np);
                        } else {
                            e.target.value = '';
                        }
                    });
                });
            });
        }

        // Form validation before submit
        document.getElementById('formBulk')?.addEventListener('submit', function (e) {
            const displays = document.querySelectorAll('.jumlah-display');
            let valid = true;

            displays.forEach(input => {
                const index = input.dataset.index;
                const hiddenInput = document.querySelector(`.jumlah-hidden[data-index="${index}"]`);

                if (!hiddenInput.value || parseInt(hiddenInput.value) < 1) {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Mohon isi semua jumlah dengan nilai minimal 1');
            }
        });
    });

    function initJumlah(dId, hId) {
        const d = document.getElementById(dId), h = document.getElementById(hId);
        if (!d || !h) return;
        if (d.value) { const r = parseNum(d.value); if (r) { d.value = fmtNum(r); h.value = r; } }
        d.addEventListener('input', function (e) {
            const raw = parseNum(e.target.value.replace(/[^\d.]/g, ''));
            h.value = raw;
            if (raw) {
                const pos = e.target.selectionStart, ol = e.target.value.length;
                e.target.value = fmtNum(raw);
                const np = pos + (e.target.value.length - ol);
                e.target.setSelectionRange(np, np);
            } else e.target.value = '';
        });
        d.closest('form').addEventListener('submit', function (e) {
            if (!h.value || parseInt(h.value) < 1) { e.preventDefault(); d.classList.add('is-invalid'); }
        });
    }

    function initSatuan(selId, spanId) {
        const sel = document.getElementById(selId), span = document.getElementById(spanId);
        if (!sel || !span) return;
        function upd() {
            const opt = sel.options[sel.selectedIndex];
            span.textContent = opt.value ? (opt.getAttribute('data-satuan') || '-') : '-';
        }
        upd(); sel.addEventListener('change', upd);
    }

    function openTambah() {
        const m = new bootstrap.Modal(document.getElementById('modalTambah'));
        m.show();
        document.getElementById('modalTambah').addEventListener('shown.bs.modal', function () {
            initJumlah('t_jumlah_display', 't_jumlah_hidden');
            initSatuan('t_bahan_baku_id', 't_satuanText');
        }, { once: true });
    }

    function openEdit(id, row) {
        document.getElementById('editForm').action = '/stok-masuk/' + id + '/update';
        const sel = document.getElementById('e_bahan_baku_id');
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value == row.bahan_baku_id) { sel.selectedIndex = i; break; }
        }
        const jd = document.getElementById('e_jumlah_display'), jh = document.getElementById('e_jumlah_hidden');
        jd.value = fmtNum(row.jumlah); jh.value = row.jumlah;
        document.getElementById('e_tanggal').value = row.tanggal;
        document.getElementById('e_keterangan').value = row.keterangan ?? '';
        const m = new bootstrap.Modal(document.getElementById('modalEdit'));
        m.show();
        document.getElementById('modalEdit').addEventListener('shown.bs.modal', function () {
            initJumlah('e_jumlah_display', 'e_jumlah_hidden');
            initSatuan('e_bahan_baku_id', 'e_satuanText');
        }, { once: true });
    }

    function setFilterType(type) {
        document.getElementById('formMonth').style.display = type === 'month' ? '' : 'none';
        document.getElementById('formRange').style.display = type === 'range' ? '' : 'none';
    }

    // Validate filter forms before submit
    document.getElementById('formMonth').addEventListener('submit', function (e) {
        const month = this.querySelector('[name="month"]').value;
        if (!month) {
            e.preventDefault();
            this.querySelector('[name="month"]').focus();
            this.querySelector('[name="month"]').classList.add('is-invalid');
        }
    });
    document.getElementById('formRange').addEventListener('submit', function (e) {
        const from = this.querySelector('[name="from"]').value;
        const to = this.querySelector('[name="to"]').value;
        if (!from || !to) {
            e.preventDefault();
            if (!from) this.querySelector('[name="from"]').classList.add('is-invalid');
            if (!to) this.querySelector('[name="to"]').classList.add('is-invalid');
        } else if (from > to) {
            e.preventDefault();
            this.querySelector('[name="to"]').classList.add('is-invalid');
            this.querySelector('[name="to"]').setCustomValidity('Tanggal akhir harus setelah tanggal awal.');
            this.querySelector('[name="to"]').reportValidity();
        }
    });
    // Clear invalid state on change
    document.querySelectorAll('#formMonth [name="month"], #formRange [name="from"], #formRange [name="to"]')
        .forEach(el => el.addEventListener('change', function () {
            this.classList.remove('is-invalid');
            this.setCustomValidity('');
        }));

    document.addEventListener('DOMContentLoaded', function () {
        const modalOpen = <?= json_encode($modalOpen) ?>;
        const editId = <?= json_encode($editId) ?>;
        const old = <?= json_encode($old) ?>;

        if (modalOpen === 'tambah') {
            openTambah();
        } else if (modalOpen === 'edit' && editId) {
            document.getElementById('editForm').action = '/stok-masuk/' + editId + '/update';
            const sel = document.getElementById('e_bahan_baku_id');
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value == old.bahan_baku_id) { sel.selectedIndex = i; break; }
            }
            const jd = document.getElementById('e_jumlah_display'), jh = document.getElementById('e_jumlah_hidden');
            if (old.jumlah) { jd.value = fmtNum(old.jumlah); jh.value = old.jumlah; }
            if (old.tanggal) document.getElementById('e_tanggal').value = old.tanggal;
            if (old.keterangan !== undefined) document.getElementById('e_keterangan').value = old.keterangan;
            const m = new bootstrap.Modal(document.getElementById('modalEdit'));
            m.show();
            document.getElementById('modalEdit').addEventListener('shown.bs.modal', function () {
                initJumlah('e_jumlah_display', 'e_jumlah_hidden');
                initSatuan('e_bahan_baku_id', 'e_satuanText');
            }, { once: true });
        }

        $('#tblStokMasuk').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [],
            language: {
                search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data', emptyTable: 'Belum ada data',
                zeroRecords: 'Data tidak ditemukan',
                paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
            },
            dom: '<"dt-top d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf><"table-responsive"t><"dt-bot d-flex justify-content-between align-items-center px-3 pt-2 pb-3"ip>',
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Stok Masuk', $content); ?>