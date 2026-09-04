<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcaraTables extends Migration
{
    public function up()
    {
        // Table: acara
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_acara' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Halalbihalal & Orkes 2026',
            ],
            'tanggal_pelaksanaan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'target_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 50000000.00,
            ],
            'skema_tarif' => [
                'type'       => 'ENUM',
                'constraint' => ['flat', 'tiered'],
                'default'    => 'flat',
            ],
            'tarif_default' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 200000.00,
            ],
            'deadline_pembayaran' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['perencanaan', 'aktif', 'selesai', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('acara');

        // Table: acara_warga
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'acara_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kategori_warga' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Warga Reguler',
            ],
            'nominal_kewajiban' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 200000.00,
            ],
            'status_wajib' => [
                'type'       => 'ENUM',
                'constraint' => ['wajib', 'bebas'],
                'default'    => 'wajib',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['acara_id', 'user_id']);
        $this->forge->createTable('acara_warga');
    }

    public function down()
    {
        $this->forge->dropTable('acara_warga', true);
        $this->forge->dropTable('acara', true);
    }
}
