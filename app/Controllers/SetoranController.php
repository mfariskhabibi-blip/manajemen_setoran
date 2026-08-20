<?php

namespace App\Controllers;

class SetoranController extends BaseController
{
    /**
     * List setoran for user
     */
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();

        // Get filter parameters
        $periodeId = $this->request->getGet('periode');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        // Build query
        $query = $setoranModel->where('user_id', $this->userData['id'])
                             ->where('status_setoran !=', 'dibatalkan');

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($status && in_array($status, ['tercatat', 'diverifikasi', 'dikoreksi'])) {
            $query->where('status_setoran', $status);
        }

        if ($search) {
            $query->groupStart()
                 ->like('keterangan', $search)
                 ->groupEnd();
        }

        // Get setoran with pagination
        $perPage = 10;
        $currentPage = $this->request->getGet('page') ?? 1;
        
        $totalRows = $query->countAllResults(false);
        $setoran = $query->orderBy('tanggal_setoran', 'DESC')
                        ->paginate($perPage, 'default', $currentPage);
        
        $pager = $setoranModel->pager;

        // Get all periodes for filter
        $periodes = $periodeModel->findAll();

        // Get statistics
        $stats = $setoranModel->getUserSummary($this->userData['id']);

        $data = [
            'title' => 'Setoran Iuran',
            'setoran' => $setoran,
            'pager' => $pager,
            'periodes' => $periodes,
            'stats' => $stats,
            'filters' => [
                'periode' => $periodeId,
                'status' => $status,
                'search' => $search,
            ],
        ];

