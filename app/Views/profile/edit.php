<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Profil</h1>
                <a href="/profile" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <?= form_open('/profile/update', ['enctype' => 'multipart/form-data']) ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Foto Profil</label>
                                <div class="text-center mb-3">
                                    <?php if ($user['foto_profil']): ?>
                                        <img src="/uploads/profile/<?= esc($user['foto_profil']) ?>" 
                                             alt="Profile" 
                                             class="rounded-circle mb-2" 
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                                             style="width: 120px; height: 120px; font-size: 50px;">
                                            <?= substr(esc($user['nama']), 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control" name="foto_profil" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama" 
                                           value="<?= esc($user['nama']) ?>" required>
                                    <?php if (isset($validation) && $validation->getError('nama')): ?>
                                        <div class="text-danger"><?= $validation->getError('nama') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?= esc($user['username']) ?>" readonly>
                                    <small class="text-muted">Username tidak dapat diubah</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= esc($user['email']) ?>" required>
                                    <?php if (isset($validation) && $validation->getError('email')): ?>
                                        <div class="text-danger"><?= $validation->getError('email') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nomor_whatsapp" 
                                           value="<?= esc($user['nomor_whatsapp']) ?>" required>
                                    <?php if (isset($validation) && $validation->getError('nomor_whatsapp')): ?>
                                        <div class="text-danger"><?= $validation->getError('nomor_whatsapp') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
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
