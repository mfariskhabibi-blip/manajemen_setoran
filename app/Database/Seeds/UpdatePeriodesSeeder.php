<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdatePeriodesSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Update existing periodes to program_id = 1
        $db->table('periode_setoran')
           ->where('program_id IS NULL OR program_id = ""')
           ->update(['program_id' => 1]);
           
        echo "Updated periodes to program_id = 1\n";
        
        // Check if there are programs in the database
        $programs = $db->table('programs')->countAllResults();
        
        if ($programs > 0) {
            echo "Found $programs program(s) in database\n";
        } else {
            echo "No programs found in database\n";
        }
    }
}