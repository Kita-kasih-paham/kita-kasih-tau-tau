<?php
require_once __DIR__ . '/../../shared/components.php';
ob_start();
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-box-seam text-primary"></i>
                </div>
                <div>
                    <div class="text-muted"
                        style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Total
                        Barang</div>
                    <div class="fs-3 fw-bold"><?= $totalBarang ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card h-100" style="border-left:4px solid #0e9f6e">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(14,159,110,0.1)">
                    <i class="bi bi-box-arrow-in-down" style="color:#0e9f6e"></i>
                </div>
                <div>
                    <div class="text-muted"
                        style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Transaksi
                        Masuk</div>
                    <div class="fs-3 fw-bold"><?= $totalMasuk ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card h-100" style="border-left:4px solid #e02424">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(224,36,36,0.1)">
                    <i class="bi bi-box-arrow-up" style="color:#e02424"></i>
                </div>
                <div>
                    <div class="text-muted"
                        style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Transaksi
                        Keluar</div>
                    <div class="fs-3 fw-bold"><?= $totalKeluar ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Warning -->
<?php if (!empty($lowStockItems)): ?>
    <div class="card mb-4" style="border:none;box-shadow:0 4px 12px rgba(245,158,11,0.15);overflow:hidden">
        <!-- Header with gradient -->
        <div class="card-header"
            style="background:linear-gradient(135deg, #f59e0b 0%, #f97316 100%);border:none;padding:1.25rem 1.5rem">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div
                        style="width:48px;height:48px;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border-radius:12px;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size:1.5rem;color:#fff"></i>
                    </div>
                    <div>
                        <h5 class="mb-0" style="color:#fff;font-weight:700;font-size:1.1rem">
                            Peringatan Stok Rendah
                        </h5>
                        <p class="mb-0" style="color:rgba(255,255,255,0.9);font-size:0.875rem;margin-top:0.25rem">
                            Perlu perhatian segera untuk barang di bawah ini
                        </p>
                    </div>
                </div>
                <div
                    style="background:rgba(255,255,255,0.25);backdrop-filter:blur(10px);padding:0.5rem 1rem;border-radius:20px">
                    <span style="color:#fff;font-weight:700;font-size:1.25rem"><?= count($lowStockItems) ?></span>
                    <span style="color:rgba(255,255,255,0.9);font-size:0.875rem;margin-left:0.25rem">items</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="card-body" style="padding:0;background:#fffbf5">
            <div style="padding:1.25rem 1.5rem;background:#fff;border-bottom:1px solid #f3f4f6">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle" style="color:#f59e0b"></i>
                    <span style="color:#78350f;font-size:0.9rem">
                        Barang dengan stok <strong>kurang dari 5</strong> unit. Segera lakukan restok untuk menghindari
                        kekosongan stok.
                    </span>
                </div>
            </div>

            <div class="table-responsive" style="max-height:350px;overflow-y:auto">
                <table class="table mb-0" style="font-size:0.9rem">
                    <thead style="position:sticky;top:0;background:#fff;z-index:10;box-shadow:0 2px 4px rgba(0,0,0,0.05)">
                        <tr>
                            <th
                                style="color:#6b7280;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;padding:1rem 1.5rem;border-bottom:2px solid #f3f4f6">
                                Kode Barang</th>
                            <th
                                style="color:#6b7280;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;padding:1rem 1.5rem;border-bottom:2px solid #f3f4f6">
                                Nama Barang</th>
                            <th
                                style="color:#6b7280;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;padding:1rem 1.5rem;border-bottom:2px solid #f3f4f6">
                                Satuan</th>
                            <th
                                style="color:#6b7280;font-weight:600;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;padding:1rem 1.5rem;text-align:center;border-bottom:2px solid #f3f4f6">
                                Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockItems as $item): ?>
                            <tr style="transition:background 0.2s ease" onmouseover="this.style.background='#fef3c7'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding:1rem 1.5rem;vertical-align:middle;border-bottom:1px solid #f3f4f6">
                                    <span
                                        style="background:linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);color:#fff;padding:0.35rem 0.75rem;border-radius:6px;font-weight:600;font-size:0.85rem;display:inline-block">
                                        <?= htmlspecialchars($item['kode_barang']) ?>
                                    </span>
                                </td>
                                <td
                                    style="padding:1rem 1.5rem;color:#1f2937;font-weight:500;vertical-align:middle;border-bottom:1px solid #f3f4f6">
                                    <?= htmlspecialchars($item['nama_barang']) ?>
                                </td>
                                <td
                                    style="padding:1rem 1.5rem;color:#6b7280;vertical-align:middle;border-bottom:1px solid #f3f4f6">
                                    <span
                                        style="background:#f3f4f6;padding:0.25rem 0.65rem;border-radius:6px;font-size:0.85rem">
                                        <?= htmlspecialchars($item['satuan']) ?>
                                    </span>
                                </td>
                                <td
                                    style="padding:1rem 1.5rem;text-align:center;vertical-align:middle;border-bottom:1px solid #f3f4f6">
                                    <?php
                                    $stok = (int) $item['stok_tersedia'];
                                    if ($stok === 0) {
                                        $badgeStyle = "background:linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);color:#fff;box-shadow:0 2px 8px rgba(220,38,38,0.3)";
                                        $icon = '<i class="bi bi-x-circle-fill me-1"></i>';
                                    } elseif ($stok <= 2) {
                                        $badgeStyle = "background:linear-gradient(135deg, #ea580c 0%, #c2410c 100%);color:#fff;box-shadow:0 2px 8px rgba(234,88,12,0.3)";
                                        $icon = '<i class="bi bi-exclamation-circle-fill me-1"></i>';
                                    } else {
                                        $badgeStyle = "background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);color:#fff;box-shadow:0 2px 8px rgba(245,158,11,0.3)";
                                        $icon = '<i class="bi bi-exclamation-triangle-fill me-1"></i>';
                                    }
                                    ?>
                                    <span
                                        style="<?= $badgeStyle ?>;padding:0.45rem 1rem;border-radius:8px;font-weight:700;font-size:0.95rem;display:inline-flex;align-items:center">
                                        <?= $icon ?>
                                        <?= fmt($stok) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer with action suggestion -->
            <div style="padding:1rem 1.5rem;background:#fff;border-top:1px solid #f3f4f6">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-lightbulb" style="color:#f59e0b;font-size:1.1rem"></i>
                        <span style="color:#6b7280;font-size:0.875rem">
                            <strong style="color:#1f2937">Saran:</strong> Buat transaksi stok masuk untuk item dengan
                            prioritas tinggi
                        </span>
                    </div>
                    <a href="/stok-masuk" class="btn btn-sm"
                        style="background:linear-gradient(135deg, #f59e0b 0%, #f97316 100%);color:#fff;border:none;padding:0.5rem 1.25rem;border-radius:8px;font-weight:600;box-shadow:0 2px 8px rgba(245,158,11,0.3);transition:all 0.2s ease"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(245,158,11,0.4)'"
                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(245,158,11,0.3)'">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Stok Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Stok Masuk Terbaru -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-in-down text-success"></i>
        <span>Stok Masuk Terbaru</span>
    </div>
    <div class="card-body p-0">
        <table id="tblMasuk" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMasuk as $row): ?>
                    <tr>
                        <td><?= fmtDate($row['tanggal']) ?></td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_barang']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td style="color:#0e9f6e;font-weight:600"><?= fmt($row['jumlah']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($row['keterangan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stok Keluar Terbaru -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-up text-danger"></i>
        <span>Stok Keluar Terbaru</span>
    </div>
    <div class="card-body p-0">
        <table id="tblKeluar" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentKeluar as $row): ?>
                    <tr>
                        <td><?= fmtDate($row['tanggal']) ?></td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_barang']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td style="color:#e02424;font-weight:600"><?= fmt($row['jumlah']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($row['keterangan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Last Added Barang -->
<div class="card mb-2">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-box-seam text-primary"></i>
        <span>Barang Terakhir Ditambahkan</span>
    </div>
    <div class="card-body p-0">
        <table id="tblBarang" class="table mb-0" style="width:100%">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                    <th>Ditambahkan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentBarang as $row): ?>
                    <tr>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_barang']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td class="text-muted" style="font-size:0.82rem">
                            <?= fmtDate(date('Y-m-d', strtotime($row['created_at']))) ?>
                            <?= date('H:i', strtotime($row['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables init -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dtConfig = {
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [],
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
        };
        $('#tblMasuk').DataTable(dtConfig);
        $('#tblKeluar').DataTable(dtConfig);
        $('#tblBarang').DataTable(dtConfig);
    });
</script>

<?php
$content = ob_get_clean();
renderLayout('Dashboard', $content);
