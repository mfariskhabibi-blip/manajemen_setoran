<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcaraIdToSetoran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('setoran', [
            'acara_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'program_id',
            ],
        ]);

        $this->forge->addForeignKey('acara_id', 'acara', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('setoran', 'setoran_acara_id_foreign');
        $this->forge->dropColumn('setoran', 'acara_id');
    }
}
