<?php
require_once __DIR__ . '/../../shared/components.php';

$errors = \Core\Flash::getErrors();
$old = \Core\Flash::getOld();
$modalOpen = \Core\Flash::get('modal_open'); // 'tambah' | 'edit' | null
$editId = \Core\Flash::get('edit_id');    // id saat edit gagal validasi

// Satuan list untuk dropdown
$satuanList = ['pcs', 'kg', 'gram', 'liter', 'ml', 'meter', 'cm', 'box', 'lusin', 'karton', 'roll', 'lembar', 'unit', 'set', 'botol', 'kaleng', 'pak', 'buah'];

ob_start();
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <span class="text-muted" style="font-size:0.85rem">Master data bahan baku</span>
    <button type="button" class="btn btn-primary btn-sm" onclick="openTambah()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Bahan Baku
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="tblBahanBaku" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Bahan Baku</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $i => $row): ?>
                    <tr style="<?= $row['is_active'] ? '' : 'opacity:0.6;background:#f8f9fa' ?>">
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_bahan']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_bahan']) ?></td>
                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                        <td>
                            <?php if ($row['is_active']): ?>
                                <span class="badge" style="background:#10b981;color:white">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background:#6b7280;color:white">
                                    <i class="bi bi-x-circle me-1"></i>Tidak Aktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted">
                            <?= $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '<span class="text-muted" style="font-size:0.78rem">—</span>' ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm"
                                    style="color:#0f6cbd;background:rgba(15,108,189,0.08);border:none"
                                    onclick="openEdit(<?= $row['id'] ?>, <?= htmlspecialchars(json_encode($row), ENT_QUOTES) ?>)"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form id="toggle-<?= $row['id'] ?>" action="/bahan-baku/<?= $row['id'] ?>/toggle-active"
                                    method="POST" style="display:inline">
                                    <button type="submit" class="btn btn-sm"
                                        style="color:<?= $row['is_active'] ? '#f59e0b' : '#10b981' ?>;background:rgba(<?= $row['is_active'] ? '245,158,11' : '16,185,129' ?>,0.08);border:none"
                                        title="<?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="bi bi-<?= $row['is_active'] ? 'toggle-on' : 'toggle-off' ?>"></i>
                                    </button>
                                </form>
                                <form id="del-bahan-baku-<?= $row['id'] ?>" action="/bahan-baku/<?= $row['id'] ?>/delete"
                                    method="POST">
                                    <button type="button" class="btn btn-sm"
                                        style="color:#e02424;background:rgba(224,36,36,0.08);border:none"
                                        onclick="confirmDelete(this)" data-form="del-bahan-baku-<?= $row['id'] ?>"
                                        data-message="Bahan baku &quot;<?= htmlspecialchars($row['nama_bahan']) ?>&quot; akan dihapus permanen."
                                        title="Hapus">
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
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" id="modalTambahLabel">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Tambah Bahan Baku
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/bahan-baku" method="POST">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Kode Bahan Baku</label>
                        <input type="text" name="kode_bahan" id="t_kode_bahan"
                            class="form-control <?= ($modalOpen === 'tambah' && isset($errors['kode_bahan'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['kode_bahan'] ?? '') : '' ?>"
                            placeholder="Contoh: BAHAN001" style="text-transform:uppercase" autocomplete="off"
                            minlength="4" required>
                        <?php if ($modalOpen === 'tambah' && isset($errors['kode_bahan'])): ?>
                            <div class="invalid-feedback"><?= $errors['kode_bahan'][0] ?></div>
                        <?php else: ?>
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Minimal 4 karakter.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Bahan Baku</label>
                        <input type="text" name="nama_bahan"
                            class="form-control <?= ($modalOpen === 'tambah' && isset($errors['nama_bahan'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['nama_bahan'] ?? '') : '' ?>"
                            placeholder="Contoh: Biji Kopi, Gula Pasir, Susu dll" autocomplete="off" required>
                        <?php if ($modalOpen === 'tambah' && isset($errors['nama_bahan'])): ?>
                            <div class="invalid-feedback"><?= $errors['nama_bahan'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <div style="position:relative">
                            <input type="text" name="satuan" id="t_satuan"
                                class="form-control <?= ($modalOpen === 'tambah' && isset($errors['satuan'])) ? 'is-invalid' : '' ?>"
                                value="<?= ($modalOpen === 'tambah') ? htmlspecialchars($old['satuan'] ?? '') : '' ?>"
                                placeholder="Ketik atau pilih satuan..." autocomplete="off" required>
                            <div id="t_satuanDropdown" class="satuan-dd"></div>
                        </div>
                        <?php if ($modalOpen === 'tambah' && isset($errors['satuan'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['satuan'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            Keterangan
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
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" id="modalEditLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Bahan Baku
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="editForm">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label">Kode Bahan Baku</label>
                        <input type="text" name="kode_bahan" id="e_kode_bahan"
                            class="form-control <?= ($modalOpen === 'edit' && isset($errors['kode_bahan'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['kode_bahan'] ?? '') : '' ?>"
                            placeholder="Contoh: BAHAN001" style="text-transform:uppercase" autocomplete="off"
                            minlength="4" required>
                        <?php if ($modalOpen === 'edit' && isset($errors['kode_bahan'])): ?>
                            <div class="invalid-feedback"><?= $errors['kode_bahan'][0] ?></div>
                        <?php else: ?>
                            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Minimal 4 karakter.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Bahan Baku</label>
                        <input type="text" name="nama_bahan" id="e_nama_bahan"
                            class="form-control <?= ($modalOpen === 'edit' && isset($errors['nama_bahan'])) ? 'is-invalid' : '' ?>"
                            value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['nama_bahan'] ?? '') : '' ?>"
                            placeholder="Contoh: Biji Kopi, Gula Pasir, Susu, dll" autocomplete="off" required>
                        <?php if ($modalOpen === 'edit' && isset($errors['nama_bahan'])): ?>
                            <div class="invalid-feedback"><?= $errors['nama_bahan'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <div style="position:relative">
                            <input type="text" name="satuan" id="e_satuan"
                                class="form-control <?= ($modalOpen === 'edit' && isset($errors['satuan'])) ? 'is-invalid' : '' ?>"
                                value="<?= ($modalOpen === 'edit') ? htmlspecialchars($old['satuan'] ?? '') : '' ?>"
                                placeholder="Ketik atau pilih satuan..." autocomplete="off" required>
                            <div id="e_satuanDropdown" class="satuan-dd"></div>
                        </div>
                        <?php if ($modalOpen === 'edit' && isset($errors['satuan'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['satuan'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            Keterangan
                            <span class="text-muted" style="font-size:0.78rem;font-weight:400">(opsional)</span>
                        </label>
                        <textarea name="keterangan" id="e_keterangan" class="form-control" rows="3"
                            placeholder="Tambahkan catatan jika perlu..."><?= ($modalOpen === 'edit') ? htmlspecialchars($old['keterangan'] ?? '') : '' ?></textarea>
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

<style>
    .satuan-dd {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #dde3ea;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        z-index: 1060;
        max-height: 200px;
        overflow-y: auto;
    }

    .satuan-dd .satuan-item {
        padding: 0.5rem 0.85rem;
        cursor: pointer;
        font-size: 0.875rem;
        color: #1a2332;
    }

    .satuan-dd .satuan-item:hover {
        background: #f0f4f8;
    }
</style>

<script>
    const SATUAN_LIST = <?= json_encode($satuanList) ?>;

    // ── Satuan autocomplete (reusable) ──────────────────────────
    function initSatuan(inputId, dropdownId) {
        const input = document.getElementById(inputId);
        const dd = document.getElementById(dropdownId);
        if (!input || !dd) return;

        function render(filter) {
            const q = (filter || '').toLowerCase();
            const filtered = SATUAN_LIST.filter(u => u.toLowerCase().includes(q));
            if (!filtered.length) { dd.style.display = 'none'; return; }
            dd.innerHTML = filtered.map(u =>
                `<div class="satuan-item" data-val="${u}">${u}</div>`
            ).join('');
            dd.querySelectorAll('.satuan-item').forEach(item => {
                item.addEventListener('mousedown', e => {
                    e.preventDefault();
                    input.value = item.dataset.val;
                    dd.style.display = 'none';
                });
            });
            dd.style.display = 'block';
        }

        input.addEventListener('focus', () => render(input.value));
        input.addEventListener('input', () => render(input.value));
        input.addEventListener('blur', () => setTimeout(() => dd.style.display = 'none', 150));
    }

    // ── Open Tambah modal ───────────────────────────────────────
    function openTambah() {
        const modal = new bootstrap.Modal(document.getElementById('modalTambah'));
        modal.show();
        document.getElementById('modalTambah').addEventListener('shown.bs.modal', () => {
            initSatuan('t_satuan', 't_satuanDropdown');
        }, { once: true });
    }

    // ── Open Edit modal ─────────────────────────────────────────
    function openEdit(id, row) {
        document.getElementById('editForm').action = '/bahan-baku/' + id + '/update';
        document.getElementById('e_kode_bahan').value = row.kode_bahan;
        document.getElementById('e_nama_bahan').value = row.nama_bahan;
        document.getElementById('e_satuan').value = row.satuan;
        document.getElementById('e_keterangan').value = row.keterangan ?? '';
        const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
        document.getElementById('modalEdit').addEventListener('shown.bs.modal', () => {
            initSatuan('e_satuan', 'e_satuanDropdown');
        }, { once: true });
    }

    // ── Auto-open modal on validation error (flash state) ───────
    document.addEventListener('DOMContentLoaded', () => {
        const modalOpen = <?= json_encode($modalOpen) ?>;
        const editId = <?= json_encode($editId) ?>;

        if (modalOpen === 'tambah') {
            openTambah();
        } else if (modalOpen === 'edit' && editId) {
            // Find the row data from the table to pre-fill, then overlay old POST values
            const old = <?= json_encode($old) ?>;
            // Manually trigger via old POST data (no table lookup needed)
            document.getElementById('editForm').action = '/bahan-baku/' + editId + '/update';
            document.getElementById('e_kode_bahan').value = old.kode_bahan ?? '';
            document.getElementById('e_nama_bahan').value = old.nama_bahan ?? '';
            document.getElementById('e_satuan').value = old.satuan ?? '';
            document.getElementById('e_keterangan').value = old.keterangan ?? '';
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
            document.getElementById('modalEdit').addEventListener('shown.bs.modal', () => {
                initSatuan('e_satuan', 'e_satuanDropdown');
            }, { once: true });
        }

        // DataTable
        $('#tblBahanBaku').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                emptyTable: 'Belum ada data',
                zeroRecords: 'Data tidak ditemukan',
                paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
            },
            dom: '<"dt-top d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf><"table-responsive"t><"dt-bot d-flex justify-content-between align-items-center px-3 pt-2 pb-3"ip>',
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Bahan Baku', $content); ?>