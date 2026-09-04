<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaProgramModel extends Model
{
    protected $table = 'peserta_program';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'program_id', 'user_id', 'periode_id',
        'total_kewajiban', 'status', 'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;
    
    protected $validationRules = [
        'program_id' => 'required|numeric',
        'user_id' => 'required|numeric',
        'total_kewajiban' => 'required|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[aktif,nonaktif]',
    ];
    
    /**
     * Get participants by program
     */
    public function getByProgram($programId)
    {
        return $this->where('program_id', $programId)
                    ->where('status', 'aktif')
                    ->findAll();
    }
    
    /**
     * Get participants by user
     */
    public function getByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'aktif')
                    ->findAll();
    }
    
    /**
     * Get participant by program and user
     */
    public function getByProgramAndUser($programId, $userId, $periodeId = null)
    {
        $query = $this->where('program_id', $programId)
                     ->where('user_id', $userId)
                     ->where('status', 'aktif');
        
        if ($periodeId !== null && $periodeId !== 0 && $periodeId !== '0') {
            $query->where('periode_id', $periodeId);
        }
        
        return $query->first();
    }
    
    /**
     * Get user's program summary with setoran info
     */
    public function getUserProgramSummary($userId, $programId)
    {
        $peserta = $this->getByProgramAndUser($programId, $userId);
        if (!$peserta) {
            return null;
        }
        
        $db = db_connect();
        
        // Get total setoran for this user in this program
        $totalSetoran = $db->table('setoran')
                          ->selectSum('nominal')
                          ->where('user_id', $userId)
                          ->where('program_id', $programId)
                          ->where('status_setoran !=', 'dibatalkan')
                          ->get()
                          ->getRow()
                          ->nominal ?? 0;
        
        $sisaKewajiban = $peserta['total_kewajiban'] - $totalSetoran;
        $progress = $peserta['total_kewajiban'] > 0 ? ($totalSetoran / $peserta['total_kewajiban']) * 100 : 0;
        
        // Determine status
        if ($totalSetoran == 0) {
            $statusKewajiban = 'belum_mulai';
        } elseif ($totalSetoran < $peserta['total_kewajiban']) {
            $statusKewajiban = 'berjalan';
        } else {
            $statusKewajiban = 'selesai';
        }
        
        return [
            'peserta' => $peserta,
            'total_setoran' => (float)$totalSetoran,
            'sisa_kewajiban' => (float)$sisaKewajiban,
            'progress' => round($progress, 2),
            'status_kewajiban' => $statusKewajiban,
        ];
    }
    
    /**
     * Add user to program
     */
    public function addUserToProgram($programId, $userId, $totalKewajiban, $periodeId = null)
    {
        // Check if already participant
        $existing = $this->getByProgramAndUser($programId, $userId, $periodeId);
        if ($existing) {
            return false;
        }
        
        $db = db_connect();
        $builder = $db->table($this->table);
        
        $data = [
            'program_id' => (int)$programId,
            'user_id' => (int)$userId,
            'total_kewajiban' => (float)$totalKewajiban,
            'status' => 'aktif',
        ];
        
        // Only include periode_id if it's provided and not 0
        if ($periodeId !== null && $periodeId !== 0 && $periodeId !== '0') {
            $data['periode_id'] = (int)$periodeId;
        }
        
        $builder->insert($data);
        return $db->insertID();
    }
    
    /**
     * Remove user from program
     */
    public function removeUserFromProgram($programId, $userId)
    {
        return $this->where('program_id', $programId)
                   ->where('user_id', $userId)
                   ->update(['status' => 'nonaktif']);
    }
    
    /**
     * Get participants count by program
     */
    public function getParticipantsCount($programId)
    {
        return $this->where('program_id', $programId)
                   ->where('status', 'aktif')
                   ->countAllResults();
    }
}
