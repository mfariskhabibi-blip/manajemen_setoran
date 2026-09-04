<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Data Pengguna</h1>
            <p class="text-muted small mb-0">Kelola akun pengguna, peran, status, dan akses dalam sistem.</p>
        </div>
        <div class="w-100 w-sm-auto text-end">
            <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary rounded-pill px-4 py-2 w-100 w-sm-auto text-center shadow-sm">
                <i class="fas fa-user-plus me-2"></i>Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Stats summary -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Pengguna</div>
                        <div class="h4 mb-0 fw-bold text-dark text-truncate"><?= $stats['total'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Pengguna Aktif</div>
                        <div class="h4 mb-0 fw-bold text-success text-truncate"><?= $stats['active'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning me-3 flex-shrink-0">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Admin</div>
                        <div class="h4 mb-0 fw-bold text-dark text-truncate"><?= $stats['admin'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-user-lock fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Ditangguhkan</div>
                        <div class="h4 mb-0 fw-bold text-danger text-truncate"><?= $stats['suspended'] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <form action="<?= base_url('admin/users') ?>" method="get" class="row g-2 align-items-end">
                <div class="col-md-4 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" value="<?= esc($filters['search']) ?>" placeholder="Cari nama, username, email...">
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label text-muted small fw-semibold mb-1">Peran (Role)</label>
                    <select name="role" class="form-select">
                        <option value="">Semua Peran</option>
                        <option value="admin" <?= $filters['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $filters['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label text-muted small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="suspended" <?= $filters['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-light border"><i class="fas fa-sync"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-3 p-md-4 pt-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block table-responsive border shadow-sm rounded-4 mb-4">
                <table class="table table-hover align-middle bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Pengguna</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 d-block text-secondary opacity-50"></i>
                                    Tidak ada data pengguna ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 160px;"><?= esc($u['nama']) ?></div>
                                                <small class="text-muted">ID: #<?= $u['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark font-monospace"><?= esc($u['username']) ?></span></td>
                                    <td class="small text-muted"><?= esc($u['email']) ?></td>
                                    <td>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $u['nomor_whatsapp']) ?>" target="_blank" class="text-decoration-none text-success small">
                                            <i class="fab fa-whatsapp me-1"></i><?= esc($u['nomor_whatsapp']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-1"><i class="fas fa-user-shield me-1"></i>Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark rounded-pill px-3 py-1"><i class="fas fa-user me-1"></i>User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u['status'] === 'active'): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-1">Active</span>
                                        <?php elseif ($u['status'] === 'suspended'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Suspended</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-1">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($u['id'] != $user['id']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning" title="Ubah Status (Toggle)" onclick="toggleUserStatus(<?= $u['id'] ?>)">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="deleteUser(<?= $u['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-block d-md-none mb-4">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                        <i class="fas fa-user-slash fa-3x mb-3 d-block text-secondary opacity-50"></i>
                        Tidak ada data pengguna ditemukan.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($users as $u): ?>
                            <div class="card border-0 rounded-4 shadow-sm bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 36px; height: 36px;">
                                                <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6 text-truncate" style="max-width: 150px;"><?= esc($u['nama']) ?></div>
                                                <small class="text-muted"><i class="fas fa-hashtag"></i> <?= $u['id'] ?> | <span class="badge bg-light text-dark font-monospace p-1"><?= esc($u['username']) ?></span></small>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if ($u['status'] === 'active'): ?>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check"></i></span>
                                            <?php elseif ($u['status'] === 'suspended'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-ban"></i></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill px-2 py-1"><i class="fas fa-minus"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-user-shield me-1"></i> Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark rounded-pill px-2 py-1"><i class="fas fa-user me-1"></i> User</span>
                                            <?php endif; ?>
                                            
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $u['nomor_whatsapp']) ?>" target="_blank" class="text-decoration-none text-success fw-bold p-1 bg-success bg-opacity-10 rounded-pill px-3">
                                                <i class="fab fa-whatsapp me-1"></i>WA
                                            </a>
                                        </div>
                                        
                                        <div class="d-flex justify-content-end gap-2 mt-2">
                                            <a href="<?= base_url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-outline-primary rounded-pill flex-fill px-0 py-2 shadow-sm text-center">
                                                <i class="fas fa-edit d-none d-sm-inline"></i> Edit
                                            </a>
                                            <?php if ($u['id'] != $user['id']): ?>
                                                <button type="button" class="btn btn-outline-warning rounded-pill flex-fill px-0 py-2 shadow-sm text-center" onclick="toggleUserStatus(<?= $u['id'] ?>)">
                                                    <i class="fas fa-power-off d-none d-sm-inline"></i> Status
                                                </button>
                                                <button type="button" class="btn btn-outline-danger rounded-pill flex-fill px-0 py-2 shadow-sm text-center" onclick="deleteUser(<?= $u['id'] ?>)">
                                                    <i class="fas fa-trash d-none d-sm-inline"></i> Hapus
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-3 d-flex justify-content-center justify-content-md-end">
                <?= $pager->links('default', 'default_full') ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function getCsrfToken() {
        let match = document.cookie.match(new RegExp('(^| )csrf_cookie_name=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : '<?= csrf_hash() ?>';
    }

    function toggleUserStatus(id) {
        Swal.fire({
            title: 'Ubah Status Pengguna?',
            text: "Status pengguna akan diaktifkan / ditangguhkan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah Status'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch(`<?= base_url('admin/users') ?>/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }

    function deleteUser(id) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((res) => {
            if (res.isConfirmed) {
                fetch(`<?= base_url('admin/users') ?>/${id}/delete`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Dihapus!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>
