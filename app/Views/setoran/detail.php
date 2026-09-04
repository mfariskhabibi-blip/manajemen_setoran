<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Detail Setoran</h1>
            <p class="text-muted">Informasi lengkap setoran iuran.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= $isAdminRoute ? base_url('admin/setoran') : base_url('setoran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <?php if ($isAdminRoute) : ?>
                <a href="<?= base_url('admin/setoran/' . $setoran['id'] . '/edit') ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> Edit
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Setoran</h6>
                    <span class="badge bg-<?= $setoran['status_setoran'] == 'diverifikasi' ? 'success' : ($setoran['status_setoran'] == 'tercatat' ? 'warning text-dark' : ($setoran['status_setoran'] == 'dikoreksi' ? 'info' : 'danger')) ?>">
                        <?= ucfirst($setoran['status_setoran']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Informasi Program</h6>
                                <div class="p-3 bg-light rounded">
                                    <div class="fw-bold"><?= esc($program['nama_program']) ?></div>
                                    <div class="text-muted">Kode: <?= esc($program['kode_program']) ?></div>
                                    <div class="small mt-1"><?= esc($program['tujuan']) ?></div>
                                    <a href="<?= base_url('program/' . $program['id']) ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-external-link-alt me-1"></i> Lihat Program
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Informasi Pengguna</h6>
                                <div class="p-3 bg-light rounded">
                                    <div class="fw-bold"><?= esc($user['nama']) ?></div>
                                    <div class="text-muted">Email: <?= esc($user['email']) ?></div>
                                    <div class="small mt-1">Status: 
                                        <span class="badge bg-<?= $user['status'] == 'aktif' ? 'success' : 'danger' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Periode</label>
                                <div class="form-control bg-light">
                                    <?= esc($periode['nama_periode']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Setoran</label>
                                <div class="form-control bg-light">
                                    <?= date('d F Y', strtotime($setoran['tanggal_setoran'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nominal Setoran</label>
                                <div class="form-control bg-light fw-bold text-success">
                                    Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dicatat Oleh</label>
                                <div class="form-control bg-light">
                                    <?= $admin ? esc($admin['nama']) : 'Sistem' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan</label>
                        <div class="form-control bg-light" style="min-height: 100px;">
                            <?= $setoran['keterangan'] ? nl2br(esc($setoran['keterangan'])) : '<span class="text-muted">Tidak ada keterangan</span>' ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Dibuat</label>
                                <div class="form-control bg-light">
                                    <?= date('d F Y H:i', strtotime($setoran['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Terakhir Diperbarui</label>
                                <div class="form-control bg-light">
                                    <?= $setoran['updated_at'] ? date('d F Y H:i', strtotime($setoran['updated_at'])) : 'Belum diperbarui' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <?php if ($isAdminRoute) : ?>
                        <?php if ($setoran['status_setoran'] == 'tercatat') : ?>
                            <button type="button" class="btn btn-success w-100 mb-2" onclick="verifySetoran(<?= $setoran['id'] ?>)">
                                <i class="fas fa-check me-1"></i> Verifikasi Setoran
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('admin/setoran/' . $setoran['id'] . '/edit') ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit me-1"></i> Edit Setoran
                        </a>
                        
                        <button type="button" class="btn btn-danger w-100 mb-2" onclick="deleteSetoran(<?= $setoran['id'] ?>)">
                            <i class="fas fa-trash me-1"></i> Batalkan/Hapus
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('riwayat/print/' . $setoran['id']) ?>" class="btn btn-outline-info w-100 mb-2" target="_blank">
                        <i class="fas fa-print me-1"></i> Cetak Kwitansi
                    </a>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-secondary">Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 text-muted ps-3">
                        <li class="mb-2">Setoran ini tercatat dalam <strong><?= esc($program['nama_program']) ?></strong>.</li>
                        <li class="mb-2">Periode: <strong><?= esc($periode['nama_periode']) ?></strong>.</li>
                        <li class="mb-2">Status <strong><?= ucfirst($setoran['status_setoran']) ?></strong> menunjukkan:
                            <?php if ($setoran['status_setoran'] == 'tercatat') : ?>
                                Setoran tercatat menunggu verifikasi.
                            <?php elseif ($setoran['status_setoran'] == 'diverifikasi') : ?>
                                Setoran telah diverifikasi dan dana sudah masuk.
                            <?php elseif ($setoran['status_setoran'] == 'dikoreksi') : ?>
                                Setoran memerlukan koreksi data.
                            <?php elseif ($setoran['status_setoran'] == 'dibatalkan') : ?>
                                Setoran telah dibatalkan.
                            <?php endif; ?>
                        </li>
                        <li>Untuk informasi lebih lanjut, hubungi admin.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdminRoute) : ?>
<!-- SweetAlert2 for Delete & Verify Confirmation -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Get latest CSRF token from cookie (handles regeneration)
    function getCsrfToken() {
        let match = document.cookie.match(new RegExp('(^| )csrf_cookie_name=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : '<?= csrf_hash() ?>';
    }

    function verifySetoran(id) {
        Swal.fire({
            title: 'Verifikasi Setoran?',
            text: "Pastikan dana sudah diterima sesuai nominal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Verifikasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('admin/setoran') ?>/${id}/verify`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: `status=verify`
                })
                .then(async response => {
                    if(!response.ok) throw new Error("Aksi tidak diizinkan atau sesi habis");
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Berhasil!', 'Setoran telah diverifikasi.', 'success')
                    .then(() => window.location.reload());
                })
                .catch(error => {
                    Swal.fire('Gagal!', error.message || 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }

    function deleteSetoran(id) {
        Swal.fire({
            title: 'Batalkan Setoran?',
            text: "Data setoran ini akan dibatalkan/dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`<?= base_url('admin/setoran') ?>/${id}/delete`, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                })
                .then(async response => {
                    if(!response.ok) throw new Error("Aksi tidak diizinkan atau sesi habis");
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Dihapus!', 'Data setoran telah dihapus.', 'success')
                    .then(() => window.location.reload());
                })
                .catch(error => {
                    Swal.fire('Gagal!', error.message || 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }
</script>
<?php endif; ?>
<?= $this->endSection() ?>