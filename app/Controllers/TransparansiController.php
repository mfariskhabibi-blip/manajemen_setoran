<?php

namespace App\Controllers;

use App\Models\PengeluaranModel;
use App\Models\AcaraModel;

class TransparansiController extends BaseController
{
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('setoran');
        $builder->select('setoran.id, setoran.tanggal_setoran, setoran.nominal, setoran.status_setoran, setoran.keterangan, users.nama as user_name, programs.nama_program');
        $builder->join('users', 'users.id = setoran.user_id');
        $builder->join('programs', 'programs.id = setoran.program_id', 'left');
        $builder->where('setoran.status_setoran', 'diverifikasi');
        $builder->orderBy('setoran.tanggal_setoran', 'DESC');
        $builder->orderBy('setoran.id', 'DESC');
        
        $setoranList = $builder->get()->getResultArray();
        
        // Sum total setoran
        $totalSetoran = 0;
        foreach ($setoranList as $row) {
            $totalSetoran += $row['nominal'];
        }

        // Fetch pengeluaran list & sum
        $pengeluaranModel = new PengeluaranModel();
        $pengeluaranList  = $pengeluaranModel->getWithAdmin();
        $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();

        // Saldo kas net
        $saldoKas = $totalSetoran - $totalPengeluaran;

        // Fetch active event for target calculations
        $acaraModel  = new AcaraModel();
        $activeEvent = $acaraModel->getActiveEvent();
        $totalTarget = $activeEvent ? (float)($activeEvent['target_total'] ?? 50000000) : 50000000;

        $progressGross = $totalTarget > 0 ? min(100, round(($totalSetoran / $totalTarget) * 100, 1)) : 0;
        $progressNet   = $totalTarget > 0 ? max(0, min(100, round(($saldoKas / $totalTarget) * 100, 1))) : 0;

        $data = [
            'title'            => 'Transparansi Kas',
            'user'             => $this->userData,
            'setoranList'      => $setoranList,
            'pengeluaranList'  => $pengeluaranList,
            'totalSetoran'     => $totalSetoran,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKas'         => $saldoKas,
            'activeEvent'      => $activeEvent,
            'totalTarget'      => $totalTarget,
            'progressGross'    => $progressGross,
            'progressNet'      => $progressNet,
        ];

        return $this->render('transparansi/index', $data);
    }
}
