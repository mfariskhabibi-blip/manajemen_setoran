<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-cog me-2 text-primary"></i>Pengaturan Sistem</h1>
            <p class="text-muted small mb-0">Kelola konfigurasikan identitas aplikasi, informasi kontak admin, dan preferensi akun Anda.</p>
        </div>
    </div>

    <div class="row g-3">
        <!-- Admin Profile Settings -->
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-user-cog me-2"></i>Pengaturan Akun Admin</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 ps-3">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Admin <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= old('nama', $user['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Admin <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor WhatsApp Admin <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_whatsapp" class="form-control" value="<?= old('nomor_whatsapp', $user['nomor_whatsapp']) ?>" required>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 w-100 w-sm-auto"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Branding & Config Information -->
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-success fw-bold"><i class="fas fa-sliders-h me-2"></i>Informasi Sistem & Parameter</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="mb-4">
                        <label class="text-muted small fw-semibold">Nama Aplikasi</label>
                        <div class="fw-bold fs-5 text-dark"><?= esc($app_name) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small fw-semibold">Organisasi / Lembaga</label>
                        <div class="fw-semibold text-dark"><?= esc($org_name) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small fw-semibold">Versi Sistem</label>
                        <div><span class="badge bg-primary rounded-pill px-3 py-2">v2.5 Professional Edition</span></div>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted small fw-semibold">Audit Logging</label>
                        <div><span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i>Aktif (Otomatis Mencatat Aktivitas)</span></div>
                    </div>
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Data setoran dicatat manual oleh Admin melalui konfirmasi WhatsApp untuk keamanan & keandalan pencatatan.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
