<?= $this->extend('auth/layout') ?>

<?= $this->section('content') ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-error">
            <?= session()->get('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if(session()->has('errors')): ?>
        <?php foreach(session()->get('errors') as $error): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <h2 class="form-title">Buat Akun Baru</h2>
    <p class="form-subtitle">Isi data diri Anda untuk membuat akun</p>
    
    <form action="<?= base_url('auth/process-register') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           class="form-control" 
                           placeholder="masukkan nama lengkap"
                           required
                           value="<?= old('nama') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="masukkan username"
                           required
                           value="<?= old('username') ?>">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   class="form-control" 
                   placeholder="masukkan alamat email"
                   required
                   value="<?= old('email') ?>">
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group password-toggle">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="buat password"
                           required>
                    <button type="button" class="password-toggle-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group password-toggle">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control" 
                           placeholder="ulangi password"
                           required>
                    <button type="button" class="password-toggle-btn">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="nomor_whatsapp">Nomor WhatsApp</label>
            <input type="tel" 
                   id="nomor_whatsapp" 
                   name="nomor_whatsapp" 
                   class="form-control" 
                   placeholder="masukkan nomor WhatsApp"
                   required
                   value="<?= old('nomor_whatsapp') ?>">
            <small class="text-muted">Digunakan untuk komunikasi terkait setoran iuran</small>
        </div>
        
        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" 
                       id="terms" 
                       name="terms" 
                       class="form-check-input" 
                       required>
                <label class="form-check-label" for="terms">
                    Saya menyetujui <a href="#" class="text-decoration-none">Syarat dan Ketentuan</a> serta <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn-primary">
            <i class="fas fa-user-plus me-2"></i>Daftar Akun
        </button>
    </form>
    
    <div class="auth-links">
        Sudah punya akun? <a href="<?= base_url('login') ?>">Masuk di sini</a>
    </div>
<?= $this->endSection() ?>