<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Ubah Password</h1>
                <a href="/profile" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Formulir Ubah Password</h5>
                </div>
                <div class="card-body">
                    <?= form_open('/profile/update-password') ?>
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="current_password" required>
                            <?php if (isset($validation) && $validation->getError('current_password')): ?>
                                <div class="text-danger"><?= $validation->getError('current_password') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="new_password" required minlength="8">
                            <small class="text-muted">Minimal 8 karakter</small>
                            <?php if (isset($validation) && $validation->getError('new_password')): ?>
                                <div class="text-danger"><?= $validation->getError('new_password') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" required minlength="8">
                            <?php if (isset($validation) && $validation->getError('confirm_password')): ?>
                                <div class="text-danger"><?= $validation->getError('confirm_password') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Tips Password:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan minimal 8 karakter</li>
                                <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                                <li>Jangan gunakan password yang sama dengan akun lain</li>
                            </ul>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Ubah Password
                                </button>
                                <a href="/profile" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
