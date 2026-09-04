<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Program Iuran</h1>
                <div>
                    <a href="<?= base_url('admin/setoran') ?>" class="btn btn-outline-primary me-2">
                        <i class="fas fa-money-bill-wave"></i> Ke Setoran
                    </a>
                    <a href="/admin/program/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Program
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Program</h5>
                            <h2 class="card-text"><?= $stats['total'] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Program Aktif</h5>
                            <h2 class="card-text"><?= $stats['aktif'] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Program Nonaktif</h5>
                            <h2 class="card-text"><?= $stats['nonaktif'] ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programs Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Program Iuran</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($programs)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada program iuran yang dibuat.</p>
                            <a href="/admin/program/create" class="btn btn-primary">Buat Program Pertama</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Program</th>
                                        <th>Tujuan</th>
                                        <th>Target Dana</th>
                                        <th>Kewajiban Default</th>
                                        <th>Peserta</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($programs as $program): ?>
                                    <tr>
                                        <td><strong><?= esc($program['kode_program']) ?></strong></td>
                                        <td><?= esc($program['nama_program']) ?></td>
                                        <td><?= esc(substr($program['tujuan'], 0, 50)) ?>...</td>
                                        <td>Rp <?= number_format($program['target_dana'], 0, ',', '.') ?></td>
                                        <td>Rp <?= number_format($program['kewajiban_default'], 0, ',', '.') ?></td>
                                        <td><?= $program['jumlah_peserta'] ?? 0 ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?= $program['progress'] ?? 0 ?>%"
                                                     aria-valuenow="<?= $program['progress'] ?? 0 ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?= round($program['progress'] ?? 0, 1) ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($program['status'] === 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/admin/program/<?= $program['id'] ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/program/<?= $program['id'] ?>/edit" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $program['id'] ?>, '<?= esc($program['nama_program']) ?>')" 
                                                        class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
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
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm('Apakah Anda yakin ingin menghapus program "' + name + '"?')) {
        fetch('/admin/program/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '_method=DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus program');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan');
        });
    }
}
</script>
<?= $this->endSection() ?>
