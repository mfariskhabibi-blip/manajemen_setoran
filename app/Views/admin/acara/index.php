<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Pengaturan Acara</h1>
            <p class="text-muted small mb-0">Kelola informasi acara, skema target iuran, dan penentuan daftar warga yang wajib iuran.</p>
        </div>
        <div class="w-100 w-sm-auto text-end">
            <a href="<?= base_url('admin/acara/sync-warga/' . $event['id']) ?>" class="btn btn-outline-primary rounded-pill px-3 py-2 w-100 w-sm-auto text-center">
                <i class="fas fa-sync me-2"></i>Sinkronisasi Daftar Warga
            </a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-primary bg-opacity-10 text-primary me-3 flex-shrink-0">
                        <i class="fas fa-bullseye fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Target Total Dana</div>
                        <div class="h5 mb-0 fw-bold text-primary text-truncate">Rp <?= number_format($event['target_total'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-info bg-opacity-10 text-info me-3 flex-shrink-0">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Potensi Iuran Wajib</div>
                        <div class="h5 mb-0 fw-bold text-info text-truncate">Rp <?= number_format($summary['potensi_total'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-success bg-opacity-10 text-success me-3 flex-shrink-0">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Warga Wajib Iuran</div>
                        <div class="h5 mb-0 fw-bold text-success text-truncate"><?= number_format($summary['total_wajib']) ?> / <?= number_format($summary['total_warga']) ?> Warga</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-4 p-3 bg-warning bg-opacity-10 text-warning me-3 flex-shrink-0">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-truncate">Deadline Pembayaran</div>
                        <div class="h5 mb-0 fw-bold text-dark text-truncate"><?= $event['deadline_pembayaran'] ? date('d M Y', strtotime($event['deadline_pembayaran'])) : '-' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs with Horizontal Scroll for Mobile -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white pt-3 pb-md-0 border-bottom-0">
            <div class="pb-1" style="-webkit-overflow-scrolling: touch;">
                <ul class="nav nav-tabs card-header-tabs flex-column flex-md-row border-bottom-0 gap-2 gap-md-0" id="eventTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link text-start text-md-center border-0 border-md <?= $activeTab === 'info' ? 'active fw-bold bg-light bg-md-white' : 'text-dark' ?>" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button">
                            <i class="fas fa-info-circle me-2 text-primary"></i>1. Informasi Acara
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link text-start text-md-center border-0 border-md <?= $activeTab === 'skema' ? 'active fw-bold bg-light bg-md-white' : 'text-dark' ?>" id="skema-tab" data-bs-toggle="tab" data-bs-target="#skema-pane" type="button">
                            <i class="fas fa-layer-group me-2 text-warning"></i>2. Target & Skema Iuran
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link text-start text-md-center border-0 border-md <?= $activeTab === 'warga' ? 'active fw-bold bg-light bg-md-white' : 'text-dark' ?>" id="warga-tab" data-bs-toggle="tab" data-bs-target="#warga-pane" type="button">
                            <i class="fas fa-users-cog me-2 text-success"></i>3. Data Anggota / Warga
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="tab-content" id="eventTabContent">
                <!-- TAB 1: INFORMASI ACARA -->
                <div class="tab-pane fade <?= $activeTab === 'info' ? 'show active' : '' ?>" id="info-pane">
                    <h5 class="text-primary mb-3"><i class="fas fa-edit me-2"></i>Kelola Informasi Acara</h5>
                    <form action="<?= base_url('admin/acara/update-info') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                        <div class="row g-3">
                            <div class="col-md-8 col-12">
                                <label class="form-label fw-semibold">Nama Acara <span class="text-danger">*</span></label>
                                <input type="text" name="nama_acara" class="form-control form-control-lg" value="<?= old('nama_acara', $event['nama_acara']) ?>" required placeholder="Contoh: Halalbihalal & Orkes 2026">
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label fw-semibold">Status Acara <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-lg">
                                    <option value="perencanaan" <?= old('status', $event['status']) === 'perencanaan' ? 'selected' : '' ?>>Perencanaan</option>
                                    <option value="aktif" <?= old('status', $event['status']) === 'aktif' ? 'selected' : '' ?>>Aktif (Berjalan)</option>
                                    <option value="selesai" <?= old('status', $event['status']) === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="nonaktif" <?= old('status', $event['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pelaksanaan" class="form-control" value="<?= old('tanggal_pelaksanaan', $event['tanggal_pelaksanaan']) ?>" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Lokasi Pelaksanaan <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" class="form-control" value="<?= old('lokasi', $event['lokasi']) ?>" required placeholder="Contoh: Lapangan Utama RW 05">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Deskripsi / Catatan Acara</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan mengenai agenda acara, ketua panitia, atau perincian kebutuhan..."><?= old('deskripsi', $event['deskripsi']) ?></textarea>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill w-100 w-sm-auto"><i class="fas fa-save me-2"></i>Simpan Informasi Acara</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: TARGET & SKEMA IURAN -->
                <div class="tab-pane fade <?= $activeTab === 'skema' ? 'show active' : '' ?>" id="skema-pane">
                    <h5 class="text-primary mb-3"><i class="fas fa-calculator me-2"></i>Pengaturan Target Total & Skema Iuran</h5>
                    <form action="<?= base_url('admin/acara/update-skema') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Nominal Target Total Dana (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="1000" name="target_total" class="form-control fw-bold text-primary" value="<?= old('target_total', $event['target_total']) ?>" required>
                                </div>
                                <small class="text-muted">Target keseluruhan anggaran yang dibutuhkan untuk acara.</small>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Batas Waktu (Deadline) Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="deadline_pembayaran" class="form-control form-control-lg" value="<?= old('deadline_pembayaran', $event['deadline_pembayaran']) ?>" required>
                                <small class="text-muted">Tanggal terakhir penerimaan iuran dari warga.</small>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Penentuan Skema Tarif Iuran <span class="text-danger">*</span></label>
                                <select name="skema_tarif" class="form-select form-select-lg">
                                    <option value="flat" <?= old('skema_tarif', $event['skema_tarif']) === 'flat' ? 'selected' : '' ?>>Flat (Satu Tarif Sama untuk Semua Warga)</option>
                                    <option value="tiered" <?= old('skema_tarif', $event['skema_tarif']) === 'tiered' ? 'selected' : '' ?>>Tiered / Bertingkat (Berdasarkan Kategori Warga)</option>
                                </select>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold">Nominal Tarif Default Per Warga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="1000" name="tarif_default" class="form-control fw-bold" value="<?= old('tarif_default', $event['tarif_default']) ?>" required id="tarifDefaultInput">
                                </div>
                                <small class="text-muted">Tarif standar yang dibebankan kepada setiap warga reguler.</small>
                                <div class="alert alert-info mt-2 p-2 small" id="tarifDefaultInfo">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="tarifInfoText">
                                        Tarif ini akan diterapkan untuk warga baru yang ditambahkan ke acara ini.
                                        Untuk menerapkan ke warga yang sudah ada, centang opsi "Terapkan tarif default ini secara masal" di bawah.
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="apply_all_warga" value="1" id="applyAllWarga">
                                    <label class="form-check-label fw-bold" for="applyAllWarga">
                                        Terapkan tarif default ini secara masal ke seluruh warga yang wajib iuran
                                    </label>
                                    <div class="text-muted small ms-0 ms-md-5 mt-1">Centang opsi ini jika Anda ingin mengganti semua nominal kewajiban warga reguler yang ada dengan tarif default baru di atas.</div>
                                    <div id="wargaCountInfo" class="text-info small mt-2 d-none">
                                        <i class="fas fa-info-circle"></i> Akan diterapkan ke <span id="regularWargaCount">0</span> warga reguler.
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill w-100 w-sm-auto"><i class="fas fa-save me-2"></i>Simpan Skema Iuran</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: DATA ANGGOTA / WARGA -->
                <div class="tab-pane fade <?= $activeTab === 'warga' ? 'show active' : '' ?>" id="warga-pane">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="text-primary mb-0"><i class="fas fa-users me-2"></i>Pengaturan Daftar Warga Wajib Iuran</h5>
                    </div>

                    <!-- Search & Filters -->
                    <form action="<?= base_url('admin/acara') ?>" method="get" class="row g-2 mb-4">
                        <input type="hidden" name="tab" value="warga">
                        <div class="col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari nama warga, username, no WA..." value="<?= esc($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-8">
                            <select name="status_wajib" class="form-select">
                                <option value="">Semua Status Wajib</option>
                                <option value="wajib" <?= $statusFilter === 'wajib' ? 'selected' : '' ?>>Wajib Iuran</option>
                                <option value="bebas" <?= $statusFilter === 'bebas' ? 'selected' : '' ?>>Bebas Iuran</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-4">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="d-none d-md-block table-responsive border shadow-sm rounded-4">
                        <table class="table table-hover align-middle bg-white mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Warga</th>
                                    <th>WhatsApp</th>
                                    <th>Kategori Warga</th>
                                    <th>Nominal Kewajiban</th>
                                    <th>Status Wajib</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($wargaList)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3 d-block opacity-50"></i>
                                            Tidak ada data warga ditemukan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($wargaList as $w): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 180px;"><?= esc($w['nama']) ?></div>
                                                <small class="text-muted">@<?= esc($w['username']) ?></small>
                                            </td>
                                            <td>
                                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $w['nomor_whatsapp']) ?>" target="_blank" class="text-success text-decoration-none small">
                                                    <i class="fab fa-whatsapp me-1"></i><?= esc($w['nomor_whatsapp']) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm input-kategori" data-userid="<?= $w['user_id'] ?>" value="<?= esc($w['kategori_warga'] ?? 'Warga Reguler') ?>" style="min-width: 120px;">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="min-width: 140px;">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" step="1000" class="form-control form-control-sm input-nominal" data-userid="<?= $w['user_id'] ?>" value="<?= (float)($w['nominal_kewajiban'] ?? $event['tarif_default']) ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <?php $isWajib = ($w['status_wajib'] ?? 'wajib') === 'wajib'; ?>
                                                <button type="button" class="btn btn-sm btn-<?= $isWajib ? 'success' : 'secondary' ?> btn-toggle-wajib text-nowrap rounded-pill px-3" data-userid="<?= $w['user_id'] ?>">
                                                    <i class="fas fa-<?= $isWajib ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                                    <span class="label-status"><?= $isWajib ? 'Wajib Iuran' : 'Bebas Iuran' ?></span>
                                                </button>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-save-tarif rounded-pill px-3" data-userid="<?= $w['user_id'] ?>" title="Simpan Tarif & Kategori">
                                                    <i class="fas fa-save me-1"></i> Simpan
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-block d-md-none mt-3">
                        <?php if (empty($wargaList)): ?>
                            <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                                <i class="fas fa-user-slash fa-3x mb-3 d-block opacity-50"></i>
                                Tidak ada data warga ditemukan.
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($wargaList as $w): ?>
                                    <div class="card border-0 rounded-4 shadow-sm bg-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-3" style="border-color: #f1f5f9 !important;">
                                                <div>
                                                    <div class="fw-bold text-dark fs-6"><?= esc($w['nama']) ?></div>
                                                    <small class="text-muted">@<?= esc($w['username']) ?></small>
                                                </div>
                                                <div>
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $w['nomor_whatsapp']) ?>" target="_blank" class="text-success text-decoration-none small fw-bold bg-success bg-opacity-10 py-1 px-2 rounded-pill shadow-sm">
                                                        <i class="fab fa-whatsapp"></i> WA
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="small text-muted mb-1">Kategori Warga</label>
                                                <input type="text" class="form-control input-kategori" data-userid="<?= $w['user_id'] ?>" value="<?= esc($w['kategori_warga'] ?? 'Warga Reguler') ?>">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="small text-muted mb-1">Nominal Kewajiban</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" step="1000" class="form-control text-success fw-bold input-nominal" data-userid="<?= $w['user_id'] ?>" value="<?= (float)($w['nominal_kewajiban'] ?? $event['tarif_default']) ?>">
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex flex-row justify-content-between align-items-center pt-3 border-top" style="border-color: #f1f5f9 !important;">
                                                <?php $isWajib = ($w['status_wajib'] ?? 'wajib') === 'wajib'; ?>
                                                <button type="button" class="btn btn-sm btn-<?= $isWajib ? 'success' : 'secondary' ?> btn-toggle-wajib rounded-pill px-3 py-2 shadow-sm" data-userid="<?= $w['user_id'] ?>">
                                                    <i class="fas fa-<?= $isWajib ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                                    <span class="label-status"><?= $isWajib ? 'Wajib' : 'Bebas' ?></span>
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-primary btn-save-tarif rounded-pill px-4 py-2 shadow-sm fw-bold" data-userid="<?= $w['user_id'] ?>" title="Simpan Tarif & Kategori">
                                                    <i class="fas fa-save me-1"></i> Simpan
                                                </button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function getCsrfToken() {
        let match = document.cookie.match(new RegExp('(^| )csrf_cookie_name=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : '<?= csrf_hash() ?>';
    }

    // Toggle Wajib / Bebas
    document.querySelectorAll('.btn-toggle-wajib').forEach(btn => {
        btn.addEventListener('click', function() {
            let userId = this.dataset.userid;
            let button = this;

            fetch('<?= base_url('admin/acara/toggle-wajib') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: `event_id=<?= $event['id'] ?>&user_id=${userId}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    let isWajib = data.new_status === 'wajib';
                    button.className = `btn btn-sm btn-${isWajib ? 'success' : 'secondary'} btn-toggle-wajib text-nowrap rounded-pill px-3`;
                    button.querySelector('.label-status').textContent = isWajib ? 'Wajib Iuran' : 'Bebas Iuran';
                    button.querySelector('i').className = `fas fa-${isWajib ? 'check-circle' : 'times-circle'} me-1`;
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            });
        });
    });

    // Save individual Tariff & Category
    document.querySelectorAll('.btn-save-tarif').forEach(btn => {
        btn.addEventListener('click', function() {
            let userId = this.dataset.userid;
            let container = this.closest('tr, .card-body');
            let kategori = container.querySelector('.input-kategori').value;
            let nominal = container.querySelector('.input-nominal').value;

            fetch('<?= base_url('admin/acara/update-warga-tarif') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: `event_id=<?= $event['id'] ?>&user_id=${userId}&kategori_warga=${encodeURIComponent(kategori)}&nominal_kewajiban=${encodeURIComponent(nominal)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            });
        });
    });

    // Update warga count info when checkbox is clicked
    document.getElementById('applyAllWarga')?.addEventListener('change', function() {
        const infoDiv = document.getElementById('wargaCountInfo');
        if (this.checked) {
            infoDiv.classList.remove('d-none');
            
            // Get count of regular residents via AJAX
            fetch('<?= base_url('admin/acara/get-regular-count/' . $event['id']) ?>')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('regularWargaCount').textContent = data.count || 0;
                })
                .catch(() => {
                    document.getElementById('regularWargaCount').textContent = '?';
                });
        } else {
            infoDiv.classList.add('d-none');
        }
    });

    // Also update when tariff default input changes (for user feedback)
    const tarifDefaultInput = document.getElementById('tarifDefaultInput');
    if (tarifDefaultInput) {
        tarifDefaultInput.addEventListener('input', function() {
            const applyAllCheckbox = document.getElementById('applyAllWarga');
            const currentValue = parseFloat(this.value) || 0;
            const currentWargaCount = parseInt(document.getElementById('regularWargaCount')?.textContent) || 0;
            const totalPotensi = new Intl.NumberFormat('id-ID').format(currentValue * currentWargaCount);
            
            if (applyAllCheckbox && applyAllCheckbox.checked) {
                // Update info text with total potential
                document.getElementById('wargaCountInfo').innerHTML = 
                    `<i class="fas fa-info-circle"></i> Akan diterapkan ke <span id="regularWargaCount">${currentWargaCount}</span> warga reguler. Potensi total: <strong>Rp ${totalPotensi}</strong>`;
            }
            
            // Update main info text
            document.getElementById('tarifInfoText').textContent = 
                `Tarif ini akan diterapkan untuk warga baru yang ditambahkan ke acara ini. ` +
                `Untuk menerapkan ke ${currentWargaCount} warga yang sudah ada (potensi total Rp ${totalPotensi}), centang opsi "Terapkan tarif default ini secara masal" di bawah.`;
        });
        
        // Initial update
        tarifDefaultInput.dispatchEvent(new Event('input'));
    }
</script>
<?= $this->endSection() ?>
