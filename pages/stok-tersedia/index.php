<?php require_once __DIR__ . '/../../shared/components.php';
ob_start(); ?>

<div class="card">
    <div class="card-body p-0">
        <table id="tblStokTersedia" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Bahan</th>
                    <th>Satuan</th>
                    <th>Total Masuk</th>
                    <th>Total Keluar</th>
                    <th>Stok Tersedia</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $i => $row): ?>
                    <tr class="<?= $row['is_active'] ? '' : 'table-secondary' ?>"
                        style="<?= $row['is_active'] ? '' : 'opacity:0.6' ?>">
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_bahan']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_bahan']) ?></td>
                        <td><span
                                class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($row['satuan']) ?></span>
                        </td>
                        <td style="color:#0e9f6e;font-weight:600"><?= fmt($row['total_masuk']) ?></td>
                        <td style="color:#e02424;font-weight:600"><?= fmt($row['total_keluar']) ?></td>
                        <td>
                            <span class="badge px-2 py-1"
                                style="font-size:0.82rem;background:<?= $row['stok_tersedia'] > 0 ? 'rgba(14,159,110,0.12);color:#0e9f6e' : 'rgba(224,36,36,0.12);color:#e02424' ?>">
                                <?= fmt($row['stok_tersedia']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['is_active']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success"><i
                                        class="bi bi-check-circle me-1"></i>Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#tblStokTersedia').DataTable({
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
        });
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Stok Tersedia', $content); ?>