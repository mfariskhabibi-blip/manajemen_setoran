<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePeriodeSetoranTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('periode_setoran', [
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id',
                'null' => true,
            ],
        ]);

        // Modify existing columns
        $this->forge->modifyColumn('periode_setoran', [
            'jumlah_kewajiban' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'name' => 'nominal_kewajiban',
            ],
        ]);

        $this->forge->addKey('program_id');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('periode_setoran', 'periode_setoran_program_id_foreign');
        $this->forge->dropColumn('periode_setoran', 'program_id');
        
        $this->forge->modifyColumn('periode_setoran', [
            'nominal_kewajiban' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'name' => 'jumlah_kewajiban',
            ],
        ]);
    }
}
