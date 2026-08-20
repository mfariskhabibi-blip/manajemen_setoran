<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitasTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'aktivitas' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'data_sebelum' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_sesudah' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'waktu' => [
                'type' => 'DATETIME',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('aktivitas');
        $this->forge->addKey('waktu');
        
        // Foreign key
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('log_aktivitas');
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas');
    }
}