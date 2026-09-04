<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Log Aktivitas Sistem</h1>
            <p class="text-muted small mb-0">Catatan riwayat transaksi, perubahan data, dan aktivitas yang terjadi di dalam sistem.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3">
            <form action="<?= base_url('admin/activity-log') ?>" method="get" class="row g-2 align-items-end">
                <div class="col-md-3 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Pengguna</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Pengguna</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= $filters['user_id'] == $u['id'] ? 'selected' : '' ?>><?= esc($u['nama']) ?> (<?= esc($u['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Kata Kunci Aktivitas</label>
                    <input type="text" name="search" class="form-control" value="<?= esc($filters['search']) ?>" placeholder="Cari deskripsi aktivitas...">
                </div>
                <div class="col-md-3 col-12">
                    <label class="form-label text-muted small fw-semibold mb-1">Rentang Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date']) ?>">
                        <span class="input-group-text">-</span>
                        <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                    </div>
                </div>
                <div class="col-md-2 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="<?= base_url('admin/activity-log') ?>" class="btn btn-light border"><i class="fas fa-sync"></i></a>
                </div>
            </form>
        </div>
        <div class="card-body p-3 p-md-4 pt-0">
            <!-- Desktop Table View -->
            <div class="d-none d-md-block table-responsive border shadow-sm rounded-4">
                <table class="table table-hover align-middle bg-white mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Waktu Log</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th class="text-end pe-4">Detail Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
                                    Belum ada catatan aktivitas sistem.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($logs as $log): ?>
                                <tr>
                                    <td class="ps-4 text-muted small"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= date('d M Y, H:i', strtotime($log['waktu'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($log['user_name'] ?? 'Sistem') ?></div>
                                        <small class="text-muted">Role: <?= esc($log['role'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark p-2 text-wrap text-start border fw-normal fs-6">
                                            <?= esc($log['aktivitas']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($log['data_sebelum'] || $log['data_sesudah']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick='showLogDetail(<?= json_encode($log['data_sebelum']) ?>, <?= json_encode($log['data_sesudah']) ?>)'>
                                                <i class="fas fa-eye me-1"></i> Data
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-block d-md-none">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-5 text-muted border rounded-4 bg-light shadow-sm">
                        <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
                        Belum ada catatan aktivitas sistem.
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($logs as $log): ?>
                            <div class="card border-0 rounded-4 shadow-sm bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2" style="border-color: #f1f5f9 !important;">
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= esc($log['user_name'] ?? 'Sistem') ?></div>
                                            <small class="text-muted"><i class="fas fa-user-tag text-secondary"></i> <?= esc($log['role'] ?? '-') ?></small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-semibold text-dark text-nowrap small"><i class="fas fa-clock fs-6"></i> <?= date('d M Y, H:i', strtotime($log['waktu'])) ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column gap-2 mb-2">
                                        <span class="text-secondary fw-normal fs-6">
                                            <?= esc($log['aktivitas']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-end pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                        <?php if ($log['data_sebelum'] || $log['data_sesudah']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-4 shadow-sm" onclick='showLogDetail(<?= json_encode($log['data_sebelum']) ?>, <?= json_encode($log['data_sesudah']) ?>)'>
                                                <i class="fas fa-eye me-1"></i> Data
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted fw-normal"><i class="fas fa-minus"></i></span>
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

<!-- Modal Detail Log -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-gradient bg-primary text-white rounded-top-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">Detail Perubahan Data</h5>
                        <small class="text-white-50" id="logDetailSubTitle">Perbandingan data sebelum dan sesudah perubahan</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-3 p-md-4 bg-light">
                <!-- Formatted View Container -->
                <div id="formattedViewContainer">
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-danger-subtle text-danger py-2 px-3 fw-bold rounded-top-4 border-0 d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-history me-2"></i>Data Sebelum (Original)</span>
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1" id="countSebelum">0 Data</span>
                                </div>
                                <div class="card-body p-0 table-responsive" style="max-height: 420px; overflow-y: auto;">
                                    <div id="formattedSebelum"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-success-subtle text-success py-2 px-3 fw-bold rounded-top-4 border-0 d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-check-circle me-2"></i>Data Sesudah (Terbaru)</span>
                                    <span class="badge bg-success text-white rounded-pill px-2 py-1" id="countSesudah">0 Data</span>
                                </div>
                                <div class="card-body p-0 table-responsive" style="max-height: 420px; overflow-y: auto;">
                                    <div id="formattedSesudah"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white rounded-bottom-4 py-2 border-top">
                <small class="text-muted me-auto"><i class="fas fa-info-circle text-primary me-1"></i> Field disorot label <span class="badge bg-warning text-dark"><i class="fas fa-pen me-1"></i>Diubah</span> menunjukkan data yang diperbarui.</small>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    const userMap = <?= json_encode($userMap ?? []) ?>;
    const programMap = <?= json_encode($programMap ?? []) ?>;
    const acaraMap = <?= json_encode($acaraMap ?? []) ?>;
    const periodeMap = <?= json_encode($periodeMap ?? []) ?>;

    const fieldDefinitions = {
        // Pengguna & Identitas
        'user_id': { label: 'Pengguna / Warga', map: userMap, icon: 'fa-user' },
        'created_by': { label: 'Dibuat Oleh', map: userMap, icon: 'fa-user-edit' },
        'updated_by': { label: 'Diubah Oleh', map: userMap, icon: 'fa-user-check' },
        'nama': { label: 'Nama Lengkap', icon: 'fa-id-card' },
        'username': { label: 'Username', icon: 'fa-at' },
        'role': { label: 'Peran / Role', icon: 'fa-user-tag', badge: true },
        'email': { label: 'Email', icon: 'fa-envelope' },
        'no_hp': { label: 'No. WhatsApp / HP', icon: 'fa-phone' },
        'alamat': { label: 'Alamat', icon: 'fa-map-marker-alt' },
        'status_user': { label: 'Status Akun', badge: true, icon: 'fa-user-clock' },

        // Setoran & Kewajiban
        'program_id': { label: 'Program Iuran', map: programMap, icon: 'fa-hand-holding-usd' },
        'acara_id': { label: 'Acara / Event', map: acaraMap, icon: 'fa-calendar-star' },
        'periode_id': { label: 'Periode Setoran', map: periodeMap, icon: 'fa-calendar-alt' },
        'tanggal_setoran': { label: 'Tanggal Setoran', type: 'date', icon: 'fa-calendar-day' },
        'tanggal': { label: 'Tanggal', type: 'date', icon: 'fa-calendar-day' },
        'nominal': { label: 'Nominal Setoran', type: 'currency', icon: 'fa-money-bill-wave' },
        'nominal_kewajiban': { label: 'Nominal Kewajiban', type: 'currency', icon: 'fa-file-invoice-dollar' },
        'status_setoran': { label: 'Status Setoran', badge: true, icon: 'fa-check-circle' },
        'keterangan': { label: 'Keterangan', icon: 'fa-comment-alt' },
        'status_wajib': { label: 'Kewajiban Event', badge: true, icon: 'fa-user-clock' },

        // Pengeluaran
        'kategori': { label: 'Kategori Pengeluaran', icon: 'fa-tag' },
        'jumlah': { label: 'Jumlah Nominal', type: 'currency', icon: 'fa-money-bill-alt' },
        'penerima': { label: 'Penerima / Penanggung Jawab', icon: 'fa-user-check' },
        'bukti_nota': { label: 'Bukti Nota', icon: 'fa-receipt' },

        // Program, Acara & Periode
        'kode_program': { label: 'Kode Program', icon: 'fa-barcode' },
        'nama_program': { label: 'Nama Program', icon: 'fa-folder' },
        'nama_acara': { label: 'Nama Acara', icon: 'fa-bullhorn' },
        'nama_periode': { label: 'Nama Periode', icon: 'fa-calendar-week' },
        'target_dana': { label: 'Target Dana', type: 'currency', icon: 'fa-bullseye' },
        'target_total': { label: 'Target Total', type: 'currency', icon: 'fa-chart-line' },
        'kewajiban_default': { label: 'Kewajiban Default', type: 'currency', icon: 'fa-coins' },
        'tarif_default': { label: 'Tarif Default', type: 'currency', icon: 'fa-tag' },
        'tanggal_pelaksanaan': { label: 'Tanggal Pelaksanaan', type: 'date', icon: 'fa-calendar-check' },
        'deadline_pembayaran': { label: 'Deadline Pembayaran', type: 'date', icon: 'fa-hourglass-end' },
        'tanggal_mulai': { label: 'Tanggal Mulai', type: 'date', icon: 'fa-calendar-plus' },
        'tanggal_selesai': { label: 'Tanggal Selesai', type: 'date', icon: 'fa-calendar-minus' },
        'lokasi': { label: 'Lokasi', icon: 'fa-map-marked-alt' },
        'deskripsi': { label: 'Deskripsi', icon: 'fa-align-left' },
        'tujuan': { label: 'Tujuan', icon: 'fa-crosshairs' },
        'status': { label: 'Status', badge: true, icon: 'fa-info-circle' },

        // Technical / Metadata
        'id': { label: 'ID Record', icon: 'fa-hashtag' },
        'created_at': { label: 'Waktu Dibuat', type: 'datetime', icon: 'fa-clock' },
        'updated_at': { label: 'Waktu Diperbarui', type: 'datetime', icon: 'fa-history' },
        'deleted_at': { label: 'Waktu Dihapus', type: 'datetime', icon: 'fa-trash' },
        'password': { label: 'Password', type: 'password', icon: 'fa-key' },
        'remember_token': { label: 'Token Akses', type: 'password', icon: 'fa-key' }
    };

    function formatFieldKey(key) {
        if (fieldDefinitions[key] && fieldDefinitions[key].label) {
            return fieldDefinitions[key].label;
        }
        // Humanize snake_case / camelCase
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatFieldValue(key, val) {
        if (val === null || val === undefined || val === '') {
            return '<span class="text-muted italic small">Tidak Ada Data</span>';
        }

        const def = fieldDefinitions[key] || {};

        // Password masking
        if (def.type === 'password' || key.toLowerCase().includes('password')) {
            return '<span class="font-monospace text-muted">••••••••</span>';
        }

        // Map foreign IDs
        if (def.map && def.map[val] !== undefined) {
            return `<span class="fw-semibold text-dark"><i class="fas ${def.icon || 'fa-info-circle'} text-primary me-1"></i>${escapeHtml(def.map[val])}</span>`;
        }

        // Currency formatting
        if (def.type === 'currency' || key.toLowerCase().includes('nominal') || key.toLowerCase().includes('jumlah') || key.toLowerCase().includes('target') || key.toLowerCase().includes('tarif')) {
            const num = parseFloat(val);
            if (!isNaN(num)) {
                return `<span class="fw-bold text-success">Rp ${num.toLocaleString('id-ID')}</span>`;
            }
        }

        // Date formatting
        if (def.type === 'date' && typeof val === 'string' && val.length >= 10) {
            const dateObj = new Date(val);
            if (!isNaN(dateObj)) {
                return `<span class="fw-semibold text-dark"><i class="far fa-calendar-alt text-info me-1"></i>${dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
            }
        }

        // Datetime formatting
        if (def.type === 'datetime' && typeof val === 'string' && val.length >= 10) {
            const dateObj = new Date(val);
            if (!isNaN(dateObj)) {
                return `<span class="small text-muted"><i class="far fa-clock me-1"></i>${dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} ${val.substring(11, 16)}</span>`;
            }
        }

        // Status & Role Badge
        if (def.badge || key.includes('status') || key === 'role') {
            const strVal = String(val).toLowerCase();
            let badgeClass = 'bg-secondary text-white';

            if (['diverifikasi', 'aktif', 'selesai', 'wajib', 'admin', 'success'].includes(strVal)) {
                badgeClass = 'bg-success-subtle text-success border border-success-subtle';
            } else if (['dibatalkan', 'nonaktif', 'tidak_wajib', 'error', 'failed'].includes(strVal)) {
                badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            } else if (['pending', 'menunggu', 'belum_aktif', 'warning'].includes(strVal)) {
                badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
            } else if (['user', 'warga'].includes(strVal)) {
                badgeClass = 'bg-info-subtle text-info border border-info-subtle';
            }

            let displayVal = escapeHtml(String(val));
            if (strVal === 'diverifikasi') displayVal = 'Diverifikasi';
            else if (strVal === 'dibatalkan') displayVal = 'Dibatalkan';
            else if (strVal === 'pending') displayVal = 'Menunggu Verifikasi';
            else if (strVal === 'admin') displayVal = 'Admin / Pengelola';
            else if (strVal === 'user') displayVal = 'Warga / Pengguna';
            else if (strVal === 'aktif') displayVal = 'Aktif';
            else if (strVal === 'nonaktif') displayVal = 'Non-Aktif';

            return `<span class="badge ${badgeClass} px-2 py-1 rounded-pill fw-semibold">${displayVal}</span>`;
        }

        return escapeHtml(String(val));
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderFormattedTable(dataObj, comparisonObj, isSebelum) {
        if (!dataObj || typeof dataObj !== 'object' || Object.keys(dataObj).length === 0) {
            return `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 opacity-50 d-block"></i>
                    <small>Tidak ada data recorded</small>
                </div>
            `;
        }

        let html = '<table class="table table-sm align-middle table-hover mb-0 font-sans" style="font-size: 0.875rem;"><tbody>';

        const keys = Object.keys(dataObj);
        keys.forEach(key => {
            const val = dataObj[key];
            const keyLabel = formatFieldKey(key);
            const formattedVal = formatFieldValue(key, val);

            let isChanged = false;
            if (comparisonObj && typeof comparisonObj === 'object' && comparisonObj.hasOwnProperty(key)) {
                if (String(comparisonObj[key]) !== String(val)) {
                    isChanged = true;
                }
            }

            const rowBg = isChanged ? 'style="background-color: #fefce8;"' : '';
            const changeBadge = isChanged ? '<span class="badge bg-warning text-dark rounded-pill ms-2" style="font-size: 0.7rem;"><i class="fas fa-pen me-1"></i>Diubah</span>' : '';
            const defIcon = (fieldDefinitions[key] && fieldDefinitions[key].icon) ? fieldDefinitions[key].icon : 'fa-th-list';

            html += `
                <tr ${rowBg}>
                    <td class="ps-3 py-2 text-muted fw-semibold text-nowrap" style="width: 42%; vertical-align: top;">
                        <i class="fas ${defIcon} me-2 text-secondary opacity-75" style="width: 16px;"></i>${escapeHtml(keyLabel)}
                    </td>
                    <td class="pe-3 py-2 text-dark" style="vertical-align: top;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <div>${formattedVal}</div>
                            ${changeBadge}
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        return html;
    }

    function showLogDetail(sebelumStr, sesudahStr) {
        let objSebelum = null;
        let objSesudah = null;

        // Parse JSON safely
        try {
            if (sebelumStr) {
                objSebelum = (typeof sebelumStr === 'object') ? sebelumStr : JSON.parse(sebelumStr);
            }
        } catch (e) {
            console.warn('Failed parsing data_sebelum:', e);
        }

        try {
            if (sesudahStr) {
                objSesudah = (typeof sesudahStr === 'object') ? sesudahStr : JSON.parse(sesudahStr);
            }
        } catch (e) {
            console.warn('Failed parsing data_sesudah:', e);
        }

        // Render Formatted Table Views
        const htmlSebelum = renderFormattedTable(objSebelum, objSesudah, true);
        const htmlSesudah = renderFormattedTable(objSesudah, objSebelum, false);

        document.getElementById('formattedSebelum').innerHTML = htmlSebelum;
        document.getElementById('formattedSesudah').innerHTML = htmlSesudah;

        // Set counters
        const countSebelum = objSebelum && typeof objSebelum === 'object' ? Object.keys(objSebelum).length : 0;
        const countSesudah = objSesudah && typeof objSesudah === 'object' ? Object.keys(objSesudah).length : 0;

        document.getElementById('countSebelum').textContent = `${countSebelum} Field`;
        document.getElementById('countSesudah').textContent = `${countSesudah} Field`;

        // Show Modal
        var modal = new bootstrap.Modal(document.getElementById('logDetailModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
