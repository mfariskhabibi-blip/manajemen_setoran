<?php

echo "=== Testing Sistem Manajemen Setoran Iuran ===\n\n";

// Cek PHP version
echo "1. Cek PHP Version: ";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "✓ " . PHP_VERSION . "\n";
} else {
    echo "✗ Versi PHP harus 7.4 atau lebih tinggi (Current: " . PHP_VERSION . ")\n";
}

// Cek extensions
echo "2. Cek Ekstensi PHP:\n";
$extensions = ['mysqli', 'json', 'mbstring', 'intl', 'curl'];
foreach ($extensions as $ext) {
    echo "   - $ext: " . (extension_loaded($ext) ? '✓' : '✗') . "\n";
}

// Cek folder permissions
echo "3. Cek Permission Folder:\n";
$folders = [
    'writable' => 'writable',
    'writable/cache' => 'writable/cache',
    'writable/logs' => 'writable/logs',
    'writable/session' => 'writable/session',
    'writable/uploads' => 'writable/uploads',
];

foreach ($folders as $folder) {
    if (is_dir($folder) && is_writable($folder)) {
        echo "   - $folder: ✓ Writeable\n";
    } elseif (!is_dir($folder)) {
        echo "   - $folder: ✗ Tidak ditemukan\n";
    } else {
        echo "   - $folder: ✗ Tidak writeable\n";
    }
}

// Cek file penting
echo "4. Cek File Penting:\n";
$files = [
    '.env' => 'Konfigurasi environment',
    'app/Config/Database.php' => 'Konfigurasi database',
    'app/Config/Auth.php' => 'Konfigurasi authentication',
    'vendor/autoload.php' => 'Composer autoload',
    'public/index.php' => 'Entry point aplikasi',
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "   - $desc: ✓ Ada\n";
    } else {
        echo "   - $desc: ✗ Tidak ditemukan\n";
    }
}

// Cek database connection
echo "5. Tes Koneksi Database:\n";
try {
    if (file_exists('.env')) {
        $env = file_get_contents('.env');
        if (strpos($env, 'setoran_db') !== false) {
            echo "   - Database config: ✓ Setoran_db ditemukan\n";
        } else {
            echo "   - Database config: ✗ Setoran_db tidak ditemukan\n";
        }
    } else {
        echo "   - Database config: ✗ File .env tidak ditemukan\n";
    }
} catch (Exception $e) {
    echo "   - Error: " . $e->getMessage() . "\n";
}

// Test routing
echo "6. Tes Routing:\n";
$routes = [
    '/login' => 'Halaman login',
    '/register' => 'Halaman register',
    '/dashboard' => 'Dashboard user (perlu login)',
    '/admin/dashboard' => 'Dashboard admin (perlu login)',
];

echo "   - Note: Test routing via browser setelah setup\n";

// Rekomendasi
echo "\n=== Rekomendasi ===\n";
echo "1. Jalankan setup.php untuk migrasi database\n";
echo "2. Akses http://localhost/manajemen_setoran/login\n";
echo "3. Login dengan:\n";
echo "   - Admin: admin / admin123\n";
echo "   - User: ahmad / password123\n";
echo "4. Test semua fitur sesuai role\n";

echo "\n=== Instruksi Setup Lengkap ===\n";
echo "1. Buat database 'setoran_db' di MySQL\n";
echo "2. Ubah config database di app/Config/Database.php jika perlu\n";
echo "3. Jalankan: php setup.php\n";
echo "4. Atau akses: http://localhost/manajemen_setoran/setup.php\n";
echo "5. Akses aplikasi: http://localhost/manajemen_setoran/\n";

echo "\n=== Quick Test ===\n";
echo "Untuk quick test, buka terminal dan jalankan:\n";
echo "php spark serve\n";
echo "Kemudian akses: http://localhost:8080\n";

// Check if we can run migrations
echo "\n=== Cek Migrasi ===\n";
if (file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        
        $config = new \Config\Database();
        if (!empty($config->default['database'])) {
            echo "Database config ditemukan.\n";
            
            // Try to connect
            try {
                $db = \Config\Database::connect();
                echo "Koneksi database: ✓ Berhasil\n";
                
                // Check if tables exist
                $tables = $db->listTables();
                $requiredTables = ['users', 'periode_setoran', 'setoran', 'chats', 'log_aktivitas'];
                $foundTables = [];
                
                foreach ($requiredTables as $table) {
                    if (in_array($table, $tables)) {
                        $foundTables[] = $table;
                    }
                }
                
                echo "Tabel ditemukan: " . count($foundTables) . "/" . count($requiredTables) . "\n";
                if (count($foundTables) < count($requiredTables)) {
                    echo "Jalankan migrasi dengan: php spark migrate\n";
                }
                
            } catch (Exception $e) {
                echo "Koneksi database: ✗ Gagal - " . $e->getMessage() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Error loading autoload: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SELESAI ===\n";