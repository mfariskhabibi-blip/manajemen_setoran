<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Active Event Context Banner (High Contrast Slate & Gold Theme) -->
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
                    <div>
                        <a href="<?= base_url('admin/acara') ?>" class="btn btn-warning text-dark fw-bold shadow-sm px-4 py-2 rounded-pill w-100 w-sm-auto text-center">
                            <i class="fas fa-cog me-2"></i>Pengaturan Acara
                        </a>
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

    <!-- Header Title & Action Buttons -->
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold"><i class="fas fa-cash-register me-2 text-primary"></i>Input & Kelola Setoran Acara</h1>
            <p class="text-muted small mb-0">Catat transaksi setoran warga, verifikasi bukti bayar, dan rekap pembayaran.</p>
        </div>
        <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto mt-2 mt-md-0">
            <button type="button" class="btn btn-primary shadow-sm rounded-pill px-3 py-2 w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#modalQuickAdd">
                <i class="fas fa-plus-circle me-2"></i>Catat Setoran Manual
            </button>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <a href="<?= base_url('admin/setoran/belum-dicatat') ?>" class="btn btn-outline-warning rounded-pill px-3 py-2 w-50 w-md-auto d-flex justify-content-center align-items-center" style="white-space: nowrap;">
                    <i class="fas fa-user-clock me-1 d-none d-sm-inline"></i> Belum Lunas
                </a>
                <a href="<?= base_url('admin/setoran/export') ?>" class="btn btn-outline-success rounded-pill px-3 py-2 w-50 w-md-auto d-flex justify-content-center align-items-center" style="white-space: nowrap;">
                    <i class="fas fa-file-excel me-1 d-none d-sm-inline"></i> Ekspor CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Filter Tabs + Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white pt-3 pb-md-0 border-bottom-0">
            <div class="pb-1">
                <ul class="nav nav-tabs card-header-tabs flex-column flex-md-row border-bottom-0 gap-2 gap-md-0">
                    <li class="nav-item">
                        <a class="nav-link <?= empty($filters['status']) ? 'active fw-bold bg-light bg-md-white border-0 border-md' : 'text-dark border-0' ?>" href="<?= base_url('admin/setoran') ?>">
                            <i class="fas fa-list me-2 text-primary"></i>Semua Setoran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($filters['status'] ?? '') === 'tercatat' ? 'active fw-bold text-dark bg-warning bg-opacity-25 bg-md-white border-0 border-md' : 'text-dark border-0' ?>" href="<?= base_url('admin/setoran?status=tercatat') ?>">
                            <i class="fas fa-clock me-2 text-warning"></i>Perlu Verifikasi
                            <?php if (!empty($adminStats['count_pending']) && $adminStats['count_pending'] > 0): ?>
                                <span class="badge bg-warning text-dark rounded-pill ms-2"><?= $adminStats['count_pending'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($filters['status'] ?? '') === 'diverifikasi' ? 'active fw-bold text-success bg-success bg-opacity-10 bg-md-white border-0 border-md' : 'text-dark border-0' ?>" href="<?= base_url('admin/setoran?status=diverifikasi') ?>">
                            <i class="fas fa-check-circle me-2 text-success"></i>Diverifikasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($filters['status'] ?? '') === 'dibatalkan' ? 'active fw-bold text-danger bg-danger bg-opacity-10 bg-md-white border-0 border-md' : 'text-dark border-0' ?>" href="<?= base_url('admin/setoran?status=dibatalkan') ?>">
                            <i class="fas fa-times-circle me-2 text-danger"></i>Dibatalkan
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Search & Filters -->
            <form action="<?= base_url('admin/setoran') ?>" method="get" class="mb-4">
                <?php if (!empty($filters['status'])): ?>
                    <input type="hidden" name="status" value="<?= esc($filters['status']) ?>">
                <?php endif; ?>

                <div class="row g-2 align-items-center mb-2">
                    <div class="col-md-7 col-12">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 border-light"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 border-light ps-0" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari warga, nominal...">
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Cari</button>
                        </div>
                    </div>

                    <div class="col-md-5 col-12 text-md-end d-flex justify-content-md-end gap-2 mt-2 mt-md-0">
                        <button class="btn btn-outline-secondary rounded-pill flex-fill flex-md-grow-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilterAccordion">
                            <i class="fas fa-sliders-h me-2"></i>Filter Lanjutan
                        </button>
                        <a href="<?= base_url('admin/setoran') ?>" class="btn btn-light rounded-pill border text-muted px-4" title="Reset">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>

                <div class="collapse mt-3 <?= (($filters['user_id'] ?? false) || ($filters['start_date'] ?? false)) ? 'show' : '' ?>" id="advancedFilterAccordion">
                    <div class="card card-body bg-light border-0 rounded-3 p-3">
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label small fw-semibold">Warga</label>
                                <select name="user_id" class="form-select form-select-sm">
                                    <option value="">Semua Warga</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= esc($u['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label small fw-semibold">Rentang Tanggal</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date'] ?? '') ?>">
                                    <span class="input-group-text">-</span>
                                    <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Transactions Table (Desktop Only) -->
            <div class="d-none d-md-block table-responsive border shadow-sm rounded-4 mb-4">
                <table class="table table-hover align-middle bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Warga</th>
                            <th>Tanggal Setor</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi & WA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $setoransList = $setorans ?? $setoran ?? []; ?>
                        <?php if (empty($setoransList)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 d-block opacity-50"></i>
                                    Belum ada data setoran ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($setoransList as $s): ?>
                                <?php 
                                    $rawWa = $s['user_wa'] ?? '';
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $rawWa);
                                    if (substr($cleanPhone, 0, 1) === '0') {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                    $statusLabel = $s['status_setoran'] === 'diverifikasi' ? 'Diverifikasi' : ($s['status_setoran'] === 'tercatat' ? 'Tercatat (Perlu Verifikasi)' : 'Dibatalkan');
                                    $waMsg = rawurlencode("Halo Bpk/Ibu " . ($s['user_name'] ?? 'Warga') . ", mengonfirmasi catatan setoran sebesar Rp " . number_format($s['nominal'], 0, ',', '.') . " pada " . date('d M Y', strtotime($s['tanggal_setoran'])) . " status: " . $statusLabel . ". Terima kasih.");
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"><?= esc($s['user_name'] ?? 'Warga') ?></div>
                                        <small class="text-muted">ID Warga: #<?= $s['user_id'] ?></small>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d M Y', strtotime($s['tanggal_setoran'])) ?>
                                    </td>
                                    <td class="fw-bold text-success">
                                        Rp <?= number_format($s['nominal'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($s['status_setoran'] === 'diverifikasi'): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i>Diverifikasi</span>
                                        <?php elseif ($s['status_setoran'] === 'tercatat'): ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i>Perlu Verifikasi</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-1"><i class="fas fa-times-circle me-1"></i>Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <?php if (!empty($cleanPhone)): ?>
                                                <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMsg ?>" target="_blank" class="btn btn-sm btn-success" title="Kirim WA Konfirmasi">
                                                    <i class="fab fa-whatsapp me-1"></i>WA
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($s['status_setoran'] === 'tercatat'): ?>
                                                <a href="<?= base_url('admin/setoran/' . $s['id'] . '/verify') ?>" class="btn btn-sm btn-outline-success" title="Verifikasi">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Transactions Cards (Mobile Only) -->
            <div class="d-block d-md-none mb-4">
                <?php if (empty($setoransList)): ?>
                    <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                        <i class="fas fa-receipt fa-3x mb-3 d-block opacity-50"></i>
                        Belum ada data.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($setoransList as $s): ?>
                            <?php 
                                $rawWa = $s['user_wa'] ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $rawWa);
                                if (substr($cleanPhone, 0, 1) === '0') {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                $statusLabel = $s['status_setoran'] === 'diverifikasi' ? 'Diverifikasi' : ($s['status_setoran'] === 'tercatat' ? 'Tercatat (Perlu Verifikasi)' : 'Dibatalkan');
                                $waMsg = rawurlencode("Halo Bpk/Ibu " . ($s['user_name'] ?? 'Warga') . ", mengonfirmasi catatan setoran sebesar Rp " . number_format($s['nominal'], 0, ',', '.') . " pada " . date('d M Y', strtotime($s['tanggal_setoran'])) . " status: " . $statusLabel . ". Terima kasih.");
                            ?>
                            <div class="card border-0 rounded-4 shadow-sm bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= esc($s['user_name'] ?? 'Warga') ?></div>
                                            <small class="text-muted"><i class="fas fa-hashtag"></i> <?= $s['user_id'] ?> &nbsp;|&nbsp; <?= date('d M Y', strtotime($s['tanggal_setoran'])) ?></small>
                                        </div>
                                        <div>
                                            <?php if ($s['status_setoran'] === 'diverifikasi'): ?>
                                                <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                            <?php elseif ($s['status_setoran'] === 'tercatat'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-clock"></i></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-times-circle"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-success" style="font-size: 1.15rem;">
                                            Rp <?= number_format($s['nominal'], 0, ',', '.') ?>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <?php if (!empty($cleanPhone)): ?>
                                                <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMsg ?>" target="_blank" class="btn btn-success rounded-pill shadow-sm px-4 py-2" title="Kirim WA" style="background:#25D366; border-color:#25D366;">
                                                    <i class="fab fa-whatsapp d-none d-sm-inline"></i> WA
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($s['status_setoran'] === 'tercatat'): ?>
                                                <a href="<?= base_url('admin/setoran/' . $s['id'] . '/verify') ?>" class="btn btn-outline-success rounded-pill shadow-sm px-4 py-2">
                                                    <i class="fas fa-check d-none d-sm-inline"></i> Verif
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pager)): ?>
                <div class="p-3 d-flex justify-content-end">
                    <?= $pager->links('default', 'default_full') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Quick Add Setoran -->
<div class="modal fade" id="modalQuickAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-dark text-white border-bottom-0 rounded-top-4 p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-1"><i class="fas fa-cash-register text-warning me-2"></i>Catat Setoran Manual Warga</h5>
                    <p class="text-white-50 small mb-0">Isi formulir pembayaran iuran untuk mencatat transaksi setoran warga secara manual.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/setoran/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12 col-12">
                            <label class="form-label fw-semibold">Pilih Warga <span class="text-danger">*</span></label>
                            <select name="user_id" id="quickAddUserId" class="form-select form-select-lg" required>
                                <option value="">-- Pilih Nama Warga --</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= esc($u['nama']) ?> (@<?= esc($u['username']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Setoran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg mb-2">
                            <span class="input-group-text fw-bold bg-light">Rp</span>
                            <input type="number" step="1000" name="nominal" id="inputNominalQuick" class="form-control fw-bold text-success" placeholder="Contoh: 100000" required>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPresetNominal(50000)">+ Rp 50.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPresetNominal(100000)">+ Rp 100.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPresetNominal(200000)">+ Rp 200.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="setPresetNominal(500000)">+ Rp 500.000</button>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Tanggal Setor <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_setoran" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select">
                                <option value="Tunai / Cash">Tunai / Cash</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional mengenai setoran ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-save me-2"></i>Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setPresetNominal(val) {
        document.getElementById('inputNominalQuick').value = val;
    }
</script>
<?= $this->endSection() ?>
