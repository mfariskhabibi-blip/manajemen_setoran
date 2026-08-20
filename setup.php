<?php

require_once 'vendor/autoload.php';

use CodeIgniter\CLI\CLI;

echo "=== Setup Sistem Manajemen Setoran Iuran ===\n\n";

// Cek environment
if (!file_exists('.env')) {
    echo "File .env tidak ditemukan. Menggunakan env default...\n";
    
    $envContent = <<<ENV
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/manajemen_setoran/'
database.default.hostname = localhost
database.default.database = setoran_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
ENV;
    
    file_put_contents('.env', $envContent);
    echo "File .env berhasil dibuat.\n";
}

// Jalankan migrasi
echo "\nMenjalankan migrasi database...\n";

$migration = \Config\Services::migrations();
$seeder = \Config\Database::seeder();

try {
    // Run migrations
    $migration->latest();
    echo "Migrasi database berhasil.\n";
    
    // Run seeder
    echo "Menjalankan seeder data awal...\n";
    $seeder->call('InitialDataSeeder');
    echo "Seeder data awal berhasil.\n";
    
    echo "\n=== Setup Selesai ===\n";
    echo "Sistem siap digunakan.\n";
    echo "Login dengan:\n";
    echo "- Admin: username: admin, password: admin123\n";
    echo "- User: username: ahmad, password: password123\n";
    echo "\nAkses aplikasi di: http://localhost/manajemen_setoran/\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Pastikan database 'setoran_db' sudah dibuat dan koneksi database benar.\n";
    
    // Create database if not exists
    echo "\nMembuat database...\n";
    try {
        $db = \Config\Database::connect(['database' => '']);
        $db->query("CREATE DATABASE IF NOT EXISTS setoran_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "Database 'setoran_db' berhasil dibuat.\n";
        
        // Coba migrasi lagi
        $migration->latest();
        $seeder->call('InitialDataSeeder');
        echo "Setup berhasil diselesaikan.\n";
        
    } catch (Exception $e2) {
        echo "Gagal membuat database: " . $e2->getMessage() . "\n";
        echo "Silakan buat database 'setoran_db' secara manual dan jalankan setup lagi.\n";
    }
}