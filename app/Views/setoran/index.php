<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Active Event Banner for User Transparency (High Contrast Theme) -->
    <?php if (!empty($activeEvent)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #10b981 !important;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div>
                        <span class="badge bg-success text-white fw-bold px-3 py-2 rounded-pill mb-2">
                            <i class="fas fa-bullhorn me-1"></i>ACARA KOMUNITAS AKTIF
                        </span>
                        <h2 class="h3 fw-bold mb-2 text-white"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></h2>
                        <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 text-light" style="color: #cbd5e1 !important; font-size: 0.9rem;">
                            <span><i class="fas fa-calendar-day text-warning me-1"></i> <strong>Pelaksanaan:</strong> <?= $activeEvent['tanggal_pelaksanaan'] ? date('d M Y', strtotime($activeEvent['tanggal_pelaksanaan'])) : '-' ?></span>
                            <span class="text-white-50 d-none d-sm-inline">•</span>
                            <span><i class="fas fa-map-marker-alt text-danger me-1"></i> <strong>Lokasi:</strong> <?= esc($activeEvent['lokasi'] ?? 'Lapangan Utama') ?></span>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto text-md-end">
                        <div class="bg-dark bg-opacity-50 border border-secondary px-4 py-2 rounded-3 text-center">
                            <small class="text-light d-block" style="color: #cbd5e1 !important;">Tarif Iuran Warga</small>
                            <span class="h4 fw-bold text-warning mb-0">Rp <?= number_format($activeEvent['tarif_default'] ?? 200000, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <hr class="my-3 opacity-25" style="border-color: #475569;">

                <!-- Dual Progress Bar -->
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

    <!-- Page Header & Summary -->
    <div class="row g-3 align-items-center mb-4">
        <div class="col-md-8 col-12">
            <h1 class="h4 mb-1 text-dark fw-bold"><i class="fas fa-history me-2 text-primary"></i>Riwayat Setoran Iuran Saya</h1>
            <p class="text-muted small mb-0">Catatan pembayaran iuran yang telah diverifikasi oleh pengurus.</p>
        </div>
        <div class="col-md-4 col-12 text-md-end">
            <div class="card border-0 shadow-sm p-3 bg-white rounded-4">
                <small class="text-muted fw-semibold">Total Setoran Saya</small>
                <div class="h4 text-primary fw-bold mb-0">Rp <?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('setoran') ?>" class="row g-2 align-items-center">
                <div class="col-md-5 col-12">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari keterangan..." value="<?= esc($filters['search'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="tercatat" <?= ($filters['status'] == 'tercatat') ? 'selected' : '' ?>>Tercatat</option>
                        <option value="diverifikasi" <?= ($filters['status'] == 'diverifikasi') ? 'selected' : '' ?>>Diverifikasi</option>
                    </select>
                </div>
                <div class="col-md-2 col-3">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
                <div class="col-md-2 col-3">
                    <a href="<?= base_url('setoran') ?>" class="btn btn-sm btn-light border w-100 text-muted"><i class="fas fa-redo me-1"></i>Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Setoran Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-3 p-md-4 pt-0">
            <?php if(empty($setoran)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block opacity-50"></i>
                    <h5 class="text-muted fw-bold">Belum Ada Catatan Setoran</h5>
                    <p class="text-muted mb-0">Setoran Anda untuk acara ini akan tampil di sini setelah dicatat oleh admin.</p>
                </div>
            <?php else: ?>
                <!-- Desktop Table -->
                <div class="d-none d-md-block table-responsive border shadow-sm rounded-4">
                    <table class="table table-hover align-middle bg-white mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Tanggal Setor</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th class="text-end pe-4">Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $counter = 1;
                            foreach($setoran as $item): 
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $counter++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($item['tanggal_setoran'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($item['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-success h6 mb-0">
                                        Rp <?= number_format($item['nominal'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($item['status_setoran'] == 'diverifikasi'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i> Diverifikasi</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i> Tercatat</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= esc($item['keterangan'] ?? 'Setoran Iuran Acara') ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-info text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#detailModal<?= $item['id'] ?>">
                                        <i class="fas fa-receipt me-1"></i>Lihat Struk
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none mt-3">
                    <div class="d-flex flex-column gap-3">
                        <?php $counter2 = 1; foreach($setoran as $item): ?>
                            <div class="card border-0 rounded-4 shadow-sm bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3" style="border-color: #f1f5f9 !important;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($item['tanggal_setoran'])) ?></div>
                                            <small class="text-muted"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($item['created_at'])) ?></small>
                                        </div>
                                        <div>
                                            <?php if ($item['status_setoran'] == 'diverifikasi'): ?>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-clock"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-success fs-5">Rp <?= number_format($item['nominal'], 0, ',', '.') ?></div>
                                        <button class="btn btn-sm btn-outline-info text-dark rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= $item['id'] ?>">
                                            <i class="fas fa-receipt me-1"></i>Struk
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted"><?= esc($item['keterangan'] ?? 'Setoran Iuran Acara') ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Detail Modals -->
<?php foreach($setoran as $item): ?>
<div class="modal fade" id="detailModal<?= $item['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Struk Bukti Setoran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-muted small">Nominal Setoran</div>
                <div class="h2 text-success fw-bold mb-2">Rp <?= number_format($item['nominal'], 0, ',', '.') ?></div>
                <span class="badge bg-success rounded-pill px-3 py-2 mb-4">DIVERIFIKASI PENGURUS</span>

                <table class="table table-sm table-borderless text-start align-middle">
                    <tr>
                        <th class="text-muted fw-normal">Nama Acara</th>
                        <td class="fw-bold text-primary"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Tanggal Setor</th>
                        <td class="fw-bold"><?= date('d F Y', strtotime($item['tanggal_setoran'])) ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Keterangan</th>
                        <td><?= esc($item['keterangan'] ?? 'Tercatat di sistem') ?></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?= $this->endSection() ?>