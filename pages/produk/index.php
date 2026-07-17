<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../shared/components.php';

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-600 mb-0">Manajemen Produk</h5>
    <a href="/produk/create" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0" id="produkTable" style="width:100%">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:35%">Nama Produk</th>
                    <th style="width:30%">Bahan-Bahan</th>
                    <th style="width:15%">Jumlah Bahan</th>
                    <th style="width:15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produks)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size:2rem"></i>
                            <div class="mt-2">Belum ada produk</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produks as $i => $produk): ?>
                        <tr>
                            <td>
                                <?= $i + 1 ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($produk['nama_produk']) ?></strong>
                            </td>
                            <td>
                                <?php if (empty($produk['ingredient_names'])): ?>
                                    <span class="text-muted" style="font-size:0.9rem">-</span>
                                <?php else: ?>
                                    <span style="font-size:0.9rem">
                                        <?= htmlspecialchars(implode(', ', $produk['ingredient_names'])) ?>
                                        <?php if ($produk['ingredient_remaining'] > 0): ?>
                                            <span class="badge bg-secondary ms-1" style="font-size:0.75rem">
                                                +<?= $produk['ingredient_remaining'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $produk['ingredient_count'] ?> bahan
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/produk/<?= $produk['id'] ?>" class="btn btn-outline-primary"
                                        title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/produk/<?= $produk['id'] ?>/edit" class="btn btn-outline-warning"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(this)"
                                        data-form="deleteForm<?= $produk['id'] ?>"
                                        data-message="Produk ini akan dihapus permanen" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="deleteForm<?= $produk['id'] ?>" action="/produk/<?= $produk['id'] ?>/delete"
                                    method="POST" style="display:none"></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#produkTable').DataTable({
            pageLength: 15,
            lengthMenu: [5, 10, 15, 25, 50],
            order: [[0, 'asc']],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ produk',
                infoEmpty: 'Tidak ada produk',
                emptyTable: 'Belum ada produk',
                zeroRecords: 'Produk tidak ditemukan',
                paginate: {
                    previous: '&lsaquo;',
                    next: '&rsaquo;',
                    first: '&laquo;',
                    last: '&raquo;'
                }
            },
            dom: '<"dt-top d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf><"table-responsive"t><"dt-bot d-flex justify-content-between align-items-center px-3 pt-2 pb-3"ip>',
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    });
</script>

<?php
$content = ob_get_clean();
renderLayout('Produk', $content);
