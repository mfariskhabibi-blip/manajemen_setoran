<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePesertaProgramTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'periode_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'total_kewajiban' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default' => 'aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('program_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('periode_id');
        $this->forge->addUniqueKey(['program_id', 'user_id', 'periode_id'], 'unique_peserta');
        $this->forge->createTable('peserta_program');
    }

    public function down()
    {
        $this->forge->dropTable('peserta_program');
    }
}
