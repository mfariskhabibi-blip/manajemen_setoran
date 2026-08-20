<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $adminData = [
            'nama' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@setoran.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'nomor_whatsapp' => '6281234567890',
            'role' => 'admin',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('users')->insert($adminData);
        
        // Create sample user
        $userData = [
            'nama' => 'Ahmad Santoso',
            'username' => 'ahmad',
            'email' => 'ahmad@gmail.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'nomor_whatsapp' => '6289876543210',
            'role' => 'user',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('users')->insert($userData);
        
        // Create sample periode
        $periodeData = [
            [
                'nama_periode' => 'Januari - Juni 2026',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-06-30',
                'jumlah_kewajiban' => 3000000,
                'status' => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_periode' => 'Juli - Desember 2026',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-31',
                'jumlah_kewajiban' => 3500000,
                'status' => 'belum_aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        foreach ($periodeData as $periode) {
            $this->db->table('periode_setoran')->insert($periode);
        }
        
        // Create sample setoran
        $setoranData = [
            [
                'user_id' => 2, // Ahmad
                'periode_id' => 1,
                'tanggal_setoran' => '2026-01-15',
                'nominal' => 500000,
                'status_setoran' => 'diverifikasi',
                'keterangan' => 'Setoran bulan Januari',
                'created_by' => 1, // Admin
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 2,
                'periode_id' => 1,
                'tanggal_setoran' => '2026-02-20',
                'nominal' => 500000,
                'status_setoran' => 'diverifikasi',
                'keterangan' => 'Setoran bulan Februari',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 2,
                'periode_id' => 1,
                'tanggal_setoran' => '2026-03-18',
                'nominal' => 500000,
                'status_setoran' => 'tercatat',
                'keterangan' => 'Setoran bulan Maret',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        foreach ($setoranData as $setoran) {
            $this->db->table('setoran')->insert($setoran);
        }
        
        // Create sample chat
        $chatData = [
            [
                'sender_id' => 2, // Ahmad
                'receiver_id' => 1, // Admin
                'pesan' => 'Halo admin, saya sudah transfer setoran bulan Maret.',
                'status_baca' => 'read',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'sender_id' => 1, // Admin
                'receiver_id' => 2, // Ahmad
                'pesan' => 'Terima kasih, setoran sudah saya catat.',
                'status_baca' => 'unread',
                'created_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
            ],
        ];
        
        foreach ($chatData as $chat) {
            $this->db->table('chats')->insert($chat);
        }
        
        // Create sample log aktivitas
        $logData = [
            [
                'user_id' => 1,
                'aktivitas' => 'Login ke sistem',
                'waktu' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            ],
            [
                'user_id' => 2,
                'aktivitas' => 'Login ke sistem',
                'waktu' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            ],
            [
                'user_id' => 1,
                'aktivitas' => 'Menambahkan setoran',
                'data_sesudah' => json_encode(['user_id' => 2, 'nominal' => 500000]),
                'waktu' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
        ];
        
        foreach ($logData as $log) {
            $this->db->table('log_aktivitas')->insert($log);
        }
        
        echo "Seeder berhasil dijalankan!\n";
        echo "- Admin: admin / admin123\n";
        echo "- User: ahmad / password123\n";
    }
}