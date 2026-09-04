<?php

namespace App\Models;

use CodeIgniter\Model;

class SetoranModel extends Model
{
    protected $table = 'setoran';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id', 'program_id', 'acara_id', 'periode_id', 'tanggal_setoran', 'nominal',
        'status_setoran', 'keterangan', 'created_by', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'program_id' => 'required|numeric',
        'periode_id' => 'required|numeric',
        'tanggal_setoran' => 'required|valid_date',
        'nominal' => 'required|numeric|greater_than[0]',
        'status_setoran' => 'required|in_list[tercatat,diverifikasi,dikoreksi,dibatalkan]',
        'created_by' => 'numeric',
    ];
    
    /**
     * Get setoran by user
     */
    public function getByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('tanggal_setoran', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get setoran by user and program
     */
    public function getByUserAndProgram($userId, $programId)
    {
        return $this->where('user_id', $userId)
                    ->where('program_id', $programId)
                    ->orderBy('tanggal_setoran', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get setoran by program
     */
    public function getByProgram($programId)
    {
        return $this->where('program_id', $programId)
                    ->orderBy('tanggal_setoran', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get setoran by periode
     */
    public function getByPeriode($periodeId)
    {
        return $this->where('periode_id', $periodeId)
                    ->orderBy('tanggal_setoran', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get total setoran by user
     */
    public function getTotalByUser($userId)
    {
        $result = $this->selectSum('nominal')
                      ->where('user_id', $userId)
                      ->where('status_setoran !=', 'dibatalkan')
                      ->first();
        
        return $result ? (float)$result['nominal'] : 0;
    }
    
    /**
     * Get total setoran by user and program
     */
    public function getTotalByUserAndProgram($userId, $programId)
    {
        $result = $this->selectSum('nominal')
                      ->where('user_id', $userId)
                      ->where('program_id', $programId)
                      ->where('status_setoran !=', 'dibatalkan')
                      ->first();
        
        return $result ? (float)$result['nominal'] : 0;
    }
    
    /**
     * Get total setoran by program
     */
    public function getTotalByProgram($programId)
    {
        $result = $this->selectSum('nominal')
                      ->where('program_id', $programId)
                      ->where('status_setoran !=', 'dibatalkan')
                      ->first();
        
        return $result ? (float)$result['nominal'] : 0;
    }
    
    /**
     * Get total setoran by periode
     */
    public function getTotalByPeriode($periodeId)
    {
        $result = $this->selectSum('nominal')
                      ->where('periode_id', $periodeId)
                      ->where('status_setoran !=', 'dibatalkan')
                      ->first();
        
        return $result ? (float)$result['nominal'] : 0;
    }
    
    /**
     * Get setoran statistics
     */
    public function getSetoranStats()
    {
        $totalSetoranResult = $this->selectSum('nominal')
                                   ->where('status_setoran !=', 'dibatalkan')
                                   ->first();
        return [
            'total_setoran' => $totalSetoranResult ? (float)$totalSetoranResult['nominal'] : 0,
            'total_tercatat' => $this->where('status_setoran', 'tercatat')->countAllResults(),
            'total_diverifikasi' => $this->where('status_setoran', 'diverifikasi')->countAllResults(),
            'total_dikoreksi' => $this->where('status_setoran', 'dikoreksi')->countAllResults(),
            'total_dibatalkan' => $this->where('status_setoran', 'dibatalkan')->countAllResults(),
        ];
    }
    
    /**
     * Get monthly statistics
     */
    public function getMonthlyStats($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $db = db_connect();
        $query = $db->query("
            SELECT 
                MONTH(tanggal_setoran) as month,
                SUM(nominal) as total
            FROM setoran
            WHERE YEAR(tanggal_setoran) = ?
                AND status_setoran != 'dibatalkan'
            GROUP BY MONTH(tanggal_setoran)
            ORDER BY month
        ", [$year]);
        
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[$i] = 0;
        }
        
        foreach ($query->getResultArray() as $row) {
            $result[(int)$row['month']] = (float)$row['total'];
        }
        
        return $result;
    }
    
    /**
     * Check if user already has setoran for periode
     */
    public function hasSetoranForPeriode($userId, $periodeId)
    {
        return $this->where('user_id', $userId)
                    ->where('periode_id', $periodeId)
                    ->where('status_setoran !=', 'dibatalkan')
                    ->countAllResults() > 0;
    }
    
    /**
     * Check if user already has setoran for program and periode
     */
    public function hasSetoranForProgramPeriode($userId, $programId, $periodeId)
    {
        return $this->where('user_id', $userId)
                    ->where('program_id', $programId)
                    ->where('periode_id', $periodeId)
                    ->where('status_setoran !=', 'dibatalkan')
                    ->countAllResults() > 0;
    }
    
    /**
     * Get recent setoran
     */
    public function getRecentSetoran($limit = 10)
    {
        return $this->select('setoran.*, u.nama as user_name')
                    ->join('users u', 'u.id = setoran.user_id', 'left')
                    ->orderBy('setoran.tanggal_setoran', 'DESC')
                    ->orderBy('setoran.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get setoran by status
     */
    public function getByStatus($status)
    {
        return $this->where('status_setoran', $status)
                    ->orderBy('tanggal_setoran', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get user setoran summary
     */
    public function getUserSummary($userId, $periodeId = null)
    {
        $query = $this->where('user_id', $userId)
                      ->where('status_setoran !=', 'dibatalkan');
        
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        
        $result = $query->select('COUNT(*) as count, SUM(nominal) as total')
                       ->first();
        
        return [
            'count' => (int)($result['count'] ?? 0),
            'total' => (float)($result['total'] ?? 0)
        ];
    }
}