<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    /**
     * User dashboard
     */
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        $pesertaProgramModel = new \App\Models\PesertaProgramModel();
        $pengeluaranModel    = new \App\Models\PengeluaranModel();

        // Get user's programs
        $userPrograms = $programModel->getProgramsForUser($this->userData['id']);
        
        // Get user statistics across all programs
        $totalKewajiban = 0;
        $totalSetoran = 0;
        $activeProgram = null;
        $activePeriode = null;
        
        foreach ($userPrograms as $program) {
            $summary = $pesertaProgramModel->getUserProgramSummary($this->userData['id'], $program['id']);
            if ($summary) {
                $totalKewajiban += $summary['peserta']['total_kewajiban'];
                $totalSetoran += $summary['total_setoran'];
                
                // Get first active program as active program
                if (!$activeProgram && $program['status'] === 'aktif') {
                    $activeProgram = $program;
                    $activeProgram['summary'] = $summary;
                }
            }
        }
        
        // Get active periode for active program
        if ($activeProgram) {
            $activePeriode = $periodeModel->getActivePeriodeByProgram($activeProgram['id']);
        }
        
        // Calculate user progress
        $progress = 0;
        if ($totalKewajiban > 0) {
            $progress = min(100, ($totalSetoran / $totalKewajiban) * 100);
        }
        
        // Determine status
        if ($totalSetoran == 0) {
            $statusKewajiban = 'belum_mulai';
        } elseif ($totalSetoran < $totalKewajiban) {
            $statusKewajiban = 'berjalan';
        } else {
            $statusKewajiban = 'selesai';
        }
        
        // Get active event details & community kas totals
        $acaraModel = new \App\Models\AcaraModel();
        $activeEvent = $acaraModel->getActiveEvent();
        $eventSummary = $acaraModel->getEventSummary($activeEvent['id']);

        $setoranStats = $setoranModel->getSetoranStats();
        $totalSetoranKomunitas = (float)($setoranStats['total_setoran'] ?? 0);
        $totalPengeluaranKomunitas = $pengeluaranModel->getTotalPengeluaran();
        $saldoKasNetKomunitas = $totalSetoranKomunitas - $totalPengeluaranKomunitas;

        $targetTotalEvent = $activeEvent ? (float)($activeEvent['target_total'] ?? 50000000) : 50000000;
        $progressGross = $targetTotalEvent > 0 ? min(100, round(($totalSetoranKomunitas / $targetTotalEvent) * 100, 1)) : 0;
        $progressNet   = $targetTotalEvent > 0 ? max(0, min(100, round(($saldoKasNetKomunitas / $targetTotalEvent) * 100, 1))) : 0;

        // Calculate specific user setoran for active event
        $db = \Config\Database::connect();
        $userEventSetoran = $db->table('setoran')
            ->selectSum('nominal', 'total')
            ->where('user_id', $this->userData['id'])
            ->where('status_setoran !=', 'dibatalkan')
            ->get()->getRow()->total ?? 0;

        $acaraWarga = $db->table('acara_warga')
            ->where('acara_id', $activeEvent['id'])
            ->where('user_id', $this->userData['id'])
            ->get()->getRowArray();

        $userKewajiban = (float)($acaraWarga['nominal_kewajiban'] ?? $activeEvent['tarif_default']);
        $userSisaTagihan = max(0, $userKewajiban - (float)$userEventSetoran);

        // Get recent setoran
        $recentSetoran = $setoranModel->where('user_id', $this->userData['id'])
                                     ->orderBy('tanggal_setoran', 'DESC')
                                     ->limit(5)
                                     ->findAll();

        $data = [
            'title'                     => 'Dashboard',
            'activeEvent'               => $activeEvent,
            'eventSummary'              => $eventSummary,
            'userEventSetoran'          => (float)$userEventSetoran,
            'userKewajiban'             => $userKewajiban,
            'userSisaTagihan'           => $userSisaTagihan,
            'userPrograms'              => $userPrograms,
            'activeProgram'             => $activeProgram,
            'activePeriode'             => $activePeriode,
            'totalKewajiban'            => $totalKewajiban,
            'totalSetoran'              => $totalSetoran,
            'sisaKewajiban'             => $totalKewajiban - $totalSetoran,
            'progress'                  => $progress,
            'statusKewajiban'           => $statusKewajiban,
            'recentSetoran'             => $recentSetoran,
            'totalSetoranKomunitas'     => $totalSetoranKomunitas,
            'totalPengeluaranKomunitas' => $totalPengeluaranKomunitas,
            'saldoKasNetKomunitas'     => $saldoKasNetKomunitas,
            'targetTotalEvent'          => $targetTotalEvent,
            'progressGross'             => $progressGross,
            'progressNet'               => $progressNet,
        ];

        return $this->render('dashboard/index', $data);
    }

    /**
     * Admin dashboard
     */
    public function adminDashboard()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userModel = new \App\Models\UserModel();
        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        $acaraModel = new \App\Models\AcaraModel();
        $pengeluaranModel = new \App\Models\PengeluaranModel();

        // Get active event details
        $activeEvent = $acaraModel->getActiveEvent();
        $eventSummary = $acaraModel->getEventSummary($activeEvent['id']);

        // Get statistics
        $userStats = $userModel->getUserStats();
        $setoranStats = $setoranModel->getSetoranStats();
        $periodeStats = $periodeModel->getPeriodeStats();
        $programStats = $programModel->getProgramStats();

        // Get monthly statistics for chart
        $monthlyStats = $setoranModel->getMonthlyStats(date('Y'));

        // Get Pengeluaran stats & Saldo Kas Net
        $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();
        $totalSetoran = (float)($setoranStats['total_setoran'] ?? 0);
        $saldoKasNet = $totalSetoran - $totalPengeluaran;

        $setoranStats['total_pengeluaran'] = $totalPengeluaran;
        $setoranStats['saldo_kas_net']     = $saldoKasNet;

        $targetTotalEvent = $activeEvent ? (float)($activeEvent['target_total'] ?? 50000000) : 50000000;
        $progressGross = $targetTotalEvent > 0 ? min(100, round(($totalSetoran / $targetTotalEvent) * 100, 1)) : 0;
        $progressNet   = $targetTotalEvent > 0 ? max(0, min(100, round(($saldoKasNet / $targetTotalEvent) * 100, 1))) : 0;

        // Get recent activities
        $recentSetoran = $setoranModel->getRecentSetoran(5);
        
        // Get recent users
        $recentUsers = $userModel->orderBy('created_at', 'DESC')
                                ->limit(5)
                                ->findAll();

        // Get setoran by status
        $setoranByStatus = [
            'tercatat' => $setoranModel->getByStatus('tercatat'),
            'diverifikasi' => $setoranModel->getByStatus('diverifikasi'),
            'dikoreksi' => $setoranModel->getByStatus('dikoreksi'),
        ];
        
        // Get programs with stats
        $programs = $programModel->getAllWithStats();

        $data = [
            'title'           => 'Dashboard Admin',
            'activeEvent'     => $activeEvent,
            'eventSummary'    => $eventSummary,
            'userStats'       => $userStats,
            'setoranStats'    => $setoranStats,
            'totalPengeluaran'=> $totalPengeluaran,
            'saldoKasNet'     => $saldoKasNet,
            'targetTotalEvent'=> $targetTotalEvent,
            'progressGross'   => $progressGross,
            'progressNet'     => $progressNet,
            'periodeStats'    => $periodeStats,
            'programStats'    => $programStats,
            'programs'        => $programs,
            'monthlyStats'    => $monthlyStats,
            'recentSetoran'   => $recentSetoran,
            'recentUsers'     => $recentUsers,
            'setoranByStatus' => $setoranByStatus,
        ];

        return $this->render('dashboard/admin', $data);
    }

    /**
     * Get dashboard statistics (AJAX)
     */
    public function getStats()
    {
        if (!$this->isAjax()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request']);
        }

        if (!$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $pengeluaranModel = new \App\Models\PengeluaranModel();

        $responseData = [];

        if ($this->userData['role'] === 'admin') {
            // Admin statistics
            $userModel = new \App\Models\UserModel();
            $totalSetoran = (float)($setoranModel->getSetoranStats()['total_setoran'] ?? 0);
            $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();
            
            $responseData = [
                'total_users'          => $userModel->countAll(),
                'total_setoran'        => $totalSetoran,
                'total_pengeluaran'    => $totalPengeluaran,
                'saldo_kas_net'        => $totalSetoran - $totalPengeluaran,
                'active_periode'       => $periodeModel->where('status', 'aktif')->countAllResults(),
                'pending_verification' => $setoranModel->where('status_setoran', 'tercatat')->countAllResults(),
            ];
        } else {
            // User statistics
            $activePeriode = $periodeModel->getActivePeriode();
            $userSummary = $setoranModel->getUserSummary($this->userData['id']);
            
            $responseData = [
                'total_setoran' => $userSummary['total'],
                'total_transactions' => $userSummary['count'],
                'remaining_balance' => $activePeriode ? ($activePeriode['jumlah_kewajiban'] - $userSummary['total']) : 0,
                'progress_percentage' => $activePeriode && $activePeriode['jumlah_kewajiban'] > 0 
                    ? min(100, ($userSummary['total'] / $activePeriode['jumlah_kewajiban']) * 100)
                    : 0,
            ];
        }

        return $this->jsonResponse($responseData);
    }

    /**
     * Get chart data (AJAX)
     */
    public function getChartData()
    {
        if (!$this->isAjax() || !$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        
        $year = $this->request->getGet('year') ?? date('Y');
        $monthlyStats = $setoranModel->getMonthlyStats($year);

        // Format for chart
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Total Setoran (Rp)',
                    'data' => array_values($monthlyStats),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ]
            ]
        ];

        return $this->jsonResponse($chartData);
    }
}