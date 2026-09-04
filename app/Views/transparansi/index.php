<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Transparansi Kas 🌐</h1>
            <p class="text-muted small mb-0">Informasi keterbukaan arus kas iuran warga dan pengeluaran dana secara real-time.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm fs-6">
                <i class="fas fa-calendar-alt text-primary me-2"></i><?= date('d F Y') ?>
            </span>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Setoran Diverifikasi</small>
                        <h4 class="fw-bold mb-0 text-success text-truncate">Rp <?= number_format($totalSetoran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Pengeluaran Kas</small>
                        <h4 class="fw-bold mb-0 text-danger text-truncate">Rp <?= number_format($totalPengeluaran ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Saldo Kas Terkini (Net)</small>
                        <h4 class="fw-bold mb-0 text-primary text-truncate">Rp <?= number_format($saldoKas ?? $totalSetoran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Progress Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-md-6 col-12 border-end-md">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold small">
                            <i class="fas fa-hand-holding-usd text-success me-1"></i>Progres Setoran Masuk (Gross):
                            <strong class="text-dark"><?= $progressGross ?? 0 ?>%</strong>
                        </span>
                        <small class="text-muted">(Rp <?= number_format($totalSetoran, 0, ',', '.') ?> / Rp <?= number_format($totalTarget ?? 50000000, 0, ',', '.') ?>)</small>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= $progressGross ?? 0 ?>%" aria-valuenow="<?= $progressGross ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold small">
                            <i class="fas fa-wallet text-primary me-1"></i>Progres Saldo Kas Bersih (Net):
                            <strong class="text-dark"><?= $progressNet ?? 0 ?>%</strong>
                        </span>
                        <small class="text-muted">(Rp <?= number_format($saldoKas ?? $totalSetoran, 0, ',', '.') ?> / Rp <?= number_format($totalTarget ?? 50000000, 0, ',', '.') ?>)</small>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: <?= $progressNet ?? 0 ?>%" aria-valuenow="<?= $progressNet ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <ul class="nav nav-pills card-header-pills gap-2" id="transparansiTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-item-btn btn btn-outline-primary active rounded-pill px-4 py-2 fw-semibold" id="pemasukan-tab" data-bs-toggle="tab" data-bs-target="#pemasukan-pane" type="button" role="tab" aria-controls="pemasukan-pane" aria-selected="true">
                        <i class="fas fa-arrow-circle-down me-2 text-success"></i>Pemasukan Setoran
                        <span class="badge bg-success rounded-pill ms-2"><?= count($setoranList) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item-btn btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran-pane" type="button" role="tab" aria-controls="pengeluaran-pane" aria-selected="false">
                        <i class="fas fa-arrow-circle-up me-2 text-danger"></i>Pengeluaran Kas
                        <span class="badge bg-danger rounded-pill ms-2"><?= count($pengeluaranList) ?></span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="transparansiTabContent">
                
                <!-- TAB 1: PEMASUKAN SETORAN -->
                <div class="tab-pane fade show active" id="pemasukan-pane" role="tabpanel" aria-labelledby="pemasukan-tab">
                    <!-- Desktop Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">No</th>
                                    <th class="py-3">Tanggal Setoran</th>
                                    <th class="py-3">Nama Warga</th>
                                    
                                    <th class="py-3 text-end">Nominal (Rp)</th>
                                    <th class="pe-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($setoranList)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x d-block mb-3 opacity-50"></i>
                                            Belum ada data setoran yang diverifikasi.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($setoranList as $setoran): ?>
                                    <tr>
                                        <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                        <td><?= date('d M Y', strtotime($setoran['tanggal_setoran'])) ?></td>
                                        <td class="fw-medium text-dark"><?= esc($setoran['user_name']) ?></td>
                                        
                                        <td class="text-end fw-bold text-success">
                                            Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                <i class="fas fa-check-circle me-1"></i>Diverifikasi
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-block d-md-none p-3">
                        <?php if (empty($setoranList)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                Belum ada data setoran yang diverifikasi.
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($setoranList as $setoran): ?>
                                    <div class="card border-0 rounded-4 shadow-sm bg-white border-start border-success border-4">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                                <div>
                                                    <div class="fw-bold text-dark fs-6"><?= esc($setoran['user_name']) ?></div>
                                                    <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($setoran['tanggal_setoran'])) ?></small>
                                                </div>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-success fs-5">Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></div>
                                                <?php if($setoran['nama_program']): ?>
                                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small"><?= esc($setoran['nama_program']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- TAB 2: PENGELUARAN KAS -->
                <div class="tab-pane fade" id="pengeluaran-pane" role="tabpanel" aria-labelledby="pengeluaran-tab">
                    <!-- Desktop Table -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">No</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Kategori</th>
                                    <th class="py-3">Keterangan / Keperluan</th>
                                    <th class="py-3 text-end">Jumlah (Rp)</th>
                                    <th class="pe-4 py-3 text-center">Bukti Pendukung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pengeluaranList)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-receipt fa-3x d-block mb-3 opacity-50"></i>
                                            Belum ada catatan pengeluaran kas.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($pengeluaranList as $p): ?>
                                    <tr>
                                        <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= date('d M Y', strtotime($p['tanggal'])) ?></div>
                                            <small class="text-muted">Pencatat: <?= esc($p['admin_name'] ?? 'Admin') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                                <?= esc($p['kategori'] ?? 'Umum') ?>
                                            </span>
                                        </td>
                                        <td class="fw-medium text-dark"><?= esc($p['keterangan']) ?></td>
                                        <td class="text-end fw-bold text-danger">
                                            Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <?php if (!empty($p['file_path'])): ?>
                                                <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="fas fa-download me-1"></i>Unduh Struk
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small"><i class="fas fa-minus"></i> Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-block d-md-none p-3">
                        <?php if (empty($pengeluaranList)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x d-block mb-3 opacity-50"></i>
                                Belum ada catatan pengeluaran kas.
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($pengeluaranList as $p): ?>
                                    <div class="card border-0 rounded-4 shadow-sm bg-white border-start border-danger border-4">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                                <div>
                                                    <div class="fw-bold text-dark fs-6"><?= esc($p['keterangan']) ?></div>
                                                    <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($p['tanggal'])) ?></small>
                                                </div>
                                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small"><?= esc($p['kategori'] ?? 'Umum') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-danger fs-5">- Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></div>
                                                <?php if (!empty($p['file_path'])): ?>
                                                    <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1">
                                                        <i class="fas fa-download me-1"></i>Bukti
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* Custom style for tab toggle active button */
.nav-item-btn.active {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.nav-item-btn#pemasukan-tab.active {
    background-color: var(--bs-primary) !important;
    color: white !important;
    border-color: var(--bs-primary) !important;
}
.nav-item-btn#pemasukan-tab.active i {
    color: white !important;
}
.nav-item-btn#pengeluaran-tab.active {
    background-color: var(--bs-danger) !important;
    color: white !important;
    border-color: var(--bs-danger) !important;
}
.nav-item-btn#pengeluaran-tab.active i {
    color: white !important;
}
</style>
<?= $this->endSection() ?>
