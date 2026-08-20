# Sistem Manajemen Data Setoran Iuran Terpadu

Sistem profesional untuk mencatat, memantau, mengelola, dan merekap data setoran iuran secara terpusat.

## 🎯 Fitur Utama

### Role & Hak Akses
- **User**: Dashboard, Lihat Setoran, Riwayat Setoran, Obrolan, Profil
- **Admin**: Dashboard Admin, Kelola Setoran, Data Pengguna, Periode Setoran, Rekap, Obrolan, Log Aktivitas, Pengaturan

### Menu User
1. **Dashboard User**
   - Statistik utama (total setoran, sisa kewajiban, progress)
   - Setoran terbaru
   - Informasi periode aktif

2. **Setoran Iuran**
   - Read-only data setoran
   - Search, filter, pagination
   - Detail setoran lengkap

3. **Riwayat Setoran**
   - Histori setoran lengkap
   - Filter tanggal, periode, status
   - Export Excel/PDF

4. **Obrolan User**
   - Chat modern dengan pengguna lain
   - Status online/offline
   - Notifikasi pesan baru

5. **Profil User**
   - Edit profil
   - Ubah password
   - Pengaturan akun

### Menu Admin
1. **Dashboard Admin**
   - Statistik lengkap (total pengguna, setoran, grafik)
   - Aktivitas terbaru
   - Monitoring real-time

2. **Kelola Setoran**
   - CRUD setoran
   - Validasi otomatis
   - Pencegahan duplikasi

3. **Data Pengguna**
   - Manajemen pengguna
   - Detail pengguna lengkap
   - Aktivasi/nonaktifkan

4. **Periode Setoran**
   - Buat/edit periode
   - Status periode (belum mulai, berjalan, selesai)

5. **Rekap Setoran**
   - Laporan bulanan
   - Rekap per pengguna
   - Rekap per periode

6. **Log Aktivitas**
   - Audit trail semua aktivitas
   - Data sebelum/sesudah
   - Waktu aksi

7. **Pengaturan**
   - Sistem, pengguna, notifikasi
   - Profil admin

## 🛠️ Teknologi

- **Framework**: CodeIgniter 4
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Chart.js, Font Awesome
- **Security**: RBAC, CSRF protection, Password hashing

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer
- Web server (Apache/Nginx)

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone [repository-url]
cd manajemen_setoran
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Database
- Buat database MySQL dengan nama `setoran_db`
- Sesuaikan konfigurasi di `app/Config/Database.php`

### 4. Jalankan Setup
```bash
php setup.php
```
Atau akses `http://localhost/manajemen_setoran/setup.php` via browser

### 5. Jalankan Aplikasi
```bash
php spark serve
```
Akses `http://localhost:8080`

## 👥 Akun Default

### Administrator
- **Username**: admin
- **Password**: admin123
- **Email**: admin@setoran.com
- **Role**: Admin

### User Contoh
- **Username**: ahmad
- **Password**: password123
- **Email**: ahmad@gmail.com
- **Role**: User

## 🔒 Keamanan

- **Authentication & Authorization**: Login dengan RBAC
- **Password Hashing**: Bcrypt algorithm
- **CSRF Protection**: Token pada semua form
- **Input Validation**: Validasi server-side
- **SQL Injection Prevention**: Parameterized queries
- **XSS Protection**: Output escaping
- **Session Management**: Secure session handling
- **Audit Log**: Log semua aktivitas penting

## 📱 Responsive Design

- Desktop, laptop, tablet, smartphone
- Sidebar mobile drawer
- Responsive tables
- Adaptive cards
- Mobile-friendly chat

## 📊 Database Schema

### Tabel Utama
1. **users**: Data pengguna (user dan admin)
2. **periode_setoran**: Periode setoran iuran
3. **setoran**: Data setoran per periode
4. **chats**: Pesan obrolan antar pengguna
5. **log_aktivitas**: Log semua aktivitas sistem
6. **groups**: Grup obrolan
7. **group_members**: Anggota grup
8. **group_messages**: Pesan grup

## 🔧 Customization

### Warna & Tema
- Primary: Biru tua (#1e40af)
- Secondary: Hijau (#10b981)
- Warning: Oranye (#f59e0b)
- Danger: Merah (#ef4444)

### Font
- Inter (utama)
- Poppins (alternatif)

### Logo & Branding
- Ganti logo di `public/`
- Sesuaikan nama sistem di config

## 📝 Alur Bisnis

1. User melakukan setoran via WhatsApp pribadi ke Admin
2. Admin menerima informasi setoran
3. Admin login ke sistem
4. Admin mencari user dan input data setoran
5. Sistem validasi (user aktif, periode valid, nominal > 0, cegah duplikasi)
6. Data disimpan ke database
7. Dashboard dan riwayat user otomatis terupdate
8. Rekap admin otomatis terupdate
9. Log aktivitas dicatat

## ⚠️ Fitur yang Tidak Tersedia

- Upload bukti pembayaran oleh User
- Submit setoran oleh User
- Payment gateway
- Form pembayaran online
- User mengubah nominal/status setoran

## 🧪 Testing

### Unit Testing
```bash
php spark test
```

### Manual Testing
1. Test login dengan semua role
2. Test akses halaman sesuai role
3. Test CRUD setoran (admin)
4. Test validasi form
5. Test filter dan search
6. Test export laporan
7. Test fitur chat
8. Test responsive design

## 🔄 Deployment

### Production Environment
1. Update `CI_ENVIRONMENT` ke `production`
2. Enable CSRF protection
3. Set secure session config
4. Enable database backup
5. Configure proper permissions
6. Setup SSL/HTTPS

### Backup Database
```sql
-- Backup manual
mysqldump -u username -p setoran_db > backup.sql

-- Restore
mysql -u username -p setoran_db < backup.sql
```

## 🆘 Troubleshooting

### Error Common Issues
1. **Database connection failed**: Cek config database dan pastikan service MySQL running
2. **404 Not Found**: Enable mod_rewrite (Apache) atau setup proper Nginx config
3. **Permission denied**: Set permission folder writable ke 755
4. **CSRF error**: Enable JavaScript di browser

### Debug Mode
```php
// app/Config/Constants.php
define('ENVIRONMENT', 'development');
```

## 📞 Support

- **Issues**: [GitHub Issues](link)
- **Documentation**: [Wiki](link)
- **Email**: support@setoran.com

## 📄 License

Proprietary Software - © 2024 Sistem Manajemen Setoran Iuran Terpadu

## 🙏 Credits

- CodeIgniter 4 Framework
- Bootstrap 5
- Chart.js
- Font Awesome
- Google Fonts

## 🔄 Update Log

### v1.0.0 (August 2024)
- Initial release
- Core features complete
- Authentication & RBAC
- Setoran management
- Chat system
- Reporting & export
- Responsive design

---

**Catatan**: Sistem ini dirancang sesuai alur bisnis dimana setoran dilakukan via WhatsApp pribadi, dan website hanya untuk pencatatan, monitoring, dan manajemen data oleh admin.