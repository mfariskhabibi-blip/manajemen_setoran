<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Detail Program Iuran</h1>
                <div>
                    <a href="/admin/program" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <a href="/admin/program/<?= $program['id'] ?>/edit" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>

            <!-- Program Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Program</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Kode Program:</strong></td>
                                    <td><?= esc($program['kode_program']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Program:</strong></td>
                                    <td><?= esc($program['nama_program']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if ($program['status'] === 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat:</strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($program['created_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Target Dana:</strong></td>
                                    <td>Rp <?= number_format($program['target_dana'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kewajiban Default:</strong></td>
                                    <td>Rp <?= number_format($program['kewajiban_default'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Peserta:</strong></td>
                                    <td><?= $program['jumlah_peserta'] ?? 0 ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Progress:</strong></td>
                                    <td><?= round($program['progress'] ?? 0, 1) ?>%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Tujuan:</strong>
                            <p><?= nl2br(esc($program['tujuan'])) ?></p>
                        </div>
                    </div>
                    <?php if ($program['deskripsi']): ?>
                    <div class="row">
                        <div class="col-12">
                            <strong>Deskripsi:</strong>
                            <p><?= nl2br(esc($program['deskripsi'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Target Dana</h6>
                            <h4 class="card-text">Rp <?= number_format($program['target_dana'] ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Setoran</h6>
                            <h4 class="card-text">Rp <?= number_format($program['total_setoran'] ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Sisa Target</h6>
                            <h4 class="card-text">Rp <?= number_format($program['total_kekurangan'] ?? 0, 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Progress</h6>
                            <h4 class="card-text"><?= round($program['progress'] ?? 0, 1) ?>%</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Progress Menuju Target Dana</h5>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= min($program['progress'] ?? 0, 100) ?>%"
                             aria-valuenow="<?= $program['progress'] ?? 0 ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <?= round($program['progress'] ?? 0, 1) ?>%
                        </div>
                    </div>
                    <small class="text-muted">
                        Rp <?= number_format($program['total_setoran'] ?? 0, 0, ',', '.') ?> dari target Rp <?= number_format($program['target_dana'] ?? 0, 0, ',', '.') ?>
                    </small>
                </div>
            </div>

            <!-- Periodes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Periode Program</h5>
                    <div>
                        <span class="badge bg-info me-2"><?= count($periodes) ?> Periode</span>
                        <a href="<?= base_url('admin/program/' . $program['id'] . '/add-periode') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Tambah Periode
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($periodes)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Belum ada periode untuk program ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Periode</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Kewajiban</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($periodes as $periode): ?>
                                    <tr>
                                        <td><?= esc($periode['nama_periode']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($periode['tanggal_mulai'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($periode['tanggal_selesai'])) ?></td>
                                        <td>Rp <?= number_format($periode['nominal_kewajiban'], 0, ',', '.') ?></td>
                                        <td>
                                            <form method="post" action="/admin/program/<?= $program['id'] ?>/update-periode-status/<?= $periode['id'] ?>" style="display: inline-block;">
                                                <?= csrf_field() ?>
                                                <select name="status" class="form-select form-select-sm" 
                                                        onchange="if(confirm('Apakah Anda yakin ingin mengubah status periode ini?')) { this.form.submit(); } else { this.selectedIndex = <?= array_search($periode['status'], ['belum_aktif', 'aktif', 'selesai']) ?>; }"
                                                        style="width: auto; display: inline-block;">
                                                    <option value="belum_aktif" <?= $periode['status'] === 'belum_aktif' ? 'selected' : '' ?>>Belum Aktif</option>
                                                    <option value="aktif" <?= $periode['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                                    <option value="selesai" <?= $periode['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <button onclick="confirmDeletePeriode(<?= $program['id'] ?>, <?= $periode['id'] ?>, '<?= esc($periode['nama_periode']) ?>')" 
                                                    class="btn btn-sm btn-danger" title="Hapus Periode">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Participants -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Peserta Program</h5>
                    <div>
                        <a href="/admin/program/<?= $program['id'] ?>/add-participant" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Tambah Peserta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($participants)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Belum ada peserta untuk program ini.</p>
                            <a href="/admin/program/<?= $program['id'] ?>/add-participant" class="btn btn-primary mt-2">
                                Tambah Peserta Pertama
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Total Kewajiban</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                    <?php 
                                    $userModel = new \App\Models\UserModel();
                                    $user = $userModel->find($participant['user_id']);
                                    ?>
                                    <tr>
                                        <td><?= esc($user['nama'] ?? '-') ?></td>
                                        <td><?= esc($user['username'] ?? '-') ?></td>
                                        <td>Rp <?= number_format($participant['total_kewajiban'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ($participant['status'] === 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button onclick="confirmRemoveParticipant(<?= $participant['id'] ?>, '<?= esc($user['nama'] ?? '') ?>')" 
                                                    class="btn btn-sm btn-danger" title="Hapus Peserta">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
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
</div>

<script>
function confirmRemoveParticipant(id, name) {
    if (confirm('Apakah Anda yakin ingin menghapus "' + name + '" dari program ini?')) {
        fetch('/admin/program/<?= $program['id'] ?>/remove-participant/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus peserta');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan');
        });
    }
}

function confirmDeletePeriode(programId, periodeId, name) {
    if (confirm('Apakah Anda yakin ingin menghapus periode "' + name + '"?')) {
        fetch('/admin/program/' + programId + '/delete-periode/' + periodeId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus periode');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan');
        });
    }
}
</script>
<?= $this->endSection() ?>
