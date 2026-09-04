<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Pusat Kendali Administrator ⚡</h1>
            <p class="text-muted small mb-0">Selamat datang kembali, <strong class="text-dark"><?= esc($user['nama'] ?? 'Admin') ?></strong>!</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm fs-6">
                <i class="fas fa-calendar-alt text-primary me-2"></i><?= date('d F Y') ?>
            </span>
        </div>
    </div>

    <!-- Active Event Context Banner (Dark Slate & Amber Gold Theme) -->
    <?php if (!empty($activeEvent)): ?>
        <?php 
        $targetDana = (float)($activeEvent['target_dana'] ?? 50000000);
        $totalTerkumpul = (float)($eventSummary['total_terkumpul'] ?? 0);
        $pct = $targetDana > 0 ? min(100, round(($totalTerkumpul / $targetDana) * 100, 1)) : 0;
        ?>
        <div class="card border-0 shadow-sm rounded-4 text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #f59e0b !important;">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-7">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                                <i class="fas fa-star me-1"></i>ACARA AKTIF UTAMA
                            </span>
                            <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                                <i class="fas fa-calendar-day me-1"></i>Pelaksanaan: <?= date('d M Y', strtotime($activeEvent['tanggal_pelaksanaan'] ?? '2026-05-15')) ?>
                            </span>
                        </div>
                        <h2 class="h3 fw-bold mb-2 text-white"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></h2>
                        <p class="mb-3 text-light small" style="color: #cbd5e1 !important;">
                            <i class="fas fa-map-marker-alt text-warning me-1"></i><?= esc($activeEvent['lokasi'] ?? 'Lapangan Warga Utama') ?>
                            <span class="mx-2 d-none d-sm-inline">•</span>
                            <br class="d-sm-none">
                            <i class="fas fa-receipt text-warning me-1 mt-1 mt-sm-0"></i>Tarif Iuran: <strong class="text-white">Rp <?= number_format($activeEvent['tarif_default'] ?? 200000, 0, ',', '.') ?></strong> / Warga
                        </p>

                        <!-- Dual Progress Target -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-light fw-semibold" style="color: #e2e8f0 !important;"><i class="fas fa-hand-holding-usd text-warning me-1"></i>Setoran Masuk (Gross):</small>
                                <strong class="text-warning small"><?= $progressGross ?>% (Rp <?= number_format($totalSetoran ?? $totalTerkumpul, 0, ',', '.') ?> / Rp <?= number_format($targetTotalEvent ?? $targetDana, 0, ',', '.') ?>)</strong>
                            </div>
                            <div class="progress rounded-pill bg-dark bg-opacity-50 mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressGross ?>%;"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-light fw-semibold" style="color: #cbd5e1 !important;"><i class="fas fa-wallet text-info me-1"></i>Saldo Kas Bersih (Net):</small>
                                <strong class="text-info small"><?= $progressNet ?>% (Rp <?= number_format($saldoKasNet ?? 0, 0, ',', '.') ?> / Rp <?= number_format($targetTotalEvent ?? $targetDana, 0, ',', '.') ?>)</strong>
                            </div>
                            <div class="progress rounded-pill bg-dark bg-opacity-50" style="height: 8px;">
                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $progressNet ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Admin Event Actions -->
                    <div class="col-lg-5">
                        <div class="d-flex flex-column flex-sm-row justify-content-lg-end gap-2">
                            <a href="<?= base_url('admin/setoran') ?>" class="btn btn-warning fw-bold text-dark rounded-pill px-4 py-2 shadow-sm text-center">
                                <i class="fas fa-cash-register me-2"></i>Catat Setoran
                            </a>
                            <a href="<?= base_url('admin/setoran/belum-dicatat') ?>" class="btn btn-outline-light rounded-pill px-3 py-2 text-center">
                                <i class="fas fa-users-slash me-1"></i>Belum Lunas
                            </a>
                            <a href="<?= base_url('admin/acara') ?>" class="btn btn-outline-light rounded-pill px-3 py-2 text-center">
                                <i class="fas fa-cog me-1"></i>Acara
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Warga</small>
                        <h3 class="fw-bold mb-0 text-dark"><?= $userStats['user'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-success border-4">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Saldo Kas Terkini (Net)</small>
                        <h4 class="fw-bold mb-0 text-success text-truncate">Rp <?= number_format($saldoKasNet ?? (($setoranStats['total_setoran'] ?? 0) - ($totalPengeluaran ?? 0)), 0, ',', '.') ?></h4>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">
                            (Setoran: Rp <?= number_format($setoranStats['total_setoran'] ?? 0, 0, ',', '.') ?> - Pengeluaran)
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Pengeluaran Kas</small>
                        <h4 class="fw-bold mb-0 text-danger text-truncate">Rp <?= number_format($totalPengeluaran ?? 0, 0, ',', '.') ?></h4>
                        <a href="<?= base_url('admin/pengeluaran') ?>" class="text-primary text-decoration-none d-block mt-1" style="font-size: 11px;">
                            Kelola Pengeluaran <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning me-3 flex-shrink-0">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Warga Lunas Acara</small>
                        <h3 class="fw-bold mb-0 text-dark"><?= $eventSummary['warga_lunas'] ?? 0 ?> <small class="text-muted fs-6">orang</small></h3>
                    </div>
                </div>
            </div>
        </div>
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Sisa Belum Lunas</small>
                        <h3 class="fw-bold mb-0 text-danger"><?= $eventSummary['warga_belum_lunas'] ?? 0 ?> <small class="text-muted fs-6">orang</small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Quick Shortcuts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i>Setoran Terbaru</h5>
                    <a href="<?= base_url('admin/setoran') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-3 p-md-4 pt-0">
                    <?php if (empty($recentSetoran)): ?>
                        <div class="text-center py-4 text-muted">Belum ada setoran terbaru.</div>
                    <?php else: ?>
                        <div class="table-responsive border rounded-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Warga</th>
                                        <th>Tanggal</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSetoran as $s): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= esc($s['user_name'] ?? 'Warga') ?></td>
                                        <td class="small text-muted"><?= date('d M Y', strtotime($s['tanggal_setoran'])) ?></td>
                                        <td class="fw-bold text-success">Rp <?= number_format($s['nominal'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i>Diverifikasi</span>
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

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat Admin</h5>
                </div>
                <div class="card-body p-3 p-md-4 pt-0 d-flex flex-column gap-3">
                    <a href="<?= base_url('admin/setoran') ?>" class="btn btn-primary btn-lg rounded-4 text-start p-3 d-flex align-items-center shadow-sm">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3 flex-shrink-0">
                            <i class="fas fa-cash-register fa-lg text-white"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Input Setoran Manual</div>
                            <small class="opacity-75 d-block">Catat pembayaran iuran warga</small>
                        </div>
                    </a>

                    <a href="<?= base_url('admin/setoran/belum-dicatat') ?>" class="btn btn-danger btn-lg rounded-4 text-start p-3 d-flex align-items-center shadow-sm">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3 flex-shrink-0">
                            <i class="fas fa-users-slash fa-lg text-white"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Rekap Belum Lunas</div>
                            <small class="opacity-75 d-block">Cek warga belum bayar & WA pengingat</small>
                        </div>
                    </a>

                    <a href="<?= base_url('admin/acara') ?>" class="btn btn-dark btn-lg rounded-4 text-start p-3 d-flex align-items-center shadow-sm">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3 flex-shrink-0">
                            <i class="fas fa-calendar-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Kelola Acara Komunitas</div>
                            <small class="opacity-75 d-block">Atur skema iuran & target dana</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
