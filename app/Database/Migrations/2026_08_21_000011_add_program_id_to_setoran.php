<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramIdToSetoran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('setoran', [
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'user_id',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('program_id');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('setoran', 'setoran_program_id_foreign');
        $this->forge->dropColumn('setoran', 'program_id');
    }
}
