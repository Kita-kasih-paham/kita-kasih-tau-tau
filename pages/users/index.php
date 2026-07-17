<?php
require_once __DIR__ . '/../../shared/components.php';

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen User</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem">Kelola data user dan hak akses</p>
    </div>
    <a href="/users/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size:3rem;color:#e5e7eb"></i>
                <h5 class="mt-3 mb-2" style="color:#9ca3af">Belum Ada Data User</h5>
                <p class="text-muted mb-3">Silakan tambah user baru untuk memulai</p>
                <a href="/users/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tambah User Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="tblUsers" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:5%">No</th>
                            <th style="width:20%">Username</th>
                            <th style="width:25%">Nama Lengkap</th>
                            <th style="width:15%">Role</th>
                            <th style="width:20%">Terakhir Update</th>
                            <th style="width:15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $i => $user): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                                    <?php if ($user['id'] == $_SESSION['user']['id']): ?>
                                        <span class="badge bg-info ms-1" style="font-size:0.65rem">Anda</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['nama_lengkap'] ?? '-') ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-fill-check me-1"></i>Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-person-fill me-1"></i>Karyawan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.85rem;color:#64748b">
                                    <?= fmtDateTime($user['updated_at']) ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline-primary"
                                            title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                            <form method="POST" action="/users/<?= $user['id'] ?>/delete" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($user['username']) ?>?')">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!empty($users)): ?>
            $('#tblUsers').DataTable({
                pageLength: 15,
                order: [[1, 'asc']],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ baris',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    emptyTable: 'Tidak ada data user',
                    zeroRecords: 'Data tidak ditemukan',
                    paginate: {
                        previous: '&lsaquo;',
                        next: '&rsaquo;'
                    }
                }
            });
        <?php endif; ?>
    });
</script>

<?php $content = ob_get_clean();
renderLayout('Manajemen User', $content); ?>