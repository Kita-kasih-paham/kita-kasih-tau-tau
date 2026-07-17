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
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Detail Produk</h6>
                <a href="/produk/<?= $produk['id'] ?>/edit" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td style="width:30%"><strong>Nama Produk</strong></td>
                        <td>
                            <?= htmlspecialchars($produk['nama_produk']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td>
                            <?= htmlspecialchars($produk['deskripsi'] ?? '-') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Bahan</strong></td>
                        <td>
                            <span class="badge bg-info"><?= count($ingredients) ?> bahan</span>
                        </td>
                    </tr>
                </table>

                <hr>

                <h6 class="mb-3">Komposisi Bahan (per 1 unit produk)</h6>

                <?php if (empty($ingredients)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Belum ada bahan ditambahkan
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:40%">Nama Bahan</th>
                                    <th style="width:25%" class="text-end">Jumlah</th>
                                    <th style="width:30%">Satuan</th>
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
                                        <td class="text-end"><strong>
                                                <?= number_format($ingredient['jumlah_dibutuhkan'], 2, ',', '.') ?>
                                            </strong></td>
                                        <td>
                                            <?= htmlspecialchars($ingredient['satuan']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Detail Produk - ' . $produk['nama_produk'], $content);
