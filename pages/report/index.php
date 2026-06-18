<?php
require_once __DIR__ . '/../../shared/components.php';

$filterType = $_GET['filter'] ?? null;
$isFiltered = in_array($filterType, ['month', 'range']);

// Build export URL preserving current filter params
$exportParams = array_filter([
    'filter' => $filterType,
    'month' => $_GET['month'] ?? null,
    'from' => $_GET['from'] ?? null,
    'to' => $_GET['to'] ?? null,
]);

ob_start();
?>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">

        <!-- Filter type toggle buttons -->
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
                <a href="/report" class="btn btn-sm btn-outline-danger ms-auto">
                    <i class="bi bi-x-circle me-1"></i>Batalkan Filter
                </a>
            <?php endif; ?>
        </div>

        <!-- Month filter form -->
        <form method="GET" id="formMonth" <?= $filterType !== 'month' ? 'style="display:none"' : '' ?>>
            <input type="hidden" name="filter" value="month">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-4">
                    <label class="form-label">Pilih Bulan</label>
                    <input type="month" name="month" class="form-control"
                        value="<?= htmlspecialchars($_GET['month'] ?? date('Y-m')) ?>">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <?php if ($filterType === 'month'): ?>
                        <a href="/report/export?<?= htmlspecialchars(http_build_query($exportParams)) ?>"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Range filter form -->
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
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <?php if ($filterType === 'range'): ?>
                        <a href="/report/export?<?= htmlspecialchars(http_build_query($exportParams)) ?>"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <?php if (!$isFiltered): ?>
            <p class="text-muted mb-0 mt-1" style="font-size:0.82rem">
                <i class="bi bi-info-circle me-1"></i>Pilih filter di atas untuk melihat dan mengexport data laporan.
            </p>
        <?php endif; ?>

    </div>
</div>

<!-- Active filter info -->
<?php if ($isFiltered): ?>
    <p class="text-muted mb-4" style="font-size:0.82rem">
        <i class="bi bi-funnel-fill me-1 text-primary"></i>
        <?php if ($filterType === 'month'): ?>
            Menampilkan bulan: <strong><?= date('F Y', strtotime($from)) ?></strong>
        <?php else: ?>
            Rentang: <strong><?= fmtDate($from) ?></strong> s/d <strong><?= fmtDate($to) ?></strong>
        <?php endif; ?>
        &nbsp;·&nbsp;
        Masuk: <strong style="color:#0e9f6e"><?= count($masuk) ?></strong> transaksi,
        Keluar: <strong style="color:#e02424"><?= count($keluar) ?></strong> transaksi
    </p>

    <div class="row g-4">
        <!-- Stok Masuk -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2" style="border-left:4px solid #0e9f6e">
                    <i class="bi bi-box-arrow-in-down" style="color:#0e9f6e"></i>
                    <span>Stok Masuk</span>
                    <span class="badge ms-auto" style="background:rgba(14,159,110,0.12);color:#0e9f6e">
                        <?= count($masuk) ?> transaksi
                    </span>
                </div>
                <div class="card-body p-0">
                    <table id="tblReportMasuk" class="table table-sm mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($masuk as $row): ?>
                                <tr>
                                    <td style="white-space:nowrap"><?= fmtDate($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td style="color:#0e9f6e;font-weight:600"><?= fmt($row['jumlah']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php if (!empty($masuk)): ?>
                            <tfoot>
                                <tr style="background:#f8fafc;font-weight:600;font-size:0.82rem">
                                    <td colspan="2" class="text-end text-muted">Total</td>
                                    <td style="color:#0e9f6e"><?= fmt(array_sum(array_column($masuk, 'jumlah'))) ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stok Keluar -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2" style="border-left:4px solid #e02424">
                    <i class="bi bi-box-arrow-up" style="color:#e02424"></i>
                    <span>Stok Keluar</span>
                    <span class="badge ms-auto" style="background:rgba(224,36,36,0.12);color:#e02424">
                        <?= count($keluar) ?> transaksi
                    </span>
                </div>
                <div class="card-body p-0">
                    <table id="tblReportKeluar" class="table table-sm mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($keluar as $row): ?>
                                <tr>
                                    <td style="white-space:nowrap"><?= fmtDate($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td style="color:#e02424;font-weight:600"><?= fmt($row['jumlah']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php if (!empty($keluar)): ?>
                            <tfoot>
                                <tr style="background:#f8fafc;font-weight:600;font-size:0.82rem">
                                    <td colspan="2" class="text-end text-muted">Total</td>
                                    <td style="color:#e02424"><?= fmt(array_sum(array_column($keluar, 'jumlah'))) ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- No filter active state -->
    <div class="text-center py-5 text-muted">
        <i class="bi bi-funnel" style="font-size:3rem;opacity:0.3"></i>
        <p class="mt-3 mb-0" style="font-size:0.95rem">Pilih filter bulan atau rentang tanggal untuk melihat laporan.</p>
    </div>
<?php endif; ?>

<script>
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
        <?php if ($isFiltered): ?>
            const cfg = {
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    emptyTable: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: { previous: '&lsaquo;', next: '&rsaquo;' }
                },
                dom: '<"dt-top d-flex justify-content-between align-items-center px-3 pt-3 pb-2"lf><"table-responsive"t><"dt-bot d-flex justify-content-between align-items-center px-3 pt-2 pb-3"ip>',
                footerCallback: function () { return; }
            };
            $('#tblReportMasuk').DataTable(cfg);
            $('#tblReportKeluar').DataTable(cfg);
        <?php endif; ?>
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Report Stok', $content); ?>