<?php

namespace App\Controllers;

class RiwayatController extends BaseController
{
    /**
     * Display setoran history for user
     */
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        $acaraModel = new \App\Models\AcaraModel();

        // Get active event
        $activeEvent = $acaraModel->getActiveEvent();
        $eventSummary = $acaraModel->getEventSummary($activeEvent['id']);

        $pengeluaranModel = new \App\Models\PengeluaranModel();
        $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();
        $terkumpulAcara   = (float)($eventSummary['total_terkumpul'] ?? 0);
        $saldoKasNet      = $terkumpulAcara - $totalPengeluaran;
        $targetDana       = (float)($activeEvent['target_total'] ?? 50000000);

        $progressGross = $targetDana > 0 ? min(100, round(($terkumpulAcara / $targetDana) * 100, 1)) : 0;
        $progressNet   = $targetDana > 0 ? max(0, min(100, round(($saldoKasNet / $targetDana) * 100, 1))) : 0;

        // Get filter parameters
        $programId = $this->request->getGet('program');
        $periodeId = $this->request->getGet('periode');
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $search = $this->request->getGet('search');

        // Build query
        $query = $setoranModel->select('setoran.*, p.nama_program')
                             ->join('programs p', 'p.id = setoran.program_id', 'left')
                             ->where('user_id', $this->userData['id'])
                             ->where('status_setoran !=', 'dibatalkan');

        if ($programId) {
            $query->where('setoran.program_id', $programId);
        }

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($status && in_array($status, ['tercatat', 'diverifikasi', 'dikoreksi'])) {
            $query->where('status_setoran', $status);
        }

        if ($startDate && $endDate) {
            $query->where('tanggal_setoran >=', $startDate)
                 ->where('tanggal_setoran <=', $endDate);
        } elseif ($startDate) {
            $query->where('tanggal_setoran >=', $startDate);
        } elseif ($endDate) {
            $query->where('tanggal_setoran <=', $endDate);
        }

        if ($search) {
            $query->groupStart()
                 ->like('keterangan', $search)
                 ->groupEnd();
        }

        // Get setoran with pagination
        $perPage = 20;
        $currentPage = $this->request->getGet('page') ?? 1;
        
        $totalRows = $query->countAllResults(false);
        $setoran = $query->orderBy('tanggal_setoran', 'DESC')
                        ->paginate($perPage, 'default', $currentPage);
        
        $pager = $setoranModel->pager;

        // Get all periodes for filter
        $periodes = $periodeModel->findAll();
        
        // Get all programs for filter
        $programs = $programModel->getProgramsForUser($this->userData['id']);

        // Get statistics
        $stats = $setoranModel->getUserSummary($this->userData['id']);

        // Get yearly summary
        $yearlySummary = $this->getYearlySummary($this->userData['id']);

        $data = [
            'title' => 'Riwayat Setoran',
            'setoran' => $setoran,
            'pager' => $pager,
            'periodes' => $periodes,
            'programs' => $programs,
            'stats' => $stats,
            'yearlySummary' => $yearlySummary,
            'activeEvent' => $activeEvent,
            'eventSummary' => $eventSummary,
            'filters' => [
                'program' => $programId,
                'periode' => $periodeId,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'search' => $search,
            ],
        ];

