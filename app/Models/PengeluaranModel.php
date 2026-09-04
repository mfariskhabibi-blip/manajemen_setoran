<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'admin_id',
        'tanggal',
        'kategori',
        'keterangan',
        'jumlah',
        'file_path',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'tanggal'    => 'required|valid_date',
        'keterangan' => 'required',
        'jumlah'     => 'required|numeric|greater_than[0]',
    ];

    /**
     * Get total sum of all expenses
     */
    public function getTotalPengeluaran()
    {
        $result = $this->selectSum('jumlah')->first();
        return $result ? (float)$result['jumlah'] : 0.00;
    }

    /**
     * Get pengeluaran list with admin info
     */
    public function getWithAdmin($limit = null, $offset = 0)
    {
        $builder = $this->select('pengeluaran.*, u.nama as admin_name')
                        ->join('users u', 'u.id = pengeluaran.admin_id', 'left')
                        ->orderBy('pengeluaran.tanggal', 'DESC')
                        ->orderBy('pengeluaran.id', 'DESC');

        if ($limit) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }
}
