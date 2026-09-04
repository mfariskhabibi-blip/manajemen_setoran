<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Rekap Kas & Setoran</h1>
            <p class="text-muted small mb-0">Laporan rekapitulasi terpadu pemasukan setoran iuran dan pengeluaran kas komunitas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 w-100 w-sm-auto">
            <a href="<?= base_url('admin/rekap/export?' . http_build_query($filters)) ?>" class="btn btn-success rounded-pill px-3 py-2 flex-grow-1 flex-sm-grow-0 text-center">
                <i class="fas fa-file-excel me-1"></i>Ekspor CSV / Excel
            </a>
            <a href="<?= base_url('admin/rekap/print?' . http_build_query($filters)) ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2 flex-grow-1 flex-sm-grow-0 text-center">
                <i class="fas fa-print me-1"></i>Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Setoran Masuk</div>
                        <div class="h4 mb-0 fw-bold text-success text-truncate">Rp <?= number_format($totalNominal, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Total Pengeluaran Kas</div>
                        <div class="h4 mb-0 fw-bold text-danger text-truncate">Rp <?= number_format($totalPengeluaran ?? 0, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Saldo Kas Terkini (Net)</div>
                        <div class="h4 mb-0 fw-bold text-primary text-truncate">Rp <?= number_format($saldoKasNet ?? ($totalNominal - ($totalPengeluaran ?? 0)), 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning me-3 flex-shrink-0">
                        <i class="fas fa-bullseye fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Target / Kekurangan</div>
                        <div class="h5 mb-0 fw-bold text-dark text-truncate">
                            Rp <?= number_format($totalTarget, 0, ',', '.') ?>
                        </div>
                        <small class="text-warning fw-semibold">Sisa: Rp <?= number_format($totalKekurangan, 0, ',', '.') ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual Progress Capaian Target -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <div class="row g-3">
                <div class="col-md-6 col-12 border-end-md">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold small">
                            <i class="fas fa-hand-holding-usd text-success me-1"></i>Progres Setoran Masuk (Gross):
                            <strong class="text-dark"><?= $progressGross ?>%</strong>
                        </span>
                        <small class="text-muted">(Rp <?= number_format($totalNominal, 0, ',', '.') ?> / Rp <?= number_format($totalTarget, 0, ',', '.') ?>)</small>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: <?= $progressGross ?>%" aria-valuenow="<?= $progressGross ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-bold small">
                            <i class="fas fa-wallet text-primary me-1"></i>Progres Saldo Kas Bersih (Net):
                            <strong class="text-dark"><?= $progressNet ?>%</strong>
                        </span>
                        <small class="text-muted">(Rp <?= number_format($saldoKasNet, 0, ',', '.') ?> / Rp <?= number_format($totalTarget, 0, ',', '.') ?>)</small>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: <?= $progressNet ?>%" aria-valuenow="<?= $progressNet ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <ul class="nav nav-pills card-header-pills gap-2" id="rekapTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-item-btn btn btn-outline-primary active rounded-pill px-4 py-2 fw-semibold" id="rekap-setoran-tab" data-bs-toggle="tab" data-bs-target="#rekap-setoran-pane" type="button" role="tab" aria-controls="rekap-setoran-pane" aria-selected="true">
                        <i class="fas fa-arrow-circle-down me-2 text-success"></i>Rekap Setoran Warga
                        <span class="badge bg-success rounded-pill ms-2"><?= count($setoranList) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item-btn btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold" id="rekap-pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#rekap-pengeluaran-pane" type="button" role="tab" aria-controls="rekap-pengeluaran-pane" aria-selected="false">
                        <i class="fas fa-arrow-circle-up me-2 text-danger"></i>Rekap Pengeluaran Kas
                        <span class="badge bg-danger rounded-pill ms-2"><?= count($pengeluaranList) ?></span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="rekapTabContent">
                
                <!-- TAB 1: REKAP SETORAN WARGA -->
                <div class="tab-pane fade show active" id="rekap-setoran-pane" role="tabpanel" aria-labelledby="rekap-setoran-tab">
                    
                    <!-- Filter Setoran Form -->
                    <div class="p-3 p-md-4 border-bottom bg-light">
                        <form action="<?= base_url('admin/rekap') ?>" method="get" class="row g-2 align-items-end">
                            <div class="col-md-3 col-12">
                                <label class="form-label text-muted small fw-semibold mb-1">Pengguna</label>
                                <select name="user_id" class="form-select">
                                    <option value="">Semua Pengguna</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= $filters['user_id'] == $u['id'] ? 'selected' : '' ?>><?= esc($u['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label text-muted small fw-semibold mb-1">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="tercatat" <?= $filters['status'] === 'tercatat' ? 'selected' : '' ?>>Tercatat</option>
                                    <option value="diverifikasi" <?= $filters['status'] === 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                                    <option value="dikoreksi" <?= $filters['status'] === 'dikoreksi' ? 'selected' : '' ?>>Dikoreksi</option>
                                    <option value="dibatalkan" <?= $filters['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label text-muted small fw-semibold mb-1">Rentang Tanggal</label>
                                <div class="input-group">
                                    <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date']) ?>">
                                    <span class="input-group-text">-</span>
                                    <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                                </div>
                            </div>
                            <div class="col-md-2 col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                                <a href="<?= base_url('admin/rekap') ?>" class="btn btn-light border"><i class="fas fa-sync"></i></a>
                            </div>
                        </form>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle bg-white mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">No</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Nama Pengguna</th>
                                    <th class="py-3">Nominal</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Pencatat</th>
                                    <th class="pe-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($setoranList)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                                            Tidak ada data setoran sesuai filter.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($setoranList as $row): ?>
                                        <tr>
                                            <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                            <td class="small text-muted"><?= date('d M Y', strtotime($row['tanggal_setoran'])) ?></td>
                                            <td class="fw-bold text-dark"><?= esc($row['user_name']) ?></td>
                                            <td class="fw-bold text-success">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                            <td>
                                                <?php if ($row['status_setoran'] === 'tercatat'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Tercatat</span>
                                                <?php elseif ($row['status_setoran'] === 'diverifikasi'): ?>
                                                    <span class="badge bg-success rounded-pill px-3 py-1">Diverifikasi</span>
                                                <?php elseif ($row['status_setoran'] === 'dikoreksi'): ?>
                                                    <span class="badge bg-info rounded-pill px-3 py-1">Dikoreksi</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-1">Dibatalkan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-muted"><?= esc($row['admin_name'] ?? 'Sistem') ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-danger rounded-pill" onclick="deleteSetoran(<?= $row['id'] ?>, '<?= esc($row['user_name']) ?>', 'Rp <?= number_format($row['nominal'], 0, ',', '.') ?>')" title="Hapus / Batalkan">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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
                            <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                                Tidak ada data setoran sesuai filter.
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($setoranList as $row): ?>
                                    <div class="card border-0 rounded-4 shadow-sm bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                                <div>
                                                    <div class="fw-bold text-dark fs-6"><?= esc($row['user_name']) ?></div>
                                                    <small class="text-muted"><i class="fas fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['tanggal_setoran'])) ?></small>
                                                </div>
                                                <div>
                                                    <?php if ($row['status_setoran'] === 'tercatat'): ?>
                                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="fas fa-clock"></i></span>
                                                    <?php elseif ($row['status_setoran'] === 'diverifikasi'): ?>
                                                        <span class="badge bg-success rounded-pill px-2 py-1"><i class="fas fa-check-circle"></i></span>
                                                    <?php elseif ($row['status_setoran'] === 'dikoreksi'): ?>
                                                        <span class="badge bg-info rounded-pill px-2 py-1"><i class="fas fa-pen"></i></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger rounded-pill px-2 py-1"><i class="fas fa-times-circle"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-success fs-5">
                                                    Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="deleteSetoran(<?= $row['id'] ?>, '<?= esc($row['user_name']) ?>', 'Rp <?= number_format($row['nominal'], 0, ',', '.') ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- TAB 2: REKAP PENGELUARAN KAS -->
                <div class="tab-pane fade" id="rekap-pengeluaran-pane" role="tabpanel" aria-labelledby="rekap-pengeluaran-tab">
                    
                    <!-- Top Action Bar for Pengeluaran -->
                    <div class="p-3 p-md-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-dark mb-1"><i class="fas fa-receipt text-danger me-2"></i>Daftar Pengeluaran Kas</h6>
                            <p class="text-muted small mb-0">Catatan pengeluaran dana beserta bukti struk/nota yang telah diinput admin.</p>
                        </div>
                        <a href="<?= base_url('admin/pengeluaran/create') ?>" class="btn btn-danger rounded-pill px-3 py-2 btn-sm">
                            <i class="fas fa-plus me-1"></i>Tambah Pengeluaran
                        </a>
                    </div>

                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">No</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3">Kategori</th>
                                    <th class="py-3">Keterangan / Keperluan</th>
                                    <th class="py-3 text-end">Jumlah (Rp)</th>
                                    <th class="py-3 text-center">Bukti Struk</th>
                                    <th class="pe-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pengeluaranList)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
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
                                        <td class="text-center">
                                            <?php if (!empty($p['file_path'])): ?>
                                                <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                                    <i class="fas fa-download me-1"></i>Bukti
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-danger rounded-pill" onclick="deletePengeluaran(<?= $p['id'] ?>, '<?= esc($p['keterangan']) ?>', 'Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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
                                                <div class="d-flex gap-2">
                                                    <?php if (!empty($p['file_path'])): ?>
                                                        <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="deletePengeluaran(<?= $p['id'] ?>, '<?= esc($p['keterangan']) ?>', 'Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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

<!-- Modal Edit Setoran -->
<div class="modal fade" id="modalEditSetoran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4 p-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-1"><i class="fas fa-edit text-warning me-2"></i>Edit Data Setoran Warga</h5>
                    <p class="text-white-50 small mb-0">Ubah detail setoran warga (program, periode, nominal, tanggal, status).</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditSetoranRekap" action="" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= base_url('admin/rekap') ?>">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Nama Warga</label>
                            <input type="text" id="editSetoranUserName" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                            <select name="program_id" id="editSetoranProgramId" class="form-select" required>
                                <option value="">-- Pilih Program --</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= $program['id'] ?>"><?= esc($program['nama_program']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <select name="periode_id" id="editSetoranPeriodeId" class="form-select" required>
                                <option value="">-- Pilih Periode --</option>
                                <?php foreach ($periodes as $periode): ?>
                                    <option value="<?= $periode['id'] ?>" data-program="<?= $periode['program_id'] ?>"><?= esc($periode['nama_periode']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Tanggal Setor <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_setoran" id="editSetoranTanggal" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Nominal Setoran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold bg-light">Rp</span>
                                <input type="number" step="1000" name="nominal" id="editSetoranNominal" class="form-control fw-bold text-success" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Status Setoran <span class="text-danger">*</span></label>
                            <select name="status_setoran" id="editSetoranStatus" class="form-select" required>
                                <option value="tercatat">Tercatat</option>
                                <option value="diverifikasi">Diverifikasi</option>
                                <option value="dikoreksi">Dikoreksi</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="keterangan" id="editSetoranKeterangan" class="form-control" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengeluaran -->
<div class="modal fade" id="modalEditPengeluaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white border-bottom-0 rounded-top-4 p-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-1"><i class="fas fa-edit text-warning me-2"></i>Edit Pengeluaran Kas</h5>
                    <p class="text-white-50 small mb-0">Ubah data transaksi pengeluaran kas atau perbarui bukti struk.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPengeluaranRekap" action="" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= base_url('admin/rekap') ?>">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="editPengeluaranTanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label fw-semibold">Kategori</label>
                            <input type="text" name="kategori" id="editPengeluaranKategori" class="form-control" placeholder="Contoh: Konsumsi, Kebersihan">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-light">Rp</span>
                            <input type="number" step="1000" name="jumlah" id="editPengeluaranJumlah" class="form-control fw-bold text-danger" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" id="editPengeluaranKeterangan" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perbarui File Bukti (Opsional)</label>
                        <input type="file" name="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah file bukti yang ada.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom style for tab toggle active button */
.nav-item-btn.active {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
.nav-item-btn#rekap-setoran-tab.active {
    background-color: var(--bs-primary) !important;
    color: white !important;
    border-color: var(--bs-primary) !important;
}
.nav-item-btn#rekap-setoran-tab.active i {
    color: white !important;
}
.nav-item-btn#rekap-pengeluaran-tab.active {
    background-color: var(--bs-danger) !important;
    color: white !important;
    border-color: var(--bs-danger) !important;
}
.nav-item-btn#rekap-pengeluaran-tab.active i {
    color: white !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function editSetoran(row) {
    document.getElementById('formEditSetoranRekap').action = '<?= base_url('admin/setoran') ?>/' + row.id + '/update';
    document.getElementById('editSetoranUserName').value = row.user_name || '';
    document.getElementById('editSetoranProgramId').value = row.program_id || '';
    document.getElementById('editSetoranPeriodeId').value = row.periode_id || '';
    document.getElementById('editSetoranTanggal').value = row.tanggal_setoran ? row.tanggal_setoran.substring(0, 10) : '';
    document.getElementById('editSetoranNominal').value = row.nominal || '';
    document.getElementById('editSetoranStatus').value = row.status_setoran || 'diverifikasi';
    document.getElementById('editSetoranKeterangan').value = row.keterangan || '';
    
    var modal = new bootstrap.Modal(document.getElementById('modalEditSetoran'));
    modal.show();
}

function deleteSetoran(id, name, nominal) {
    Swal.fire({
        title: 'Batalkan / Hapus Setoran?',
        text: 'Setoran sebesar ' + nominal + ' atas nama ' + name + ' akan dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan Setoran!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitPostForm('<?= base_url('admin/setoran') ?>/' + id + '/delete');
        }
    });
}

function editPengeluaran(p) {
    document.getElementById('formEditPengeluaranRekap').action = '<?= base_url('admin/pengeluaran') ?>/' + p.id + '/update';
    document.getElementById('editPengeluaranTanggal').value = p.tanggal || '';
    document.getElementById('editPengeluaranKategori').value = p.kategori || '';
    document.getElementById('editPengeluaranJumlah').value = p.jumlah || '';
    document.getElementById('editPengeluaranKeterangan').value = p.keterangan || '';
    
    var modal = new bootstrap.Modal(document.getElementById('modalEditPengeluaran'));
    modal.show();
}

function deletePengeluaran(id, ket, jumlah) {
    Swal.fire({
        title: 'Hapus Pengeluaran Kas?',
        text: 'Pengeluaran "' + ket + '" sejumlah ' + jumlah + ' akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus Data!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            submitPostForm('<?= base_url('admin/pengeluaran') ?>/' + id + '/delete');
        }
    });
}

function submitPostForm(url) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
<?= $this->endSection() ?>
