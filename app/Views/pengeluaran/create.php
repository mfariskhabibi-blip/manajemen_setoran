<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Tambah Catatan Pengeluaran 💸</h1>
            <p class="text-muted small mb-0">Input data transaksi pengeluaran kas dan sertakan bukti pendukung.</p>
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
                        <i class="fas fa-pen-nib text-primary me-2"></i>Formulir Input Pengeluaran
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

                    <form action="<?= base_url('admin/pengeluaran/store') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label fw-bold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control rounded-3" value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="kategori" class="form-label fw-bold">Kategori Pengeluaran</label>
                                <input type="text" name="kategori" id="kategori" class="form-control rounded-3" placeholder="Contoh: Konsumsi, Perlengkapan, Kebersihan" value="<?= old('kategori', 'Umum') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label fw-bold">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" step="1000" name="jumlah" id="jumlah" class="form-control rounded-end-3" placeholder="Contoh: 150000" value="<?= old('jumlah') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold">Keterangan / Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control rounded-3" placeholder="Jelaskan detail pengeluaran dana..." required><?= old('keterangan') ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="file_bukti" class="form-label fw-bold">Upload File Bukti / Struk / Nota (Opsional)</label>
                            <input type="file" name="file_bukti" id="file_bukti" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                            <small class="text-muted d-block mt-1">Format didukung: PDF, JPG, PNG, WEBP, DOCX (Maksimal 10MB).</small>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/pengeluaran') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-save me-2"></i>Simpan Pengeluaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i>Catatan Penting</h6>
                </div>
                <div class="card-body p-4 text-muted small">
                    <ul class="ps-3 mb-0 d-flex flex-column gap-2">
                        <li>Pengeluaran yang Anda catat di sini akan **otomatis mengurangi saldo kas terkini** yang ditampilkan pada dashboard dan halaman transparansi.</li>
                        <li>Pastikan mengunggah **nota / kwitansi** untuk transparansi kepada seluruh warga.</li>
                        <li>Warga hanya memiliki akses untuk **melihat** daftar dan **mengunduh** bukti pengeluaran.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
