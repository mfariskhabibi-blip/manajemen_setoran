<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodeModel extends Model
{
    protected $table = 'periode_setoran';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nama_periode', 'tanggal_mulai', 'tanggal_selesai',
        'jumlah_kewajiban', 'status', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'nama_periode' => 'required|min_length[3]|max_length[100]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
        'jumlah_kewajiban' => 'required|numeric',
        'status' => 'required|in_list[belum_aktif,aktif,selesai]',
    ];
    
    /**
     * Get active periode
     */
    public function getActivePeriode()
    {
        return $this->where('status', 'aktif')->first();
    }
    
    /**
     * Get periode by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('tanggal_mulai >=', $startDate)
                    ->where('tanggal_selesai <=', $endDate)
                    ->findAll();
    }
    
    /**
     * Get periode statistics
     */
    public function getPeriodeStats()
    {
        return [
            'total' => $this->countAll(),
            'belum_aktif' => $this->where('status', 'belum_aktif')->countAllResults(),
            'aktif' => $this->where('status', 'aktif')->countAllResults(),
            'selesai' => $this->where('status', 'selesai')->countAllResults(),
        ];
    }
    
    /**
     * Check if periode overlaps with existing periode
     */
    public function checkOverlap($startDate, $endDate, $excludeId = null)
    {
        $query = $this->where('(tanggal_mulai <=', $endDate)
                      ->where('tanggal_selesai >=', $startDate . ')');
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->countAllResults() > 0;
    }
    
    /**
     * Get periodes for user (based on user registration date)
     */
    public function getPeriodesForUser($userCreatedAt)
    {
        return $this->where('tanggal_mulai >=', $userCreatedAt)->findAll();
    }
}