        return $this->render('setoran/index', $data);
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

        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return redirect()->to('/setoran')->with('error', 'Setoran tidak ditemukan.');
        }

        // Check if user owns this setoran (unless admin)
        if ($this->userData['role'] !== 'admin' && $setoran['user_id'] != $this->userData['id']) {
            return redirect()->to('/setoran')->with('error', 'Anda tidak memiliki akses ke setoran ini.');
        }

        $periode = $periodeModel->find($setoran['periode_id']);

        $data = [
            'title' => 'Detail Setoran',
            'setoran' => $setoran,
            'periode' => $periode,
        ];

        return $this->render('setoran/detail', $data);
    }

    /**
     * Create setoran form (admin only)
     */
    public function create()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userModel = new \App\Models\UserModel();
        $periodeModel = new \App\Models\PeriodeModel();

        $data = [
            'title' => 'Tambah Setoran',
            'users' => $userModel->getActiveUsers(),
            'periodes' => $periodeModel->where('status', 'aktif')->findAll(),
        ];

        return $this->render('setoran/create', $data);
    }

    /**
     * Store setoran (admin only)
     */
    public function store()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request.']);
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'user_id' => 'required|numeric',
            'periode_id' => 'required|numeric',
            'tanggal_setoran' => 'required|valid_date',
            'nominal' => 'required|numeric|greater_than[0]',
            'status_setoran' => 'required|in_list[tercatat,diverifikasi,dikoreksi,dibatalkan]',
            'keterangan' => 'permit_empty|max_length[500]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->jsonResponse([], 422, $validation->getErrors());
        }

        $setoranModel = new \App\Models\SetoranModel();

        // Check if user already has setoran for this periode
        $existing = $setoranModel->where('user_id', $this->request->getPost('user_id'))
                                ->where('periode_id', $this->request->getPost('periode_id'))
                                ->where('status_setoran !=', 'dibatalkan')
                                ->first();

        if ($existing) {
            return $this->jsonResponse([], 400, 'Setoran untuk periode ini sudah tercatat.');
        }

        // Prepare data
        $data = [
            'user_id' => $this->request->getPost('user_id'),
            'periode_id' => $this->request->getPost('periode_id'),
            'tanggal_setoran' => $this->request->getPost('tanggal_setoran'),
            'nominal' => $this->request->getPost('nominal'),
            'status_setoran' => $this->request->getPost('status_setoran'),
            'keterangan' => $this->request->getPost('keterangan'),
            'created_by' => $this->userData['id'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->insert($data)) {
            // Log activity
            $this->logActivity('Menambahkan setoran', null, $data);
            
            return $this->jsonResponse([
                'id' => $setoranModel->getInsertID()
            ], 201, 'Setoran berhasil ditambahkan.');
        }

        return $this->jsonResponse([], 500, 'Gagal menambahkan setoran.');
    }

    /**
     * Edit setoran form (admin only)
     */
    public function edit($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $userModel = new \App\Models\UserModel();
        $periodeModel = new \App\Models\PeriodeModel();

        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return redirect()->to('/admin/setoran')->with('error', 'Setoran tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Setoran',
            'setoran' => $setoran,
            'users' => $userModel->getActiveUsers(),
            'periodes' => $periodeModel->findAll(),
        ];

        return $this->render('setoran/edit', $data);
    }

    /**
     * Update setoran (admin only)
     */
    public function update($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request.']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return $this->jsonResponse([], 404, 'Setoran tidak ditemukan.');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'periode_id' => 'required|numeric',
            'tanggal_setoran' => 'required|valid_date',
            'nominal' => 'required|numeric|greater_than[0]',
            'status_setoran' => 'required|in_list[tercatat,diverifikasi,dikoreksi,dibatalkan]',
            'keterangan' => 'permit_empty|max_length[500]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return $this->jsonResponse([], 422, $validation->getErrors());
        }

        // Check for duplicate (excluding current setoran)
        $existing = $setoranModel->where('user_id', $setoran['user_id'])
                                ->where('periode_id', $this->request->getPost('periode_id'))
                                ->where('status_setoran !=', 'dibatalkan')
                                ->where('id !=', $id)
                                ->first();

        if ($existing) {
            return $this->jsonResponse([], 400, 'Setoran untuk periode ini sudah tercatat.');
        }

        // Prepare data
        $oldData = $setoran;
        $newData = [
            'periode_id' => $this->request->getPost('periode_id'),
            'tanggal_setoran' => $this->request->getPost('tanggal_setoran'),
            'nominal' => $this->request->getPost('nominal'),
            'status_setoran' => $this->request->getPost('status_setoran'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->update($id, $newData)) {
            // Log activity
            $this->logActivity('Mengedit setoran', $oldData, $newData);
            
            return $this->jsonResponse([], 200, 'Setoran berhasil diperbarui.');
        }

        return $this->jsonResponse([], 500, 'Gagal memperbarui setoran.');
    }

    /**
     * Delete setoran (admin only)
     */
    public function delete($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request.']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return $this->jsonResponse([], 404, 'Setoran tidak ditemukan.');
        }

        // Soft delete (change status to dibatalkan)
        $data = [
            'status_setoran' => 'dibatalkan',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->update($id, $data)) {
            // Log activity
            $this->logActivity('Membatalkan setoran', $setoran, $data);
            
            return $this->jsonResponse([], 200, 'Setoran berhasil dibatalkan.');
        }

        return $this->jsonResponse([], 500, 'Gagal membatalkan setoran.');
    }

    /**
     * Verify setoran (admin only)
     */
    public function verify($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request.']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return $this->jsonResponse([], 404, 'Setoran tidak ditemukan.');
        }

        $oldStatus = $setoran['status_setoran'];
        $newStatus = $this->request->getPost('status') === 'verify' ? 'diverifikasi' : 'tercatat';

        $data = [
            'status_setoran' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->update($id, $data)) {
            // Log activity
            $this->logActivity('Memverifikasi setoran', ['old_status' => $oldStatus], ['new_status' => $newStatus]);
            
            return $this->jsonResponse([], 200, 'Status setoran berhasil diperbarui.');
        }

        return $this->jsonResponse([], 500, 'Gagal memperbarui status setoran.');
    }

    /**
     * Export setoran (admin only)
     */
    public function export()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $userModel = new \App\Models\UserModel();

        // Get filter parameters
        $periodeId = $this->request->getGet('periode');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Build query
        $query = $setoranModel->where('status_setoran !=', 'dibatalkan');

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($startDate && $endDate) {
            $query->where('tanggal_setoran >=', $startDate)
                 ->where('tanggal_setoran <=', $endDate);
        }

        $setoran = $query->orderBy('tanggal_setoran', 'DESC')->findAll();

        // Get periodes for filter
        $periodes = $periodeModel->findAll();

        $data = [
            'title' => 'Ekspor Setoran',
            'setoran' => $setoran,
            'periodes' => $periodes,
            'filters' => [
                'periode' => $periodeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        return $this->render('setoran/export', $data);
    }

    /**
     * Generate report (admin only)
     */
    public function generateReport()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Bad request.']);
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();

        $periodeId = $this->request->getPost('periode_id');
        $format = $this->request->getPost('format');

        // Get data
        $setoran = $setoranModel->where('status_setoran !=', 'dibatalkan');

        if ($periodeId) {
            $setoran->where('periode_id', $periodeId);
        }

        $setoran = $setoran->findAll();

        if (empty($setoran)) {
            return $this->jsonResponse([], 404, 'Tidak ada data setoran untuk diekspor.');
        }

        // Generate report data
        $reportData = [
            'periode' => $periodeId ? $periodeModel->find($periodeId) : null,
            'total_setoran' => array_sum(array_column($setoran, 'nominal')),
            'total_transactions' => count($setoran),
            'transactions' => $setoran,
            'generated_at' => date('Y-m-d H:i:s'),
            'generated_by' => $this->userData['nama'],
        ];

        // Log activity
        $this->logActivity('Membuat laporan setoran', null, [
            'periode_id' => $periodeId,
            'format' => $format,
            'total_records' => count($setoran)
        ]);

        return $this->jsonResponse($reportData, 200, 'Laporan berhasil dibuat.');
    }
}