        return $this->render('riwayat/index', $data);
    }

    /**
     * Get yearly summary for user
     */
    private function getYearlySummary($userId)
    {
        $setoranModel = new \App\Models\SetoranModel();
        
        $db = db_connect();
        $query = $db->query("
            SELECT 
                YEAR(tanggal_setoran) as year,
                COUNT(*) as total_transactions,
                SUM(nominal) as total_amount
            FROM setoran
            WHERE user_id = ?
                AND status_setoran != 'dibatalkan'
            GROUP BY YEAR(tanggal_setoran)
            ORDER BY year DESC
        ", [$userId]);
        
        return $query->getResultArray();
    }

    /**
     * View setoran detail
     */
    public function show($id)
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        $acaraModel = new \App\Models\AcaraModel();

        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return redirect()->to('/riwayat')->with('error', 'Setoran tidak ditemukan.');
        }

        // Check if user owns this setoran
        if ($setoran['user_id'] != $this->userData['id']) {
            return redirect()->to('/riwayat')->with('error', 'Anda tidak memiliki akses ke setoran ini.');
        }

        $periode = $periodeModel->find($setoran['periode_id']);
        $program = $programModel->find($setoran['program_id']);
        $acara = $setoran['acara_id'] ? $acaraModel->find($setoran['acara_id']) : null;

        $data = [
            'title' => 'Detail Riwayat Setoran',
            'setoran' => $setoran,
            'periode' => $periode,
            'program' => $program,
            'acara' => $acara,
        ];

        return $this->render('riwayat/detail', $data);
    }

    /**
     * Export history (PDF/Excel)
     */
    public function export()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $programModel = new \App\Models\ProgramModel();

        // Get filter parameters
        $programId = $this->request->getGet('program');
        $periodeId = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $format = $this->request->getGet('format') ?? 'pdf';

        // Build query
        $query = $setoranModel->select('setoran.*, p.nama_program')
                             ->join('programs p', 'p.id = setoran.program_id', 'left')
                             ->where('user_id', $this->userData['id'])
                             ->where('status_setoran !=', 'dibatalkan');

        if ($programId) {
            $query->where('setoran.program_id', $programId);
        }

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($startDate && $endDate) {
            $query->where('tanggal_setoran >=', $startDate)
                 ->where('tanggal_setoran <=', $endDate);
        }

        $setoran = $query->orderBy('tanggal_setoran', 'DESC')->findAll();

        if (empty($setoran)) {
            return redirect()->to('/riwayat')->with('error', 'Tidak ada data untuk diekspor.');
        }

        // Get statistics
        $totalAmount = array_sum(array_column($setoran, 'nominal'));
        $totalTransactions = count($setoran);

        // Prepare export data
        $exportData = [
            'user' => $this->userData,
            'setoran' => $setoran,
            'total_amount' => $totalAmount,
            'total_transactions' => $totalTransactions,
            'export_date' => date('Y-m-d H:i:s'),
            'filters' => [
                'program' => $programId,
                'periode' => $periodeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        // Log activity
        $this->logActivity('Mengekspor riwayat setoran', null, [
            'format' => $format,
            'total_records' => $totalTransactions,
            'total_amount' => $totalAmount
        ]);

        // Return based on format
        if ($format === 'excel') {
            return $this->exportToExcel($exportData);
        } else {
            return $this->exportToPDF($exportData);
        }
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($data)
    {
        // Create simple CSV for now (in production, use PHPExcel or similar)
        $csv = "RIWAYAT SETORAN - " . $data['user']['nama'] . "\n";
        $csv .= "Periode: " . ($data['filters']['start_date'] ?? 'Semua') . " s/d " . ($data['filters']['end_date'] ?? 'Semua') . "\n";
        $csv .= "Tanggal Ekspor: " . $data['export_date'] . "\n\n";
        
        $csv .= "No,Tanggal Setoran,Program,Periode,Nominal,Status,Keterangan\n";
        
        $counter = 1;
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        
        foreach ($data['setoran'] as $item) {
            $periode = $periodeModel->find($item['periode_id']);
            $program = isset($item['program_id']) ? $programModel->find($item['program_id']) : null;
            
            $csv .= $counter++ . ",";
            $csv .= $item['tanggal_setoran'] . ",";
            $csv .= ($program ? $program['nama_program'] : '-') . ",";
            $csv .= ($periode ? $periode['nama_periode'] : '-') . ",";
            $csv .= $item['nominal'] . ",";
            $csv .= $item['status_setoran'] . ",";
            $csv .= str_replace(',', ';', $item['keterangan'] ?? '-') . "\n";
        }
        
        $csv .= "\nTotal Transaksi: " . $data['total_transactions'] . "\n";
        $csv .= "Total Nominal: " . $data['total_amount'] . "\n";
        
        // Set headers for download
        $filename = 'riwayat_setoran_' . date('Ymd_His') . '.csv';
        
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    /**
     * Export to PDF
     */
    private function exportToPDF($data)
    {
        // For now, return CSV as PDF is more complex
        // In production, use TCPDF, mPDF, or Dompdf
        
        $html = '<html><head><style>';
        $html .= 'body { font-family: Arial, sans-serif; }';
        $html .= 'h1 { color: #333; }';
        $html .= 'table { width: 100%; border-collapse: collapse; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; }';
        $html .= 'th { background-color: #f2f2f2; }';
        $html .= '</style></head><body>';
        
        $html .= '<h1>Riwayat Setoran - ' . $data['user']['nama'] . '</h1>';
        $html .= '<p>Periode: ' . ($data['filters']['start_date'] ?? 'Semua') . ' s/d ' . ($data['filters']['end_date'] ?? 'Semua') . '</p>';
        $html .= '<p>Tanggal Ekspor: ' . $data['export_date'] . '</p>';
        
        $html .= '<table>';
        $html .= '<tr><th>No</th><th>Tanggal Setoran</th><th>Program</th><th>Periode</th><th>Nominal</th><th>Status</th><th>Keterangan</th></tr>';
        
        $counter = 1;
        $periodeModel = new \App\Models\PeriodeModel();
        $programModel = new \App\Models\ProgramModel();
        
        foreach ($data['setoran'] as $item) {
            $periode = $periodeModel->find($item['periode_id']);
            $program = isset($item['program_id']) ? $programModel->find($item['program_id']) : null;
            
            $html .= '<tr>';
            $html .= '<td>' . $counter++ . '</td>';
            $html .= '<td>' . $item['tanggal_setoran'] . '</td>';
            $html .= '<td>' . ($program ? $program['nama_program'] : '-') . '</td>';
            $html .= '<td>' . ($periode ? $periode['nama_periode'] : '-') . '</td>';
            $html .= '<td>' . number_format($item['nominal'], 0, ',', '.') . '</td>';
            $html .= '<td>' . $item['status_setoran'] . '</td>';
            $html .= '<td>' . ($item['keterangan'] ?? '-') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        $html .= '<p><strong>Total Transaksi:</strong> ' . $data['total_transactions'] . '</p>';
        $html .= '<p><strong>Total Nominal:</strong> ' . number_format($data['total_amount'], 0, ',', '.') . '</p>';
        
        $html .= '</body></html>';
        
        // Set headers for download
        $filename = 'riwayat_setoran_' . date('Ymd_His') . '.html';
        
        return $this->response
            ->setHeader('Content-Type', 'text/html')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    /**
     * Get chart data (AJAX)
     */
    public function getChartData()
    {
        if (!$this->isAjax() || !$this->userData) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        
        $year = $this->request->getGet('year') ?? date('Y');
        
        $db = db_connect();
        $query = $db->query("
            SELECT 
                MONTH(tanggal_setoran) as month,
                SUM(nominal) as total
            FROM setoran
            WHERE user_id = ?
                AND YEAR(tanggal_setoran) = ?
                AND status_setoran != 'dibatalkan'
            GROUP BY MONTH(tanggal_setoran)
            ORDER BY month
        ", [$this->userData['id'], $year]);
        
        $monthlyData = [];
        foreach ($query->getResultArray() as $row) {
            $monthlyData[(int)$row['month']] = (float)$row['total'];
        }
        
        // Fill missing months with 0
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $result[] = $monthlyData[$i] ?? 0;
        }
        
        return $this->jsonResponse($result);
    }

    /**
     * Print receipt
     */
    public function printReceipt($id)
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();

        $setoran = $setoranModel->find($id);

        if (!$setoran || $setoran['user_id'] != $this->userData['id']) {
            return redirect()->to('/riwayat')->with('error', 'Setoran tidak ditemukan.');
        }

        $periode = $periodeModel->find($setoran['periode_id']);

        $data = [
            'title' => 'Kwitansi Setoran',
            'setoran' => $setoran,
            'periode' => $periode,
            'print_date' => date('Y-m-d H:i:s'),
        ];

        // Set print layout
        return view('riwayat/print_receipt', $data);
    }
}