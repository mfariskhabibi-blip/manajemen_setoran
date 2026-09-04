<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Edit Catatan Pengeluaran ✏️</h1>
            <p class="text-muted small mb-0">Ubah data transaksi pengeluaran kas atau perbarui file bukti.</p>
        </div>
        <div>
            <a href="<?= base_url('admin/pengeluaran') ?>" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-edit text-warning me-2"></i>Formulir Edit Pengeluaran
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/pengeluaran/' . $pengeluaran['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label fw-bold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control rounded-3" value="<?= old('tanggal', $pengeluaran['tanggal']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label fw-bold">Kategori Pengeluaran</label>
                                <input type="text" name="kategori" id="kategori" class="form-control rounded-3" placeholder="Contoh: Konsumsi, Perlengkapan, Kebersihan" value="<?= old('kategori', $pengeluaran['kategori']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label fw-bold">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" step="1000" name="jumlah" id="jumlah" class="form-control rounded-end-3" placeholder="Contoh: 150000" value="<?= old('jumlah', $pengeluaran['jumlah']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold">Keterangan / Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control rounded-3" placeholder="Jelaskan detail pengeluaran dana..." required><?= old('keterangan', $pengeluaran['keterangan']) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_bukti" class="form-label fw-bold">Perbarui File Bukti (Opsional)</label>
                            <?php if (!empty($pengeluaran['file_path'])): ?>
                                <div class="mb-2">
                                    <span class="badge bg-info text-dark me-2"><i class="fas fa-file-alt"></i> File Terlampir saat ini</span>
                                    <a href="<?= base_url('pengeluaran/download/' . $pengeluaran['id']) ?>" class="btn btn-sm btn-link p-0">Unduh File Saat Ini</a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="file_bukti" id="file_bukti" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah file bukti.</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/pengeluaran') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">
                                <i class="fas fa-save me-2"></i>Perbarui Pengeluaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
