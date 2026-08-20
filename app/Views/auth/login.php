<?= $this->extend('auth/layout') ?>

<?= $this->section('content') ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-error">
            <?= session()->get('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if(session()->has('errors')): ?>
        <?php foreach(session()->get('errors') as $error): ?>
            <div class="alert alert-error">
                <?= $error ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <h2 class="form-title">Masuk ke Akun Anda</h2>
    <p class="form-subtitle">Silakan masukkan kredensial Anda untuk mengakses sistem</p>
    
    <form action="<?= base_url('auth/process-login') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label class="form-label" for="username">Username atau Email</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   class="form-control" 
                   placeholder="masukkan username atau email"
                   required
                   value="<?= old('username') ?>">
        </div>
        
        <div class="form-group password-toggle">
            <label class="form-label" for="password">Password</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="form-control" 
                   placeholder="masukkan password"
                   required>
            <button type="button" class="password-toggle-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        
        <div class="form-group d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" 
                       id="remember" 
                       name="remember" 
                       class="form-check-input">
                <label class="form-check-label" for="remember">
                    Ingat saya
                </label>
            </div>
            <a href="<?= base_url('forgot-password') ?>" class="text-decoration-none">
                Lupa password?
            </a>
        </div>
        
        <button type="submit" class="btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>
    </form>
    
    <div class="auth-links">
        Belum punya akun? <a href="<?= base_url('register') ?>">Daftar di sini</a>
    </div>
<?= $this->endSection() ?>