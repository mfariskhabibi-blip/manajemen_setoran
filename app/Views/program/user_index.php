<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Program Iuran Saya</h1>

            <?php if (empty($userPrograms)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Belum Ada Program</h4>
                        <p class="text-muted">Anda belum terdaftar dalam program iuran apapun.</p>
                        <p class="text-muted">Silakan hubungi admin untuk ditambahkan ke program.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($userPrograms as $program): ?>
                    <?php 
                    $pesertaProgramModel = new \App\Models\PesertaProgramModel();
                    $summary = $pesertaProgramModel->getUserProgramSummary(session()->get('user_id'), $program['id']);
                    ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?= esc($program['nama_program']) ?></h5>
                            <span class="badge bg-info"><?= esc($program['kode_program']) ?></span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Tujuan:</strong></td>
                                            <td><?= esc(substr($program['tujuan'], 0, 100)) ?>...</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Program:</strong></td>
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
                                            <td width="40%"><strong>Total Kewajiban:</strong></td>
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
                                            <td><strong>Status:</strong></td>
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
                            
                            <?php if ($summary): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6 class="mb-2">Progress Setoran</h6>
                                    <div class="progress" style="height: 25px;">
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
                            
                            <?php if ($program['deskripsi']): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Deskripsi:</strong>
                                    <p class="text-muted mb-0"><?= nl2br(esc($program['deskripsi'])) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="/program/<?= $program['id'] ?>" class="btn btn-info">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
