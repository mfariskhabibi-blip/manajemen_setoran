<?php

namespace App\Controllers;

use App\Models\SetoranModel;
use App\Models\ProgramModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
use App\Models\AcaraModel;
use App\Models\PengeluaranModel;

class RekapController extends BaseController
{
    protected $setoranModel;
    protected $programModel;
    protected $periodeModel;
    protected $userModel;
    protected $acaraModel;
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->setoranModel     = new SetoranModel();
        $this->programModel     = new ProgramModel();
        $this->periodeModel     = new PeriodeModel();
        $this->userModel        = new UserModel();
        $this->acaraModel       = new AcaraModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    /**
     * Rekap Setoran & Pengeluaran Page
     */
    public function index()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $programId = $this->request->getGet('program_id');
        $periodeId = $this->request->getGet('periode_id');
        $userId    = $this->request->getGet('user_id');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $status    = $this->request->getGet('status');

        $db = db_connect();
        $builder = $db->table('setoran s')
            ->select('s.*, u.nama as user_name, u.email as user_email, p.nama_program, p.kode_program, pr.nama_periode, a.nama as admin_name')
            ->join('users u', 'u.id = s.user_id')
            ->join('programs p', 'p.id = s.program_id')
            ->join('periode_setoran pr', 'pr.id = s.periode_id')
            ->join('users a', 'a.id = s.created_by', 'left');

