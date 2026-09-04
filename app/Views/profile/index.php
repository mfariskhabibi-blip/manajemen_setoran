<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Profil Saya</h1>
                <a href="/profile/edit" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profil
                </a>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="mb-3">
                                <?php if ($user['foto_profil']): ?>
                                    <img src="/uploads/profile/<?= esc($user['foto_profil']) ?>" 
                                         alt="Profile" 
                                         class="rounded-circle" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 150px; height: 150px; font-size: 60px;">
                                        <?= substr(esc($user['nama']), 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h4><?= esc($user['nama']) ?></h4>
                            <p class="text-muted mb-2">@<?= esc($user['username']) ?></p>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nama Lengkap</label>
                                <p class="fw-bold"><?= esc($user['nama']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Username</label>
                                <p class="fw-bold"><?= esc($user['username']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="fw-bold"><?= esc($user['email']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Nomor WhatsApp</label>
                                <p class="fw-bold"><?= esc($user['nomor_whatsapp']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Status Akun</label>
                                <p>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Terdaftar Sejak</label>
                                <p class="fw-bold"><?= date('d F Y', strtotime($user['created_at'])) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">Terakhir Login</label>
                                <p class="fw-bold"><?= $user['last_login'] ? date('d F Y H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Keamanan Akun</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Password</h6>
                                    <p class="text-muted mb-0">Ubah password akun Anda untuk keamanan</p>
                                </div>
                                <a href="/profile/change-password" class="btn btn-outline-primary">
                                    <i class="fas fa-key"></i> Ubah Password
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
