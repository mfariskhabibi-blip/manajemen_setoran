<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Detail Program: <?= esc($program['nama_program']) ?></h1>
                <a href="/program" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
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
                            </table>
                        </div>
                        <div class="col-md-6">
                            <?php if ($summary): ?>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Total Kewajiban:</strong></td>
                                    <td>Rp <?= number_format($summary['peserta']['total_kewajiban'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Setoran:</strong></td>
                                    <td>Rp <?= number_format($summary['total_setoran'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Sisa Kewajiban:</strong></td>
                                    <td class="<?= $summary['sisa_kewajiban'] > 0 ? 'text-danger' : 'text-success' ?>">
                                        Rp <?= number_format($summary['sisa_kewajiban'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Progress:</strong></td>
                                    <td><?= round($summary['progress'], 1) ?>%</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Kewajiban:</strong></td>
                                    <td>
                                        <?php if ($summary['status_kewajiban'] === 'selesai'): ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php elseif ($summary['status_kewajiban'] === 'berjalan'): ?>
                                            <span class="badge bg-warning">Berjalan</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum Mulai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>
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

            <?php if ($summary): ?>
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Kewajiban</h6>
                            <h4 class="card-text">Rp <?= number_format($summary['peserta']['total_kewajiban'], 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Setoran</h6>
                            <h4 class="card-text">Rp <?= number_format($summary['total_setoran'], 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card <?= $summary['sisa_kewajiban'] > 0 ? 'bg-warning' : 'bg-info' ?> text-white">
                        <div class="card-body">
                            <h6 class="card-title">Sisa Kewajiban</h6>
                            <h4 class="card-text">Rp <?= number_format($summary['sisa_kewajiban'], 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Progress Setoran Anda</h5>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar <?= $summary['progress'] >= 100 ? 'bg-success' : ($summary['progress'] >= 50 ? 'bg-info' : 'bg-warning') ?>" 
                             role="progressbar" 
                             style="width: <?= $summary['progress'] ?>%"
                             aria-valuenow="<?= $summary['progress'] ?>" 
                             aria-valuemin="0" aria-valuemax="100">
                            <?= round($summary['progress'], 1) ?>%
                        </div>
                    </div>
                    <small class="text-muted">
                        Rp <?= number_format($summary['total_setoran'], 0, ',', '.') ?> dari Rp <?= number_format($summary['peserta']['total_kewajiban'], 0, ',', '.') ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>

            <!-- Periodes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Periode Program</h5>
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
                    <?php endif; ?>
                </div>
            </div>

            <!-- Setoran History for this program -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Setoran Anda</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $setoranModel = new \App\Models\SetoranModel();
                    $setoranHistory = $setoranModel->getByUserAndProgram(session()->get('user_id'), $program['id']);
                    ?>
                    <?php if (empty($setoranHistory)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Belum ada setoran untuk program ini.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Periode</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($setoranHistory as $setoran): ?>
                                    <?php 
                                    $periodeData = $periodeModel->find($setoran['periode_id']);
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($setoran['tanggal_setoran'])) ?></td>
                                        <td><?= esc($periodeData['nama_periode'] ?? '-') ?></td>
                                        <td>Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php if ($setoran['status_setoran'] === 'tercatat'): ?>
                                                <span class="badge bg-info">Tercatat</span>
                                            <?php elseif ($setoran['status_setoran'] === 'diverifikasi'): ?>
                                                <span class="badge bg-success">Diverifikasi</span>
                                            <?php elseif ($setoran['status_setoran'] === 'dikoreksi'): ?>
                                                <span class="badge bg-warning">Dikoreksi</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($setoran['keterangan'] ?? '-') ?></td>
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
<?= $this->endSection() ?>
