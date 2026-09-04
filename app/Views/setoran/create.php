<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Tambah Setoran</h1>
            <p class="text-muted">Masukkan data setoran baru untuk pengguna.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= base_url('admin/setoran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Input Setoran</h6>
                </div>
                <div class="card-body">
                    <?php if ($activeEvent): ?>
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check fa-2x me-3"></i>
                            <div>
                                <strong class="d-block">Acara Aktif: <?= esc($activeEvent['nama_acara']) ?></strong>
                                <small class="text-muted">Tanggal: <?= date('d M Y', strtotime($activeEvent['tanggal_pelaksanaan'])) ?> | Tarif Default: Rp <?= number_format($activeEvent['tarif_default'], 0, ',', '.') ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form id="formCreateSetoran" action="<?= base_url('admin/setoran/store') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="acara_id" id="acara_id" value="<?= $activeEvent['id'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pengguna <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Pilih Pengguna --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>"><?= esc($user['nama']) ?> (<?= esc($user['email']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Pengguna harus dipilih</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Setoran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_setoran" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                <div class="invalid-feedback">Tanggal setoran harus diisi</div>
                            </div>
                        </div>

                        <div id="wargaBalanceCard" class="mb-3" style="display: none;">
                            <div class="card bg-light border-0">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Kewajiban Acara:</small>
                                            <div class="fw-bold" id="kewajibanDisplay">Rp 0</div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Sudah Setor:</small>
                                            <div class="fw-bold text-success" id="sudahSetorDisplay">Rp 0</div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Sisa Tagihan:</small>
                                            <div class="fw-bold text-danger" id="sisaTagihanDisplay">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nominal (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="nominal" id="nominal" class="form-control" placeholder="Contoh: 500000" min="1" required>
                                </div>
                                <div class="invalid-feedback">Nominal harus diisi dan lebih dari 0</div>
                                <button type="button" id="btnAutoFill" class="btn btn-sm btn-outline-primary mt-1" style="display: none;">
                                    <i class="fas fa-magic me-1"></i> Isi Sisa Tagihan
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Setoran bulan Agustus, transfer via BCA"><?= $activeEvent ? 'Setoran untuk acara ' . esc($activeEvent['nama_acara']) : '' ?></textarea>
                        </div>

                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="fas fa-save me-1"></i> Simpan Setoran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-secondary">Panduan Input</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 text-muted ps-3">
                        <li class="mb-2"><strong>Acara Aktif:</strong> Setoran akan otomatis terhubung ke acara aktif saat ini.</li>
                        <li class="mb-2"><strong>Alur Input:</strong> 1. Pilih Pengguna → 2. Lihat kewajiban → 3. Input nominal.</li>
                        <li class="mb-2">Setelah memilih pengguna, sistem akan menampilkan kewajiban dan riwayat setoran untuk acara aktif.</li>
                        <li class="mb-2">Gunakan tombol <b>Isi Sisa Tagihan</b> untuk mengisi nominal sesuai sisa kewajiban pengguna.</li>
                        <li class="mb-2">Status setoran otomatis diatur sebagai <b>Diverifikasi</b> (karena input manual oleh admin).</li>
                        <li><b>Keterangan</b> opsional tapi disarankan untuk catatan sumber dana atau info lain.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk memvalidasi pengguna yang dipilih
document.getElementById('user_id').addEventListener('change', function() {
    const userId = this.value;
    
    // Validasi akan dilakukan di server
    // Di sini hanya reset warning jika ada
    const userSelect = document.getElementById('user_id');
    if (userSelect.classList.contains('is-invalid')) {
        userSelect.classList.remove('is-invalid');
    }

    // Fetch warga balance if user is selected
    if (userId) {
        fetchWargaBalance(userId);
    } else {
        document.getElementById('wargaBalanceCard').style.display = 'none';
        document.getElementById('btnAutoFill').style.display = 'none';
    }
});

// Fetch warga balance via AJAX
function fetchWargaBalance(userId) {
    const acaraId = document.getElementById('acara_id').value;
    
    if (!acaraId) {
        document.getElementById('wargaBalanceCard').style.display = 'none';
        return;
    }

    fetch(`/admin/setoran/get-warga-balance/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('wargaBalanceCard').style.display = 'block';
                document.getElementById('kewajibanDisplay').textContent = 'Rp ' + data.kewajiban.toLocaleString('id-ID');
                document.getElementById('sudahSetorDisplay').textContent = 'Rp ' + data.sudah_setor.toLocaleString('id-ID');
                document.getElementById('sisaTagihanDisplay').textContent = 'Rp ' + data.sisa_tagihan.toLocaleString('id-ID');
                
                // Show auto-fill button if there's remaining balance
                const btnAutoFill = document.getElementById('btnAutoFill');
                if (data.sisa_tagihan > 0 && data.status_wajib === 'wajib') {
                    btnAutoFill.style.display = 'inline-block';
                    btnAutoFill.dataset.sisaTagihan = data.sisa_tagihan;
                } else {
                    btnAutoFill.style.display = 'none';
                }
            } else {
                document.getElementById('wargaBalanceCard').style.display = 'none';
                document.getElementById('btnAutoFill').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching warga balance:', error);
            document.getElementById('wargaBalanceCard').style.display = 'none';
            document.getElementById('btnAutoFill').style.display = 'none';
        });
}

// Auto-fill nominal with remaining balance
document.getElementById('btnAutoFill').addEventListener('click', function() {
    const sisaTagihan = parseFloat(this.dataset.sisaTagihan);
    if (sisaTagihan > 0) {
        document.getElementById('nominal').value = sisaTagihan;
    }
});
</script>
<?= $this->endSection() ?>
