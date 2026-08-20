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

        // Get user statistics
        $userStats = $setoranModel->getUserSummary($this->userData['id']);

        // Get active periode
        $activePeriode = $periodeModel->getActivePeriode();

        // Get user setoran for active periode
        $activePeriodeSetoran = [];
        if ($activePeriode) {
            $activePeriodeSetoran = $setoranModel->where('user_id', $this->userData['id'])
                                                ->where('periode_id', $activePeriode['id'])
                                                ->where('status_setoran !=', 'dibatalkan')
                                                ->findAll();
        }

        // Calculate progress
        $progress = 0;
        if ($activePeriode && $activePeriode['jumlah_kewajiban'] > 0) {
            $totalSetoran = $setoranModel->getTotalByUser($this->userData['id']);
            $progress = min(100, ($totalSetoran / $activePeriode['jumlah_kewajiban']) * 100);
        }

        // Get recent setoran
        $recentSetoran = $setoranModel->where('user_id', $this->userData['id'])
                                     ->orderBy('tanggal_setoran', 'DESC')
                                     ->limit(5)
                                     ->findAll();

        // Get all user periodes
        $userPeriodes = $periodeModel->getPeriodesForUser($this->userData['created_at']);

        // Calculate periode statistics
        $periodeStats = [
            'total' => count($userPeriodes),
            'completed' => 0,
            'in_progress' => 0,
            'not_started' => 0,
        ];

        foreach ($userPeriodes as $periode) {
            $periodeSetoran = $setoranModel->getUserSummary($this->userData['id'], $periode['id']);
            
            if ($periodeSetoran['total'] >= $periode['jumlah_kewajiban']) {
                $periodeStats['completed']++;
            } elseif ($periodeSetoran['total'] > 0) {
                $periodeStats['in_progress']++;
            } else {
                $periodeStats['not_started']++;
            }
        }

        $data = [
            'title' => 'Dashboard',
            'userStats' => $userStats,
            'activePeriode' => $activePeriode,
            'activePeriodeSetoran' => $activePeriodeSetoran,
            'progress' => $progress,
            'recentSetoran' => $recentSetoran,
            'userPeriodes' => $userPeriodes,
            'periodeStats' => $periodeStats,
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

        // Get statistics
        $userStats = $userModel->getUserStats();
        $setoranStats = $setoranModel->getSetoranStats();
        $periodeStats = $periodeModel->getPeriodeStats();

        // Get monthly statistics for chart
        $monthlyStats = $setoranModel->getMonthlyStats(date('Y'));

        // Get recent activities (simplified - use log model in real app)
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

        $data = [
            'title' => 'Dashboard Admin',
            'userStats' => $userStats,
            'setoranStats' => $setoranStats,
            'periodeStats' => $periodeStats,
            'monthlyStats' => $monthlyStats,
            'recentSetoran' => $recentSetoran,
            'recentUsers' => $recentUsers,
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

        $responseData = [];

        if ($this->userData['role'] === 'admin') {
            // Admin statistics
            $userModel = new \App\Models\UserModel();
            
            $responseData = [
                'total_users' => $userModel->countAll(),
                'total_setoran' => $setoranModel->getSetoranStats()['total_setoran'],
                'active_periode' => $periodeModel->where('status', 'aktif')->countAllResults(),
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