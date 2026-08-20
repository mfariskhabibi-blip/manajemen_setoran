<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodeSetoranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_periode' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
            ],
            'jumlah_kewajiban' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_aktif', 'aktif', 'selesai'],
                'default'    => 'belum_aktif',
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
        $this->forge->addKey('status');
        $this->forge->addKey('tanggal_mulai');
        $this->forge->addKey('tanggal_selesai');
        
        $this->forge->createTable('periode_setoran');
    }

    public function down()
    {
        $this->forge->dropTable('periode_setoran');
    }
}