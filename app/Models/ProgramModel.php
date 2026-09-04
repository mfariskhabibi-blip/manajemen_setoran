<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'kode_program', 'nama_program', 'tujuan', 'deskripsi',
        'target_dana', 'kewajiban_default', 'status',
        'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'kode_program' => 'required|min_length[3]|max_length[50]|is_unique[programs.kode_program]',
        'nama_program' => 'required|min_length[3]|max_length[255]',
        'tujuan' => 'required',
        'target_dana' => 'required|numeric|greater_than_equal_to[0]',
        'kewajiban_default' => 'required|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[aktif,nonaktif]',
    ];
    
    protected $validationMessages = [
        'kode_program' => [
            'required' => 'Kode program harus diisi',
            'min_length' => 'Kode program minimal 3 karakter',
            'max_length' => 'Kode program maksimal 50 karakter',
            'is_unique' => 'Kode program sudah digunakan',
        ],
        'nama_program' => [
            'required' => 'Nama program harus diisi',
            'min_length' => 'Nama program minimal 3 karakter',
            'max_length' => 'Nama program maksimal 255 karakter',
        ],
        'tujuan' => [
            'required' => 'Tujuan program harus diisi',
        ],
    ];
    
    /**
     * Get active programs
     */
    public function getActivePrograms()
    {
        return $this->where('status', 'aktif')->findAll();
    }
    
    /**
     * Get program with statistics
     */
    public function getProgramWithStats($programId)
    {
        $program = $this->find($programId);
        if (!$program) {
            return null;
        }
        
        $db = db_connect();
        
        // Get total participants
        $participants = $db->table('peserta_program')
                          ->where('program_id', $programId)
                          ->where('status', 'aktif')
                          ->countAllResults();
        
        // Get total kewajiban
        $totalKewajiban = $db->table('peserta_program')
                           ->selectSum('total_kewajiban')
                           ->where('program_id', $programId)
                           ->where('status', 'aktif')
                           ->get()
                           ->getRow()
                           ->total_kewajiban ?? 0;
        
        // Get total setoran
        $totalSetoran = $db->table('setoran')
                          ->selectSum('nominal')
                          ->where('program_id', $programId)
                          ->where('status_setoran !=', 'dibatalkan')
                          ->get()
                          ->getRow()
                          ->nominal ?? 0;
        
        // Calculate progress based on target dana (bukan total kewajiban)
        $targetDana = (float)$program['target_dana'];
        $progress = $targetDana > 0 ? ($totalSetoran / $targetDana) * 100 : 0;
        
        // Calculate progress kewajiban (untuk tracking kewajiban peserta)
        $progressKewajiban = $totalKewajiban > 0 ? ($totalSetoran / $totalKewajiban) * 100 : 0;
        
        $program['jumlah_peserta'] = $participants;
        $program['total_kewajiban'] = (float)$totalKewajiban;
        $program['total_setoran'] = (float)$totalSetoran;
        $program['total_kekurangan'] = (float)($targetDana - $totalSetoran);
        $program['sisa_kewajiban'] = (float)($totalKewajiban - $totalSetoran);
        $program['progress'] = round($progress, 2);
        $program['progress_kewajiban'] = round($progressKewajiban, 2);
        
        return $program;
    }
    
    /**
     * Get all programs with statistics
     */
    public function getAllWithStats()
    {
        $programs = $this->findAll();
        foreach ($programs as &$program) {
            $stats = $this->getProgramWithStats($program['id']);
            if ($stats) {
                $program = array_merge($program, $stats);
            }
        }
        return $programs;
    }
    
    /**
     * Get program statistics summary
     */
    public function getProgramStats()
    {
        return [
            'total' => $this->countAll(),
            'aktif' => $this->where('status', 'aktif')->countAllResults(),
            'nonaktif' => $this->where('status', 'nonaktif')->countAllResults(),
        ];
    }
    
    /**
     * Get programs for user
     */
    public function getProgramsForUser($userId)
    {
        $db = db_connect();
        return $db->table('programs p')
                  ->join('peserta_program pp', 'p.id = pp.program_id')
                  ->where('pp.user_id', $userId)
                  ->where('pp.status', 'aktif')
                  ->where('p.status', 'aktif')
                  ->get()
                  ->getResultArray();
    }
    
    /**
     * Check if user is participant of program
     */
    public function isUserParticipant($programId, $userId)
    {
        $db = db_connect();
        $result = $db->table('peserta_program')
                  ->where('program_id', $programId)
                  ->where('user_id', $userId)
                  ->where('status', 'aktif')
                  ->get()
                  ->getRow();
        
        return $result !== null;
    }
}
