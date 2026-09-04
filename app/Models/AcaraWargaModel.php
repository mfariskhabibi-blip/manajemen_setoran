<?php

namespace App\Models;

use CodeIgniter\Model;

class AcaraWargaModel extends Model
{
    protected $table            = 'acara_warga';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'acara_id',
        'user_id',
        'kategori_warga',
        'nominal_kewajiban',
        'status_wajib',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all citizens with event obligation status
     */
    public function getWargaListForEvent($acaraId, $search = null, $statusFilter = null)
    {
        $db = db_connect();

        $builder = $db->table('users u')
            ->select('u.id as user_id, u.nama, u.username, u.email, u.nomor_whatsapp, u.status as user_status, aw.id as acara_warga_id, aw.acara_id, aw.kategori_warga, aw.nominal_kewajiban, aw.status_wajib')
            ->join('acara_warga aw', 'aw.user_id = u.id AND aw.acara_id = ' . (int)$acaraId, 'left')
            ->where('u.role', 'user');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('u.nama', $search)
                ->orLike('u.username', $search)
                ->orLike('u.nomor_whatsapp', $search)
                ->groupEnd();
        }

        if ($statusFilter === 'wajib') {
            $builder->where('aw.status_wajib', 'wajib');
        } elseif ($statusFilter === 'bebas') {
            $builder->where('aw.status_wajib', 'bebas');
        } elseif ($statusFilter === 'unassigned') {
            $builder->where('aw.id IS NULL');
        }

        $results = $builder->orderBy('u.nama', 'ASC')->get()->getResultArray();

        return $results;
    }

    /**
     * Sync or Assign all users to active event with default tariff
     */
    public function syncAllWargaToEvent($acaraId, $defaultTariff = 200000.00)
    {
        $db = db_connect();
        $users = $db->table('users')->where('role', 'user')->get()->getResultArray();

        foreach ($users as $user) {
            $existing = $this->where('acara_id', $acaraId)->where('user_id', $user['id'])->first();
            if (!$existing) {
                $this->insert([
                    'acara_id'          => $acaraId,
                    'user_id'           => $user['id'],
                    'kategori_warga'    => 'Warga Reguler',
                    'nominal_kewajiban' => $defaultTariff,
                    'status_wajib'      => 'wajib',
                ]);
            } else {
                // Always update nominal kewajiban untuk warga reguler
                // Hanya skip jika warga memiliki kategori khusus (bukan Warga Reguler)
                if ($existing['kategori_warga'] === 'Warga Reguler' || empty($existing['kategori_warga'])) {
                    $this->update($existing['id'], [
                        'nominal_kewajiban' => $defaultTariff,
                        'kategori_warga' => 'Warga Reguler',
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                // Jika warga bebas iuran (status_wajib = 'bebas'), tetap update nominal untuk referensi
                else if ($existing['status_wajib'] === 'bebas') {
                    $this->update($existing['id'], [
                        'nominal_kewajiban' => $defaultTariff,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    /**
     * Update default tariff for all regular residents in an event
     */
    public function updateDefaultTariffForAllWarga($acaraId, $defaultTariff)
    {
        $db = db_connect();
        $updated = $db->table('acara_warga')
            ->where('acara_id', $acaraId)
            ->where('kategori_warga', 'Warga Reguler')
            ->update([
                'nominal_kewajiban' => $defaultTariff,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return $updated;
    }

    /**
     * Get count of regular residents in an event
     */
    public function countRegularResidents($acaraId)
    {
        return $this->where('acara_id', $acaraId)
            ->where('kategori_warga', 'Warga Reguler')
            ->countAllResults();
    }
}