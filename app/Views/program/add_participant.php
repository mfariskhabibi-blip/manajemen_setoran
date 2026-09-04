<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Tambah Peserta Program</h1>
                <a href="/admin/program/<?= $program['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Program: <?= esc($program['nama_program']) ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Kode Program:</strong></td>
                            <td><?= esc($program['kode_program']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kewajiban Default:</strong></td>
                            <td>Rp <?= number_format($program['kewajiban_default'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Tambah Peserta</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/program/store-participant" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Pilih Pengguna <span class="text-danger">*</span></label>
                            <select class="form-select <?= $validation->hasError('user_id') ? 'is-invalid' : '' ?>" 
                                    id="user_id" name="user_id" required>
                                <option value="">-- Pilih Pengguna --</option>
                                <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= esc($user['nama']) ?> (<?= esc($user['username']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($validation->hasError('user_id')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('user_id') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="periode_id" class="form-label">Periode (Opsional)</label>
                            <select class="form-select" id="periode_id" name="periode_id">
                                <option value="0">-- Semua Periode --</option>
                                <?php foreach ($periodes as $periode): ?>
                                <option value="<?= $periode['id'] ?>"><?= esc($periode['nama_periode']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Biarkan kosong jika peserta mengikuti semua periode program</small>
                        </div>

                        <div class="mb-3">
                            <label for="total_kewajiban" class="form-label">Total Kewajiban <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control <?= $validation->hasError('total_kewajiban') ? 'is-invalid' : '' ?>" 
                                       id="total_kewajiban" name="total_kewajiban" 
                                       value="<?= old('total_kewajiban', $program['kewajiban_default']) ?>" 
                                       min="0" step="1000" required>
                            </div>
                            <?php if ($validation->hasError('total_kewajiban')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('total_kewajiban') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Default: Rp <?= number_format($program['kewajiban_default'], 0, ',', '.') ?></small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informasi:</strong> Pengguna yang sudah terdaftar sebagai peserta program ini tidak akan muncul dalam daftar.
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/admin/program/<?= $program['id'] ?>" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Tambah Peserta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
