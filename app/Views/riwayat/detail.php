<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i><?= esc($title) ?></h5>
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($acara): ?>
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold">Informasi Acara</h6>
                        <div class="p-3 bg-light rounded border border-light">
                            <p class="mb-1"><strong>Nama Acara:</strong> <?= esc($acara['nama_acara']) ?></p>
                            <p class="mb-1"><strong>Tanggal Pelaksanaan:</strong> <?= date('d F Y', strtotime($acara['tanggal_pelaksanaan'])) ?></p>
                            <p class="mb-0"><strong>Lokasi:</strong> <?= esc($acara['lokasi']) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small fw-bold">Rincian Setoran</h6>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%" class="bg-light">Tanggal Setoran</th>
                                    <td><?= date('d F Y H:i', strtotime($setoran['tanggal_setoran'])) ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Nominal Setoran</th>
                                    <td class="fs-5 fw-bold text-success">Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Status Setoran</th>
                                    <td>
                                        <?php 
                                        $badgeClass = match($setoran['status_setoran']) {
                                            'tercatat' => 'bg-info',
                                            'diverifikasi' => 'bg-success',
                                            'dikoreksi' => 'bg-warning',
                                            'dibatalkan' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?> px-3 py-2">
                                            <?= ucfirst($setoran['status_setoran']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Keterangan</th>
                                    <td><?= empty($setoran['keterangan']) ? '<em class="text-muted">Tidak ada keterangan</em>' : nl2br(esc($setoran['keterangan'])) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <a href="<?= base_url('riwayat/print/' . $setoran['id']) ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i> Cetak Kwitansi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
