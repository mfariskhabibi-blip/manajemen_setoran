<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Active Event Context Banner -->
    <?php if (!empty($activeEvent)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #f59e0b !important;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-2">
                            <i class="fas fa-star me-1"></i>ACARA AKTIF SAAT INI
                        </span>
                        <h2 class="h3 fw-bold mb-2 text-white"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></h2>
                        <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 text-light" style="color: #cbd5e1 !important; font-size: 0.9rem;">
                            <span><i class="fas fa-calendar-day text-warning me-1"></i> <strong>Pelaksanaan:</strong> <?= $activeEvent['tanggal_pelaksanaan'] ? date('d M Y', strtotime($activeEvent['tanggal_pelaksanaan'])) : '-' ?></span>
                            <span class="text-white-50 d-none d-sm-inline">•</span>
                            <span><i class="fas fa-map-marker-alt text-danger me-1"></i> <strong>Lokasi:</strong> <?= esc($activeEvent['lokasi'] ?? 'Lapangan Utama') ?></span>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25" style="border-color: #475569;">

                <!-- Dual Progress Bar for Event Target -->
                <?php 
                    $targetDana = (float)($activeEvent['target_total'] ?? 50000000);
                    $terkumpulAcara = (float)($eventSummary['total_terkumpul'] ?? 0);
                    $pGross = $progressGross ?? ($targetDana > 0 ? min(100, round(($terkumpulAcara / $targetDana) * 100, 1)) : 0);
                    $pNet   = $progressNet ?? 0;
                    $kasNet = $saldoKasNet ?? 0;
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-light fw-semibold" style="color: #e2e8f0 !important;"><i class="fas fa-hand-holding-usd text-warning me-1"></i>Setoran Masuk (Gross):</small>
                        <strong class="text-warning small"><?= $pGross ?>% (Rp <?= number_format($terkumpulAcara, 0, ',', '.') ?> / Rp <?= number_format($targetDana, 0, ',', '.') ?>)</strong>
                    </div>
                    <div class="progress rounded-pill bg-dark bg-opacity-50 mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pGross ?>%;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-light fw-semibold" style="color: #cbd5e1 !important;"><i class="fas fa-wallet text-info me-1"></i>Saldo Kas Bersih (Net):</small>
                        <strong class="text-info small"><?= $pNet ?>% (Rp <?= number_format($kasNet, 0, ',', '.') ?> / Rp <?= number_format($targetDana, 0, ',', '.') ?>)</strong>
                    </div>
                    <div class="progress rounded-pill bg-dark bg-opacity-50" style="height: 8px;">
                        <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $pNet ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-history me-2 text-primary"></i><?= esc($title) ?></h1>
            <p class="text-muted small mb-0">Catatan riwayat transaksi setoran iuran yang telah tercatat dalam sistem.</p>
        </div>
        <div class="w-100 w-sm-auto text-end">
            <a href="<?= base_url('riwayat/export') ?>?format=excel&periode=<?= $filters['periode'] ?? '' ?>&start_date=<?= $filters['start_date'] ?? '' ?>&end_date=<?= $filters['end_date'] ?? '' ?>" class="btn btn-success rounded-pill px-4 py-2 w-100 w-sm-auto text-center shadow-sm">
                <i class="fas fa-file-excel me-2"></i>Ekspor Excel
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Transaksi</div>
                        <div class="h4 mb-0 fw-bold text-dark text-truncate"><?= number_format($stats['count'] ?? 0) ?> Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Nominal Setoran</div>
                        <div class="h4 mb-0 fw-bold text-success text-truncate">Rp <?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Status Setoran</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="tercatat" <?= ($filters['status'] ?? '') == 'tercatat' ? 'selected' : '' ?>>Tercatat</option>
                        <option value="diverifikasi" <?= ($filters['status'] ?? '') == 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                        <option value="dikoreksi" <?= ($filters['status'] ?? '') == 'dikoreksi' ? 'selected' : '' ?>>Dikoreksi</option>
                    </select>
                </div>
                <div class="col-md-5 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Rentang Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date'] ?? '') ?>">
                        <span class="input-group-text">-</span>
                        <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filter</button>
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-light border"><i class="fas fa-sync"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-3 p-md-4 pt-0">
            <!-- Desktop Table -->
            <div class="d-none d-md-block table-responsive border shadow-sm rounded-4">
                <table class="table table-hover align-middle bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Tanggal Setor</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($setoran)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
                                    Belum ada riwayat setoran ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($setoran as $item): ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= date('d M Y', strtotime($item['tanggal_setoran'])) ?></div>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Rp <?= number_format($item['nominal'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($item['status_setoran'] === 'diverifikasi'): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i>Diverifikasi</span>
                                        <?php elseif ($item['status_setoran'] === 'tercatat'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i>Tercatat</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-1"><?= ucfirst($item['status_setoran']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('riwayat/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i> Struk
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-block d-md-none">
                <?php if (empty($setoran)): ?>
                    <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                        <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
                        Belum ada riwayat setoran ditemukan.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($setoran as $item): ?>
                            <div class="card border-0 rounded-4 shadow-sm bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3" style="border-color: #f1f5f9 !important;">
                                        <div class="fw-bold text-dark"><?= date('d M Y', strtotime($item['tanggal_setoran'])) ?></div>
                                        <div>
                                            <?php if ($item['status_setoran'] === 'diverifikasi'): ?>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                            <?php elseif ($item['status_setoran'] === 'tercatat'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-clock"></i></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill px-2 py-1"><?= ucfirst($item['status_setoran']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-success fs-5">Rp <?= number_format($item['nominal'], 0, ',', '.') ?></div>
                                        <a href="<?= base_url('riwayat/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-eye me-1"></i> Struk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-3 d-flex justify-content-center">
                <?= $pager ? $pager->links('default', 'default_full') : '' ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