        if (!empty($programId)) {
            $builder->where('s.program_id', $programId);
        }
        if (!empty($periodeId)) {
            $builder->where('s.periode_id', $periodeId);
        }
        if (!empty($userId)) {
            $builder->where('s.user_id', $userId);
        }
        if (!empty($status)) {
            $builder->where('s.status_setoran', $status);
        }
        if (!empty($startDate)) {
            $builder->where('s.tanggal_setoran >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('s.tanggal_setoran <=', $endDate);
        }

        $setoranList = $builder->orderBy('s.tanggal_setoran', 'DESC')->get()->getResultArray();

        // Calculate statistics summary for setoran
        $totalNominal = 0;
        $totalTercatat = 0;
        $totalDiverifikasi = 0;

        foreach ($setoranList as $item) {
            if ($item['status_setoran'] !== 'dibatalkan') {
                $totalNominal += (float)$item['nominal'];
            }
            if ($item['status_setoran'] === 'tercatat') {
                $totalTercatat++;
            } elseif ($item['status_setoran'] === 'diverifikasi') {
                $totalDiverifikasi++;
            }
        }

        // Pengeluaran Query
        $pengeluaranBuilder = $db->table('pengeluaran p')
            ->select('p.*, u.nama as admin_name')
            ->join('users u', 'u.id = p.admin_id', 'left');

        if (!empty($startDate)) {
            $pengeluaranBuilder->where('p.tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $pengeluaranBuilder->where('p.tanggal <=', $endDate);
        }

        $pengeluaranList = $pengeluaranBuilder->orderBy('p.tanggal', 'DESC')->get()->getResultArray();

        $totalPengeluaran = 0;
        foreach ($pengeluaranList as $p) {
            $totalPengeluaran += (float)$p['jumlah'];
        }

        $saldoKasNet = $totalNominal - $totalPengeluaran;

        // Calculate Target
        $totalTarget = 0;
        if (!empty($programId)) {
            $program = $this->programModel->find($programId);
            $totalTarget = $program ? (float)$program['target_dana'] : 0;
        } else {
            $activeEvent = $this->acaraModel->getActiveEvent();
            $totalTarget = $activeEvent ? (float)$activeEvent['target_total'] : 0;
        }
        $totalKekurangan = $totalTarget - $totalNominal;
        if ($totalKekurangan < 0) $totalKekurangan = 0;
        $progressGross = $totalTarget > 0 ? min(100, round(($totalNominal / $totalTarget) * 100, 1)) : 0;
        $progressNet   = $totalTarget > 0 ? max(0, min(100, round(($saldoKasNet / $totalTarget) * 100, 1))) : 0;

        $data = [
            'title'             => 'Rekap Kas & Setoran',
            'setoranList'       => $setoranList,
            'pengeluaranList'   => $pengeluaranList,
            'programs'          => $this->programModel->findAll(),
            'periodes'          => $this->periodeModel->findAll(),
            'users'             => $this->userModel->getByRole('user'),
            'totalNominal'      => $totalNominal,
            'totalPengeluaran'  => $totalPengeluaran,
            'saldoKasNet'       => $saldoKasNet,
            'totalTercatat'     => $totalTercatat,
            'totalDiverifikasi' => $totalDiverifikasi,
            'totalTarget'       => $totalTarget,
            'totalKekurangan'   => $totalKekurangan,
            'progressGross'     => $progressGross,
            'progressNet'       => $progressNet,
            'progressPercentage'=> $progressGross,
            'filters'           => [
                'program_id' => $programId,
                'periode_id' => $periodeId,
                'user_id'    => $userId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => $status,
            ]
        ];

        return $this->render('admin/rekap/index', $data);
    }

    /**
     * Export Rekap to CSV / Excel
     */
    public function export()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $programId = $this->request->getGet('program_id');
        $periodeId = $this->request->getGet('periode_id');
        $userId    = $this->request->getGet('user_id');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $status    = $this->request->getGet('status');

        $db = db_connect();
        $builder = $db->table('setoran s')
            ->select('s.*, u.nama as user_name, p.nama_program, pr.nama_periode, a.nama as admin_name')
            ->join('users u', 'u.id = s.user_id')
            ->join('programs p', 'p.id = s.program_id')
            ->join('periode_setoran pr', 'pr.id = s.periode_id')
            ->join('users a', 'a.id = s.created_by', 'left');

        if (!empty($programId)) $builder->where('s.program_id', $programId);
        if (!empty($periodeId)) $builder->where('s.periode_id', $periodeId);
        if (!empty($userId)) $builder->where('s.user_id', $userId);
        if (!empty($status)) $builder->where('s.status_setoran', $status);
        if (!empty($startDate)) $builder->where('s.tanggal_setoran >=', $startDate);
        if (!empty($endDate)) $builder->where('s.tanggal_setoran <=', $endDate);

        $results = $builder->orderBy('s.tanggal_setoran', 'DESC')->get()->getResultArray();

        $filename = 'Rekap_Setoran_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Tanggal', 'Pengguna', 'Periode', 'Nominal', 'Status', 'Catatan/Keterangan', 'Recorded By']);

        $no = 1;
        foreach ($results as $row) {
            fputcsv($output, [
                $no++,
                date('d/m/Y', strtotime($row['tanggal_setoran'])),
                $row['user_name'],
                $row['nama_periode'],
                'Rp ' . number_format($row['nominal'], 0, ',', '.'),
                ucfirst($row['status_setoran']),
                $row['keterangan'] ?? '-',
                $row['admin_name'] ?? 'System'
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Print Rekap View
     */
    public function printRekap()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $programId = $this->request->getGet('program_id');
        $periodeId = $this->request->getGet('periode_id');
        $userId    = $this->request->getGet('user_id');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $db = db_connect();
        $builder = $db->table('setoran s')
            ->select('s.*, u.nama as user_name, p.nama_program, pr.nama_periode')
            ->join('users u', 'u.id = s.user_id')
            ->join('programs p', 'p.id = s.program_id')
            ->join('periode_setoran pr', 'pr.id = s.periode_id');

        if (!empty($programId)) $builder->where('s.program_id', $programId);
        if (!empty($periodeId)) $builder->where('s.periode_id', $periodeId);
        if (!empty($userId)) $builder->where('s.user_id', $userId);
        if (!empty($startDate)) $builder->where('s.tanggal_setoran >=', $startDate);
        if (!empty($endDate)) $builder->where('s.tanggal_setoran <=', $endDate);

        $setoranList = $builder->orderBy('s.tanggal_setoran', 'DESC')->get()->getResultArray();

        $data = [
            'title'       => 'Cetak Rekap Setoran',
            'setoranList' => $setoranList,
            'tanggal'     => date('d F Y')
        ];

        return view('admin/rekap/print', $data);
    }
}
