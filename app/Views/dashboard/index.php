<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="mb-2">Selamat datang, <?= esc($user['nama']) ?>! 👋</h3>
                            <p class="text-muted mb-0">
                                <?php if(date('H') < 12): ?>
                                    Selamat pagi! Semoga hari Anda menyenangkan.
                                <?php elseif(date('H') < 18): ?>
                                    Selamat siang! Terus semangat mengumpulkan iuran.
                                <?php else: ?>
                                    Selamat malam! Waktunya istirahat setelah seharian beraktivitas.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-light text-dark p-2">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d F Y') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background-color: rgba(30, 64, 175, 0.1); color: var(--primary-color);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="stats-value">Rp <?= number_format($userStats['total'] ?? 0, 0, ',', '.') ?></h3>
                <p class="stats-label">Total Setoran</p>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background-color: rgba(16, 185, 129, 0.1); color: var(--secondary-color);">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="stats-value"><?= $userStats['count'] ?? 0 ?></h3>
                <p class="stats-label">Jumlah Setoran</p>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background-color: rgba(245, 158, 11, 0.1); color: var(--warning-color);">
                    <i class="fas fa-percentage"></i>
                </div>
                <h3 class="stats-value"><?= number_format($progress ?? 0, 1) ?>%</h3>
                <p class="stats-label">Progress Setoran</p>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <div class="stats-icon" style="background-color: rgba(239, 68, 68, 0.1); color: var(--danger-color);">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stats-value"><?= $periodeStats['in_progress'] ?? 0 ?></h3>
                <p class="stats-label">Periode Berjalan</p>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <?php if($activePeriode): ?>
    <div class="row mb-4">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Progress Setoran - <?= esc($activePeriode['nama_periode']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Progress</span>
                            <span class="fw-bold"><?= number_format($progress, 1) ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success p-2 rounded me-3">
                                    <i class="fas fa-arrow-up text-white"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Total Setoran</p>
                                    <h4 class="mb-0">Rp <?= number_format($userStats['total'] ?? 0, 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger p-2 rounded me-3">
                                    <i class="fas fa-arrow-down text-white"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-0">Sisa Kewajiban</p>
                                    <h4 class="mb-0">Rp <?= number_format($activePeriode['jumlah_kewajiban'] - ($userStats['total'] ?? 0), 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Detail Periode:</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tanggal Mulai</span>
                                <span class="fw-bold"><?= date('d F Y', strtotime($activePeriode['tanggal_mulai'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Tanggal Selesai</span>
                                <span class="fw-bold"><?= date('d F Y', strtotime($activePeriode['tanggal_selesai'])) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Target Setoran</span>
                                <span class="fw-bold">Rp <?= number_format($activePeriode['jumlah_kewajiban'], 0, ',', '.') ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Status Periode
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-4">
                            <div class="position-relative d-inline-block">
                                <canvas id="periodeChart" width="150" height="150"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <h3 class="mb-0"><?= $periodeStats['total'] ?? 0 ?></h3>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">&nbsp;</span>
                                    <span>Selesai</span>
                                </div>
                                <span class="fw-bold"><?= $periodeStats['completed'] ?? 0 ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">&nbsp;</span>
                                    <span>Berjalan</span>
                                </div>
                                <span class="fw-bold"><?= $periodeStats['in_progress'] ?? 0 ?></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary me-2">&nbsp;</span>
                                    <span>Belum Mulai</span>
                                </div>
                                <span class="fw-bold"><?= $periodeStats['not_started'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Setoran -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Setoran Terbaru
                    </h5>
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <?php if(empty($recentSetoran)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada setoran</h5>
                            <p class="text-muted">Setoran Anda akan muncul di sini setelah dicatat oleh admin.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Periode</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recentSetoran as $setoran): ?>
                                    <tr>
                                        <td><?= date('d F Y', strtotime($setoran['tanggal_setoran'])) ?></td>
                                        <td>
                                            <?php 
                                            $periodeModel = new \App\Models\PeriodeModel();
                                            $periode = $periodeModel->find($setoran['periode_id']);
                                            echo $periode ? esc($periode['nama_periode']) : '-';
                                            ?>
                                        </td>
                                        <td class="fw-bold">Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php 
                                            $badgeClass = match($setoran['status_setoran']) {
                                                'tercatat' => 'badge-info',
                                                'diverifikasi' => 'badge-success',
                                                'dikoreksi' => 'badge-warning',
                                                'dibatalkan' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($setoran['status_setoran']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?= $setoran['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal<?= $setoran['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Setoran</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Setoran</label>
                                                        <p class="fw-bold"><?= date('d F Y', strtotime($setoran['tanggal_setoran'])) ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Periode</label>
                                                        <p class="fw-bold"><?= $periode ? esc($periode['nama_periode']) : '-' ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Nominal</label>
                                                        <p class="fw-bold text-primary">Rp <?= number_format($setoran['nominal'], 0, ',', '.') ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <p>
                                                            <span class="badge <?= $badgeClass ?>">
                                                                <?= ucfirst($setoran['status_setoran']) ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <?php if(!empty($setoran['keterangan'])): ?>
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan</label>
                                                        <p><?= esc($setoran['keterangan']) ?></p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-comments fa-3x text-primary"></i>
                    </div>
                    <h5>Obrolan</h5>
                    <p class="text-muted">Hubungi admin atau pengguna lain melalui fitur chat.</p>
                    <a href="<?= base_url('chat') ?>" class="btn btn-primary">
                        <i class="fas fa-comment me-1"></i>Mulai Chat
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-edit fa-3x text-success"></i>
                    </div>
                    <h5>Profil</h5>
                    <p class="text-muted">Perbarui informasi profil dan pengaturan akun Anda.</p>
                    <a href="<?= base_url('profile/edit') ?>" class="btn btn-success">
                        <i class="fas fa-edit me-1"></i>Edit Profil
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice fa-3x text-warning"></i>
                    </div>
                    <h5>Riwayat Lengkap</h5>
                    <p class="text-muted">Lihat semua riwayat setoran Anda dari awal hingga sekarang.</p>
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-warning">
                        <i class="fas fa-list me-1"></i>Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Period Chart
const periodeCtx = document.getElementById('periodeChart').getContext('2d');
const periodeChart = new Chart(periodeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Selesai', 'Berjalan', 'Belum Mulai'],
        datasets: [{
            data: [
                <?= $periodeStats['completed'] ?? 0 ?>,
                <?= $periodeStats['in_progress'] ?? 0 ?>,
                <?= $periodeStats['not_started'] ?? 0 ?>
            ],
            backgroundColor: [
                '#10b981',
                '#f59e0b',
                '#6b7280'
            ],
            borderWidth: 0
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            }
        },
        responsive: true,
        maintainAspectRatio: true
    }
});

// Update stats every minute
function updateStats() {
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update stats cards
                document.querySelector('[data-stat="total-setoran"]').textContent = 
                    'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.total_setoran);
                document.querySelector('[data-stat="total-transactions"]').textContent = 
                    data.data.total_transactions;
                document.querySelector('[data-stat="progress-percentage"]').textContent = 
                    data.data.progress_percentage.toFixed(1) + '%';
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Update every minute
setInterval(updateStats, 60000);
</script>
<?= $this->endSection() ?>