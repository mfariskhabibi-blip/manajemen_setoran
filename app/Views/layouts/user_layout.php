<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - Sistem Manajemen Setoran Iuran</title>
    
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
            --sidebar-width: 260px;
            --header-height: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            min-height: 100vh;
        }
        
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid var(--border-color);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            text-decoration: none;
            color: var(--text-primary);
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            display: block;
            padding: 12px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .menu-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .menu-item.active {
            background-color: rgba(30, 64, 175, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
            font-weight: 500;
        }
        
        .menu-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .menu-divider {
            margin: 15px 20px;
            border-top: 1px solid var(--border-color);
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .notification-badge {
            position: relative;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 20px;
        }
        
        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }
        
        .user-role {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        /* Content Area */
        .content-area {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        
        /* Footer */
        .footer {
            background: white;
            border-top: 1px solid var(--border-color);
            padding: 20px 30px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                padding: 0 15px;
            }
            
            .content-area {
                padding: 20px 15px;
            }
            
            .menu-toggle {
                display: block !important;
            }
        }
        
        /* Menu Toggle Button */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-primary);
            cursor: pointer;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stats-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 0;
        }
        
        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border-color: #bfdbfe;
        }
        
        /* Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        /* Progress Bar */
        .progress {
            height: 8px;
            background-color: var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            height: 100%;
            transition: width 0.3s ease;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 8px 20px;
            font-size: 14px;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }
        
        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 90;
        }
        
        .mobile-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?= base_url('dashboard') ?>" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="logo-text">
                        Sistem Setoran
                    </div>
                </a>
            </div>
            
            <div class="sidebar-menu">
                <a href="<?= base_url('dashboard') ?>" class="menu-item <?= current_url() == base_url('dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <a href="<?= base_url('setoran') ?>" class="menu-item <?= strpos(current_url(), base_url('setoran')) !== false ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i> Setoran Iuran
                </a>
                
                <a href="<?= base_url('riwayat') ?>" class="menu-item <?= strpos(current_url(), base_url('riwayat')) !== false ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Riwayat Setoran
                </a>
                
                <a href="<?= base_url('chat') ?>" class="menu-item <?= strpos(current_url(), base_url('chat')) !== false ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Obrolan
                    <span class="badge badge-danger ms-2" id="unread-count">0</span>
                </a>
                
                <div class="menu-divider"></div>
                
                <a href="<?= base_url('profile') ?>" class="menu-item <?= strpos(current_url(), base_url('profile')) !== false ? 'active' : '' ?>">
                    <i class="fas fa-user"></i> Profil
                </a>
                
                <a href="<?= base_url('logout') ?>" class="menu-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="d-flex align-items-center gap-3">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
                
                <div class="user-menu">
                    <div class="notification-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    
                    <div class="user-profile dropdown">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <?= substr($user['nama'], 0, 1) ?>
                            </div>
                            <div class="user-info">
                                <span class="user-name"><?= esc($user['nama']) ?></span>
                                <span class="user-role"><?= esc($user['role']) ?></span>
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('profile/edit') ?>"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="content-area">
                <?php if(session()->has('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?= session()->get('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session()->has('error')): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle me-2"></i><?= session()->get('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session()->has('warning')): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= session()->get('warning') ?>
                    </div>
                <?php endif; ?>
                
                <?php if(session()->has('info')): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i><?= session()->get('info') ?>
                    </div>
                <?php endif; ?>
                
                <?= $this->renderSection('content') ?>
            </main>
            
            <!-- Footer -->
            <footer class="footer">
                <p>&copy; <?= date('Y') ?> Sistem Manajemen Data Setoran Iuran Terpadu. Hak cipta dilindungi undang-undang.</p>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Menu toggle for mobile
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.querySelector('.sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.add('show');
            mobileOverlay.classList.add('show');
        });
        
        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
        
        // Close menu when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuToggle.contains(e.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('show');
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
        
        // Update unread message count (placeholder - implement with AJAX)
        function updateUnreadCount() {
            // This would normally fetch from API
            // fetch('/api/chat/unread-count')
            //     .then(response => response.json())
            //     .then(data => {
            //         document.getElementById('unread-count').textContent = data.count;
            //     });
        }
        
        // Update every 30 seconds
        setInterval(updateUnreadCount, 30000);
        updateUnreadCount();
        
        // Handle dropdown clicks
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>