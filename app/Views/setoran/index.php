<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-2">Setoran Iuran</h2>
                    <p class="text-muted mb-0">Daftar semua setoran iuran yang sudah dicatat oleh admin.</p>
                </div>
                <div class="text-end">
                    <div class="bg-light p-3 rounded">
                        <p class="text-muted mb-0">Total Setoran</p>
                        <h4 class="mb-0 text-primary">Rp <?= number_format($stats['total'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= base_url('setoran') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Periode</label>
                            <select name="periode" class="form-select">
                                <option value="">Semua Periode</option>
                                <?php foreach($periodes as $periode): ?>
                                <option value="<?= $periode['id'] ?>" <?= ($filters['periode'] == $periode['id']) ? 'selected' : '' ?>>
                                    <?= esc($periode['nama_periode']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="tercatat" <?= ($filters['status'] == 'tercatat') ? 'selected' : '' ?>>Tercatat</option>
                                <option value="diverifikasi" <?= ($filters['status'] == 'diverifikasi') ? 'selected' : '' ?>>Diverifikasi</option>
                                <option value="dikoreksi" <?= ($filters['status'] == 'dikoreksi') ? 'selected' : '' ?>>Dikoreksi</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Pencarian</label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari berdasarkan keterangan..."
                                   value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Setoran Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if(empty($setoran)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada setoran</h5>
                            <p class="text-muted mb-4">
                                <?php if($filters['periode'] || $filters['status'] || $filters['search']): ?>
                                    Tidak ada setoran yang sesuai dengan filter Anda.
                                <?php else: ?>
                                    Setoran Anda akan muncul di sini setelah dicatat oleh admin.
                                <?php endif; ?>
                            </p>
                            <?php if($filters['periode'] || $filters['status'] || $filters['search']): ?>
                                <a href="<?= base_url('setoran') ?>" class="btn btn-primary">
                                    <i class="fas fa-times me-1"></i>Hapus Filter
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal Setoran</th>
                                        <th>Periode</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = 1;
                                    foreach($setoran as $item): 
                                        $periodeModel = new \App\Models\PeriodeModel();
                                        $periode = $periodeModel->find($item['periode_id']);
                                    ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <div class="fw-bold"><?= date('d F Y', strtotime($item['tanggal_setoran'])) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <?= $periode ? esc($periode['nama_periode']) : '-' ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">
                                                Rp <?= number_format($item['nominal'], 0, ',', '.') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            $badgeClass = match($item['status_setoran']) {
                                                'tercatat' => 'badge-info',
                                                'diverifikasi' => 'badge-success',
                                                'dikoreksi' => 'badge-warning',
                                                'dibatalkan' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= ucfirst($item['status_setoran']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(!empty($item['keterangan'])): ?>
                                                <?= esc(substr($item['keterangan'], 0, 50)) ?>
                                                <?= strlen($item['keterangan']) > 50 ? '...' : '' ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal<?= $item['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if($pager->getPageCount() > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <?= $pager->links() ?>
                        </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
<?php foreach($setoran as $item): 
    $periodeModel = new \App\Models\PeriodeModel();
    $periode = $periodeModel->find($item['periode_id']);
?>
<div class="modal fade" id="detailModal<?= $item['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Setoran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Tanggal Setoran</label>
                            <p class="fw-bold"><?= date('d F Y', strtotime($item['tanggal_setoran'])) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Waktu Pencatatan</label>
                            <p class="fw-bold"><?= date('H:i:s', strtotime($item['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Periode</label>
                            <p class="fw-bold"><?= $periode ? esc($periode['nama_periode']) : '-' ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <?php 
                                $badgeClass = match($item['status_setoran']) {
                                    'tercatat' => 'badge-info',
                                    'diverifikasi' => 'badge-success',
                                    'dikoreksi' => 'badge-warning',
                                    'dibatalkan' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($item['status_setoran']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nominal Setoran</label>
                            <h3 class="text-primary fw-bold">
                                Rp <?= number_format($item['nominal'], 0, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                </div>
                
                <?php if(!empty($item['keterangan'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted">Keterangan</label>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0"><?= esc($item['keterangan']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <p class="mb-0">
                                        Setoran ini dicatat oleh sistem. Jika ada ketidaksesuaian, 
                                        silakan hubungi admin melalui fitur obrolan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<style>
.table th {
    font-weight: 600;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
}

.badge {
    padding: 5px 10px;
    font-weight: 500;
}

.pagination {
    margin-bottom: 0;
}

.page-link {
    border-color: var(--border-color);
    color: var(--primary-color);
}

.page-link:hover {
    background-color: var(--light-bg);
    border-color: var(--primary-color);
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-select, .form-control {
    border-color: var(--border-color);
}

.form-select:focus, .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}
</style>

<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Auto-hide modals after 10 seconds of inactivity
document.querySelectorAll('.modal').forEach(modal => {
    let timeout;
    
    modal.addEventListener('shown.bs.modal', function() {
        timeout = setTimeout(() => {
            bootstrap.Modal.getInstance(modal).hide();
        }, 10000);
    });
    
    modal.addEventListener('hide.bs.modal', function() {
        clearTimeout(timeout);
    });
    
    modal.addEventListener('mousemove', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            bootstrap.Modal.getInstance(modal).hide();
        }, 10000);
    });
});

// Export functionality
document.getElementById('exportBtn')?.addEventListener('click', function() {
    const format = this.dataset.format;
    
    fetch('/api/setoran/export', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            format: format,
            periode_id: document.querySelector('[name="periode"]')?.value || '',
            start_date: document.querySelector('[name="start_date"]')?.value || '',
            end_date: document.querySelector('[name="end_date"]')?.value || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (format === 'excel') {
                // Generate Excel download
                downloadExcel(data.data);
            } else if (format === 'pdf') {
                // Generate PDF download
                downloadPDF(data.data);
            }
            
            showAlert('success', 'Laporan berhasil diekspor.');
        } else {
            showAlert('error', data.message || 'Gagal mengekspor laporan.');
        }
    })
    .catch(error => {
        console.error('Export error:', error);
        showAlert('error', 'Terjadi kesalahan saat mengekspor.');
    });
});

function downloadExcel(data) {
    // Implement Excel download
    console.log('Exporting Excel:', data);
}

function downloadPDF(data) {
    // Implement PDF download
    console.log('Exporting PDF:', data);
}

function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
<?= $this->endSection() ?>