<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Tambah Periode Program</h1>
                <a href="/admin/program/<?= $program['id'] ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Periode Program</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/program/store-periode" method="post">
                        <?= csrf_field() ?>
                        
                        <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="nama_periode" class="form-label">Nama Periode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= $validation->hasError('nama_periode') ? 'is-invalid' : '' ?>" 
                                   id="nama_periode" name="nama_periode" 
                                   value="<?= old('nama_periode') ?>" required>
                            <?php if ($validation->hasError('nama_periode')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('nama_periode') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Contoh: Januari 2026, Semester 1 2026, dll.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?= $validation->hasError('tanggal_mulai') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_mulai" name="tanggal_mulai" 
                                           value="<?= old('tanggal_mulai') ?>" required>
                                    <?php if ($validation->hasError('tanggal_mulai')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('tanggal_mulai') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?= $validation->hasError('tanggal_selesai') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_selesai" name="tanggal_selesai" 
                                           value="<?= old('tanggal_selesai') ?>" required>
                                    <?php if ($validation->hasError('tanggal_selesai')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('tanggal_selesai') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nominal_kewajiban" class="form-label">Nominal Kewajiban per Peserta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control <?= $validation->hasError('nominal_kewajiban') ? 'is-invalid' : '' ?>" 
                                       id="nominal_kewajiban" name="nominal_kewajiban" 
                                       value="<?= old('nominal_kewajiban', $program['kewajiban_default'] ?? 0) ?>" min="1" step="1" required>
                            </div>
                            <?php if ($validation->hasError('nominal_kewajiban')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('nominal_kewajiban') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted">Kewajiban yang harus dibayar setiap peserta untuk periode ini</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informasi:</strong> 
                            <ul class="mb-0">
                                <li>Periode baru akan dibuat dengan status "Belum Aktif"</li>
                                <li>Pastikan tanggal tidak bertabrakan dengan periode lain</li>
                                <li>Status dapat diubah ke "Aktif" atau "Selesai" nanti</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="/admin/program/<?= $program['id'] ?>" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Periode
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Existing Periodes -->
            <?php
            $periodeModel = new \App\Models\PeriodeModel();
            $existingPeriodes = $periodeModel->getByProgram($program['id']);
            ?>
            <?php if (!empty($existingPeriodes)): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Periode yang Sudah Ada</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Kewajiban</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($existingPeriodes as $periode): ?>
                                <tr>
                                    <td><?= esc($periode['nama_periode']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($periode['tanggal_mulai'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($periode['tanggal_selesai'])) ?></td>
                                    <td>Rp <?= number_format($periode['nominal_kewajiban'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($periode['status'] === 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($periode['status'] === 'selesai'): ?>
                                            <span class="badge bg-primary">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Set minimum date for tanggal_selesai based on tanggal_mulai
document.getElementById('tanggal_mulai').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('tanggal_selesai');
    
    if (startDate && endDateInput.value && endDateInput.value < startDate) {
        endDateInput.value = startDate;
    }
    
    endDateInput.min = startDate;
});

// Set default tanggal_mulai to today and tanggal_selesai to end of month
window.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('tanggal_mulai');
    const endDateInput = document.getElementById('tanggal_selesai');
    
    if (!startDateInput.value) {
        startDateInput.value = today;
    }
    
    if (!endDateInput.value && startDateInput.value) {
        // Set end date to end of month
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
        endDateInput.value = endDate.toISOString().split('T')[0];
    }
});
</script>
<?= $this->endSection() ?>