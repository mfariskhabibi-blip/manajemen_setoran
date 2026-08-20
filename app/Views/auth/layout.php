<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Manajemen Data Setoran Iuran Terpadu') ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --dark-bg: #1e293b;
            --text-primary: #334155;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 500px;
        }
        
        .auth-left {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .auth-right {
            padding: 40px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }
        
        .auth-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .auth-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .auth-features {
            list-style: none;
            margin-top: 30px;
        }
        
        .auth-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .auth-features li i {
            color: var(--secondary-color);
        }
        
        .form-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .form-subtitle {
            color: var(--text-secondary);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 20px;
            color: var(--text-secondary);
        }
        
        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            
            .auth-left, .auth-right {
                width: 100%;
            }
            
            .auth-left {
                padding: 30px;
            }
            
            .auth-title {
                font-size: 28px;
            }
        }
        
        .input-error {
            border-color: var(--danger-color);
        }
        
        .error-message {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="row g-0">
            <div class="col-md-5">
                <div class="auth-left">
                    <div class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="logo-text">
                            Sistem Setoran Iuran
                        </div>
                    </div>
                    
                    <h1 class="auth-title">Sistem Manajemen Data Setoran Iuran Terpadu</h1>
                    <p class="auth-subtitle">
                        Solusi digital terpadu untuk pencatatan, pemantauan, dan pengelolaan setoran iuran dengan sistem yang aman dan efisien.
                    </p>
                    
                    <ul class="auth-features">
                        <li><i class="fas fa-check-circle"></i> Dashboard real-time</li>
                        <li><i class="fas fa-check-circle"></i> Riwayat setoran lengkap</li>
                        <li><i class="fas fa-check-circle"></i> Chat terintegrasi</li>
                        <li><i class="fas fa-check-circle"></i> Laporan otomatis</li>
                        <li><i class="fas fa-check-circle"></i> Keamanan terjamin</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-7">
                <div class="auth-right">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle functionality
        document.querySelectorAll('.password-toggle-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
        
        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let firstError = null;
                
                // Clear previous error states
                document.querySelectorAll('.input-error').forEach(el => {
                    el.classList.remove('input-error');
                });
                document.querySelectorAll('.error-message').forEach(el => {
                    el.remove();
                });
                
                // Validate required fields
                this.querySelectorAll('[required]').forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('input-error');
                        
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Field ini wajib diisi';
                        field.parentNode.appendChild(errorMsg);
                        
                        if (!firstError) {
                            firstError = field;
                        }
                    }
                });
                
                // Validate email
                const emailField = this.querySelector('input[type="email"]');
                if (emailField && emailField.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailField.value)) {
                        isValid = false;
                        emailField.classList.add('input-error');
                        
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Format email tidak valid';
                        emailField.parentNode.appendChild(errorMsg);
                        
                        if (!firstError) {
                            firstError = emailField;
                        }
                    }
                }
                
                // Validate password confirmation
                const passwordField = this.querySelector('input[name="password"]');
                const confirmField = this.querySelector('input[name="password_confirmation"]');
                
                if (passwordField && confirmField && passwordField.value && confirmField.value) {
                    if (passwordField.value !== confirmField.value) {
                        isValid = false;
                        passwordField.classList.add('input-error');
                        confirmField.classList.add('input-error');
                        
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Password tidak cocok';
                        confirmField.parentNode.appendChild(errorMsg);
                        
                        if (!firstError) {
                            firstError = confirmField;
                        }
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });
        });
    </script>
</body>
</html>