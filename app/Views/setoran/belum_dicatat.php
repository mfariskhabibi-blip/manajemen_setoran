<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Active Event Context Banner -->
    <div class="card border-0 shadow-sm rounded-4 text-white mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #f59e0b !important;">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-2">
                        <i class="fas fa-exclamation-circle me-1"></i>REKAP BELUM BAYAR / LUNAS
                    </span>
                    <h2 class="h3 fw-bold mb-1 text-white"><?= esc($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') ?></h2>
                    <p class="mb-0 text-light small" style="color: #cbd5e1 !important;">
                        Daftar warga yang masih memiliki sisa tagihan iuran acara.
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('admin/setoran') ?>" class="btn btn-outline-light rounded-pill px-4 py-2 w-100 w-sm-auto text-center">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Kelola Setoran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Warga Belum Lunas Card Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-users-slash text-danger me-2"></i>Daftar Warga Belum Lunas (<?= count($belumLunasList) ?> Orang)
            </h5>
        </div>
        <div class="card-body p-3 p-md-4 pt-0">
            <?php if (empty($belumLunasList)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3 opacity-75"></i>
                    <h4 class="fw-bold text-dark">Luar Biasa! Semua Warga Sudah Lunas</h4>
                    <p class="text-muted mb-0">Seluruh warga yang terdaftar telah menyelesaikan kewajiban iuran acara ini.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive border rounded-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Warga</th>
                                <th>Kewajiban</th>
                                <th>Sudah Dibayar</th>
                                <th>Sisa Tagihan</th>
                                <th>Pengingat WA</th>
                                <th class="text-end pe-4">Catat Setoran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($belumLunasList as $item): 
                                $u = $item['user'];
                                $cleanPhone = preg_replace('/[^0-9]/', '', $u['nomor_whatsapp'] ?? '');
                                if (substr($cleanPhone, 0, 1) === '0') {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                $waText = rawurlencode("Halo Bpk/Ibu " . $u['nama'] . ", mengingatkan untuk iuran acara " . ($activeEvent['nama_acara'] ?? 'Halalbihalal & Orkes 2026') . ". Sisa tagihan Anda sebesar Rp " . number_format($item['sisa_tagihan'], 0, ',', '.') . ". Pembayaran dapat diserahkan ke pengurus/bendahara. Terima kasih.");
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"><?= esc($u['nama']) ?></div>
                                    <small class="text-muted">@<?= esc($u['username']) ?></small>
                                </td>
                                <td>
                                    <span class="fw-semibold">Rp <?= number_format($item['kewajiban'], 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">Rp <?= number_format($item['sudah_setor'], 0, ',', '.') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-danger rounded-pill px-3 py-2 fs-6">
                                        Rp <?= number_format($item['sisa_tagihan'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($cleanPhone)): ?>
                                        <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waText ?>" target="_blank" class="btn btn-sm btn-success shadow-sm rounded-pill px-3 text-nowrap">
                                            <i class="fab fa-whatsapp me-1"></i> Kirim Pengingat WA
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No. WA Tdk Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 text-nowrap" onclick="quickPayModal(<?= $u['id'] ?>, '<?= esc($u['nama']) ?>', <?= $item['sisa_tagihan'] ?>)">
                                        <i class="fas fa-plus-circle me-1"></i> Catat Bayar
                                    </button>
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

<!-- Modal Quick Pay -->
<div class="modal fade" id="modalQuickPay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-cash-register me-2"></i>Catat Setoran - <span id="quickUserName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/setoran/store') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="quickUserId">
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 rounded-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Sisa tagihan acara saat ini: <strong id="quickSisaText" class="text-danger"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nominal Setoran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="1000" name="nominal" id="quickNominalInput" class="form-control fw-bold text-success" required>
                        </div>
                        <div class="form-text">Bisa dibayar lunas sekaligus atau dicicil.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Setor <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_setoran" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select">
                            <option value="Tunai / Cash">Tunai / Cash</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fas fa-save me-2"></i>Simpan Setoran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function quickPayModal(userId, userName, sisaTagihan) {
        document.getElementById('quickUserId').value = userId;
        document.getElementById('quickUserName').textContent = userName;
        document.getElementById('quickSisaText').textContent = 'Rp ' + sisaTagihan.toLocaleString('id-ID');
        document.getElementById('quickNominalInput').value = sisaTagihan;
        
        let modal = new bootstrap.Modal(document.getElementById('modalQuickPay'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
