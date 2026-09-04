<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Program Iuran</h1>
                <a href="/admin/program" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Program: <?= esc($program['nama_program']) ?></h5>
                </div>
                <div class="card-body">
                    <form action="/admin/program/<?= $program['id'] ?>/update" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_program" class="form-label">Kode Program <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $validation->hasError('kode_program') ? 'is-invalid' : '' ?>" 
                                           id="kode_program" name="kode_program" 
                                           value="<?= old('kode_program', $program['kode_program']) ?>" required>
                                    <?php if ($validation->hasError('kode_program')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('kode_program') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_program" class="form-label">Nama Program <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $validation->hasError('nama_program') ? 'is-invalid' : '' ?>" 
                                           id="nama_program" name="nama_program" 
                                           value="<?= old('nama_program', $program['nama_program']) ?>" required>
                                    <?php if ($validation->hasError('nama_program')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('nama_program') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan Program <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= $validation->hasError('tujuan') ? 'is-invalid' : '' ?>" 
                                      id="tujuan" name="tujuan" rows="3" required><?= old('tujuan', $program['tujuan']) ?></textarea>
                            <?php if ($validation->hasError('tujuan')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('tujuan') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?= old('deskripsi', $program['deskripsi']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_dana" class="form-label">Target Dana <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control <?= $validation->hasError('target_dana') ? 'is-invalid' : '' ?>" 
                                               id="target_dana" name="target_dana" 
                                               value="<?= old('target_dana', $program['target_dana']) ?>" min="0" step="1000" required>
                                    </div>
                                    <?php if ($validation->hasError('target_dana')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('target_dana') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kewajiban_default" class="form-label">Kewajiban Default per Pengguna <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control <?= $validation->hasError('kewajiban_default') ? 'is-invalid' : '' ?>" 
                                               id="kewajiban_default" name="kewajiban_default" 
                                               value="<?= old('kewajiban_default', $program['kewajiban_default']) ?>" min="0" step="1000" required>
                                    </div>
                                    <?php if ($validation->hasError('kewajiban_default')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('kewajiban_default') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="aktif" <?= $program['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $program['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Mengubah kewajiban default tidak akan mengubah kewajiban peserta yang sudah terdaftar.
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/admin/program" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
