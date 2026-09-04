<?php

namespace App\Models;

use CodeIgniter\Model;

class AcaraModel extends Model
{
    protected $table            = 'acara';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nama_acara',
        'tanggal_pelaksanaan',
        'lokasi',
        'deskripsi',
        'target_total',
        'skema_tarif',
        'tarif_default',
        'deadline_pembayaran',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get or create active event
     */
    public function getActiveEvent()
    {
        $event = $this->where('status', 'aktif')->orderBy('id', 'DESC')->first();
        if (!$event) {
            $event = $this->first();
        }

        if (!$event) {
            $id = $this->insert([
                'nama_acara'          => 'Halalbihalal & Orkes 2026',
                'tanggal_pelaksanaan' => '2026-05-15',
                'lokasi'              => 'Lapangan Warga Utama',
                'deskripsi'           => 'Acara tahunan silaturahmi Halalbihalal & Panggung Orkes Kebersamaan Warga 2026.',
                'target_total'        => 50000000.00,
                'skema_tarif'         => 'flat',
                'tarif_default'       => 200000.00,
                'deadline_pembayaran' => '2026-05-01',
                'status'              => 'aktif',
            ]);
            $event = $this->find($id);
        }

        return $event;
    }

    /**
     * Get event summary statistics
     */
    public function getEventSummary($acaraId)
    {
        $db = db_connect();

        // Total Warga
        $totalWarga = $db->table('users')->where('role', 'user')->countAllResults();

        // Total Wajib Iuran in this event
        $wajibQuery = $db->table('acara_warga')
            ->where('acara_id', $acaraId)
            ->where('status_wajib', 'wajib');

        $totalWajib = $wajibQuery->countAllResults();

        // Sum Potential Nominal from mandatory residents
        $potensiRow = $db->table('acara_warga')
            ->selectSum('nominal_kewajiban', 'potensi')
            ->where('acara_id', $acaraId)
            ->where('status_wajib', 'wajib')
            ->get()->getRow();

        $potensiTotal = (float)($potensiRow->potensi ?? 0);

        // Sum Collected Nominal (setoran)
        $terkumpulRow = $db->table('setoran')
            ->selectSum('nominal', 'terkumpul')
            ->where('status_setoran !=', 'dibatalkan')
            ->get()->getRow();

        $totalTerkumpul = (float)($terkumpulRow->terkumpul ?? 0);

        return [
            'total_warga'     => $totalWarga,
            'total_wajib'     => $totalWajib,
            'potensi_total'   => $potensiTotal,
            'total_terkumpul' => $totalTerkumpul,
        ];
    }
}
