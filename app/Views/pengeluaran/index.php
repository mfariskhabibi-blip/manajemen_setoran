<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Flash Messages -->
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
    <!-- Header Title & Action -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Pengeluaran & Transparansi Kas 💸</h1>
            <p class="text-muted small mb-0">Catatan pengeluaran dana kas komunitas beserta bukti pendukung.</p>
        </div>
        <?php if ($isAdmin): ?>
            <div>
                <a href="<?= base_url('admin/pengeluaran/create') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Pengeluaran
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Summary KPI Cards (Setoran, Pengeluaran, Saldo Net) -->
    <div class="row g-3 mb-4">
        <!-- Total Setoran Terkumpul -->
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Setoran Terkumpul</small>
                        <h4 class="fw-bold mb-0 text-success text-truncate">Rp <?= number_format($totalSetoran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-danger bg-opacity-10 text-danger me-3 flex-shrink-0">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Total Pengeluaran Kas</small>
                        <h4 class="fw-bold mb-0 text-danger text-truncate">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Kas Terkini (Net Balance) -->
        <div class="col-md-4 col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-primary border-4">
                <div class="card-body p-3 p-md-4 d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted fw-semibold d-block text-truncate">Saldo Kas Terkini</small>
                        <h4 class="fw-bold mb-0 text-primary text-truncate">Rp <?= number_format($saldoKas, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data List Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Daftar Pengeluaran Dana
            </h5>
            <span class="badge bg-secondary rounded-pill px-3 py-2">Total <?= count($pengeluaranList) ?> Catatan</span>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Keterangan / Keperluan</th>
                            <th class="py-3 text-end">Jumlah Pengeluaran</th>
                            <th class="py-3 text-center">Bukti / Lampiran</th>
                            <?php if ($isAdmin): ?>
                                <th class="pe-4 py-3 text-center">Aksi Admin</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengeluaranList)): ?>
                            <tr>
                                <td colspan="<?= $isAdmin ? 7 : 6 ?>" class="text-center py-5 text-muted">
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
                                        <small class="text-muted">Input: <?= esc($p['admin_name'] ?? 'Admin') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                            <?= esc($p['kategori'] ?? 'Umum') ?>
                                        </span>
                                    </td>
                                    <td class="text-dark fw-medium">
                                        <?= esc($p['keterangan']) ?>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($p['file_path'])): ?>
                                            <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-download me-1"></i>Unduh Bukti
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fas fa-minus"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($isAdmin): ?>
                                        <td class="pe-4 text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/pengeluaran/' . $p['id'] . '/edit') ?>" class="btn btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['keterangan'], 'js') ?>', 'Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>')" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
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
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($p['tanggal'])) ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small">
                                            <?= esc($p['kategori'] ?? 'Umum') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-danger fs-5">
                                            - Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <?php if (!empty($p['file_path'])): ?>
                                                <a href="<?= base_url('pengeluaran/download/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1">
                                                    <i class="fas fa-download"></i> Bukti
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($isAdmin): ?>
                                                <a href="<?= base_url('admin/pengeluaran/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="confirmDelete(<?= $p['id'] ?>, '<?= esc($p['keterangan'], 'js') ?>', 'Rp <?= number_format($p['jumlah'], 0, ',', '.') ?>')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
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

<?php if ($isAdmin): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, keterangan, jumlah) {
    Swal.fire({
        title: 'Hapus Pengeluaran?',
        text: 'Data pengeluaran "' + keterangan + '" sejumlah ' + jumlah + ' akan dihapus permanen dan tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/pengeluaran') ?>/' + id + '/delete';

            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '<?= csrf_token() ?>';
            csrf.value = '<?= csrf_hash() ?>';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
<?php endif; ?>

<?= $this->endSection() ?>
