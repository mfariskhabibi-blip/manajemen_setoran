<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Selamat datang, <?= esc($user['nama'] ?? 'Warga') ?>! 👋</h1>
            <p class="text-muted small mb-0">Pantau perkembangan iuran acara dan catatan setoran pribadi Anda.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm fs-6">
                <i class="fas fa-calendar-alt text-primary me-2"></i><?= date('d F Y') ?>
            </span>
        </div>
    </div>

    <!-- Active Event Transparency Banner (Dark Slate Navy & Gold) -->
    <?php if (!empty($activeEvent)): ?>
        <?php 
        $targetDana = (float)($activeEvent['target_dana'] ?? 50000000);
        $totalTerkumpul = (float)($eventSummary['total_terkumpul'] ?? 0);
        $pct = $targetDana > 0 ? min(100, round(($totalTerkumpul / $targetDana) * 100, 1)) : 0;
        ?>
        <div class="card border-0 shadow-sm rounded-4 text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #f59e0b !important;">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                                <i class="fas fa-star me-1"></i>ACARA AKTIF SAAT INI
                            </span>
                            <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                                Pelaksanaan: <?= date('d M Y', strtotime($activeEvent['tanggal_pelaksanaan'] ?? '2026-05-15')) ?>
                            </span>
                        </div>
                        <h2 class="h3 fw-bold mb-2 text-white"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></h2>
                        <p class="mb-3 text-light small" style="color: #cbd5e1 !important;">
                            <i class="fas fa-map-marker-alt text-warning me-1"></i><?= esc($activeEvent['lokasi'] ?? 'Lapangan Warga Utama') ?>
                            <span class="mx-2 d-none d-sm-inline">•</span>
                            <br class="d-sm-none">
                            Kewajiban Iuran Per Warga: <strong class="text-white">Rp <?= number_format($userKewajiban ?? 200000, 0, ',', '.') ?></strong>
                        </p>

                        <!-- Dual Progress Target Acara Komunitas -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-light fw-semibold" style="color: #e2e8f0 !important;"><i class="fas fa-hand-holding-usd text-warning me-1"></i>Setoran Masuk (Gross):</small>
                                <strong class="text-warning small"><?= $progressGross ?? 0 ?>% (Rp <?= number_format($totalSetoranKomunitas ?? $totalTerkumpul, 0, ',', '.') ?> / Rp <?= number_format($targetTotalEvent ?? $targetDana, 0, ',', '.') ?>)</strong>
                            </div>
                            <div class="progress rounded-pill bg-dark bg-opacity-50 mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressGross ?? 0 ?>%;"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-light fw-semibold" style="color: #cbd5e1 !important;"><i class="fas fa-wallet text-info me-1"></i>Saldo Kas Bersih (Net):</small>
                                <strong class="text-info small"><?= $progressNet ?? 0 ?>% (Rp <?= number_format($saldoKasNetKomunitas ?? 0, 0, ',', '.') ?> / Rp <?= number_format($targetTotalEvent ?? $targetDana, 0, ',', '.') ?>)</strong>
                            </div>
                            <div class="progress rounded-pill bg-dark bg-opacity-50" style="height: 8px;">
                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressNet ?? 0 ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= base_url('setoran') ?>" class="btn btn-warning fw-bold text-dark rounded-pill px-4 py-2 shadow-sm w-100 w-sm-auto text-center">
                            <i class="fas fa-receipt me-2"></i>Lihat Struk Setoran Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- User Event Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Kewajiban Acara</small>
                        <h4 class="fw-bold mb-0 text-dark text-truncate">Rp <?= number_format($userKewajiban ?? 200000, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Sudah Dibayar</small>
                        <h4 class="fw-bold mb-0 text-success text-truncate">Rp <?= number_format($userEventSetoran ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 <?= ($userSisaTagihan ?? 0) <= 0 ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= ($userSisaTagihan ?? 0) <= 0 ? 'text-success' : 'text-danger' ?> me-3 flex-shrink-0">
                        <i class="fas <?= ($userSisaTagihan ?? 0) <= 0 ? 'fa-award' : 'fa-exclamation-triangle' ?> fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Status Tagihan Anda</small>
                        <?php if (($userSisaTagihan ?? 0) <= 0): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">LUNAS</span>
                        <?php else: ?>
                            <h4 class="fw-bold mb-0 text-danger text-truncate">Rp <?= number_format($userSisaTagihan, 0, ',', '.') ?></h4>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Setoran History -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i>Catatan Pembayaran Setoran Anda</h5>
                    <a href="<?= base_url('setoran') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua Struk</a>
                </div>
                <div class="card-body p-3 p-md-4 pt-0">
                    <?php if (empty($recentSetoran)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-muted">Belum ada catatan setoran</h5>
                            <p class="text-muted mb-0">Setoran yang Anda serahkan ke pengurus akan tampil di sini setelah diverifikasi.</p>
                        </div>
                    <?php else: ?>
                        <!-- Desktop Table -->
                        <div class="d-none d-md-block table-responsive border shadow-sm rounded-4">
                            <table class="table table-hover align-middle bg-white mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Tanggal Setor</th>
                                        <th>Nominal Setoran</th>
                                        <th>Status Verifikasi</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($recentSetoran as $setoran): ?>
                                    <tr>
                                        <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($setoran['tanggal_setoran'])) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($setoran['created_at'])) ?></small>
                                        </td>
                                        <td class="fw-bold text-success h6 mb-0">
                                            Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i> Diverifikasi
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <?= esc($setoran['keterangan'] ?? 'Setoran iuran acara') ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-block d-md-none mt-2">
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($recentSetoran as $setoran): ?>
                                    <div class="card border-0 rounded-4 shadow-sm bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                                <div>
                                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($setoran['tanggal_setoran'])) ?></div>
                                                    <small class="text-muted"><?= date('H:i', strtotime($setoran['created_at'])) ?></small>
                                                </div>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-success fs-5">Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></div>
                                                <small class="text-muted"><?= esc($setoran['keterangan'] ?? 'Setoran iuran acara') ?></small>
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
    </div>
</div>
<?= $this->endSection() ?>