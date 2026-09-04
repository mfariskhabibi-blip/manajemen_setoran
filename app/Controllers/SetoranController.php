<?php

namespace App\Controllers;

class SetoranController extends BaseController
{
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $setoranModel = new \App\Models\SetoranModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $userModel = new \App\Models\UserModel();
        $programModel = new \App\Models\ProgramModel();
        $pesertaProgramModel = new \App\Models\PesertaProgramModel();

        // Check if admin route
        $isAdminRoute = ($this->request->getUri()->getSegment(1) === 'admin');
        if ($isAdminRoute && $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Get filter parameters
        $programId = $this->request->getGet('program');
        $periodeId = $this->request->getGet('periode');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search'); // Used for Keterangan OR user_name in admin
        $userIdFilter = $this->request->getGet('user_id'); // Admin only
        $startDate = $this->request->getGet('start_date'); // Admin only
        $endDate = $this->request->getGet('end_date'); // Admin only

        // Build query
        $query = $setoranModel->select('setoran.*, u.nama as user_name, u.nomor_whatsapp as user_wa, a.nama as admin_name, p.nama_program, p.kode_program')
                              ->join('users u', 'u.id = setoran.user_id', 'left')
                              ->join('users a', 'a.id = setoran.created_by', 'left')
                              ->join('programs p', 'p.id = setoran.program_id', 'left');

        if (!$isAdminRoute) {
            $query->where('setoran.user_id', $this->userData['id'])
                  ->where('setoran.status_setoran !=', 'dibatalkan');
        } else {
            // Admin filters
            if ($userIdFilter) {
                $query->where('setoran.user_id', $userIdFilter);
            }
            if ($startDate && $endDate) {
                $query->where('setoran.tanggal_setoran >=', $startDate)
                      ->where('setoran.tanggal_setoran <=', $endDate);
            }
            if ($search) {
                $query->groupStart()
                     ->like('setoran.keterangan', $search)
                     ->orLike('u.nama', $search)
                     ->groupEnd();
            }
        }

        if ($programId) {
            $query->where('setoran.program_id', $programId);
        }

        if ($periodeId) {
            $query->where('setoran.periode_id', $periodeId);
        }

        if ($status && in_array($status, ['tercatat', 'diverifikasi', 'dikoreksi', 'dibatalkan'])) {
            $query->where('setoran.status_setoran', $status);
        }

        if (!$isAdminRoute && $search) {
             $query->groupStart()
                 ->like('setoran.keterangan', $search)
                 ->groupEnd();
        }

        // Get setoran with pagination
        $perPage = 10;
        $currentPage = $this->request->getGet('page') ?? 1;
        
        $totalRows = $query->countAllResults(false);
        $setoran = $query->orderBy('setoran.tanggal_setoran', 'DESC')
                        ->paginate($perPage, 'default', $currentPage);
                        
        // Set period names to avoid extra queries in view
        $periodesArr = [];
        $allPeriodes = $periodeModel->findAll();
        foreach($allPeriodes as $p) {
            $periodesArr[$p['id']] = $p['nama_periode'];
        }
        foreach($setoran as &$s) {
            $s['nama_periode'] = $periodesArr[$s['periode_id']] ?? '-';
        }
        
        $pager = $setoranModel->pager;

        $data = [
            'title' => 'Setoran Iuran',
            'setoran' => $setoran,
            'setorans' => $setoran,
            'pager' => $pager,
            'periodes' => $allPeriodes,
            'programs' => $programModel->findAll(),
            'filters' => [
                'program' => $programId,
                'periode' => $periodeId,
                'status' => $status,
                'search' => $search,
                'user_id' => $userIdFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        if ($isAdminRoute) {
            $data['title'] = 'Kelola Setoran';
            $data['users'] = $userModel->where('role', 'user')->findAll();

            $db = db_connect();
            $today = date('Y-m-d');
            
            // Get Active Event
            $acaraModel = new \App\Models\AcaraModel();
            $activeEvent = $acaraModel->getActiveEvent();
            $eventSummary = $acaraModel->getEventSummary($activeEvent['id']);

            $pengeluaranModel = new \App\Models\PengeluaranModel();
            $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();

            $totalTerkumpul = $db->table('setoran')->selectSum('nominal', 'total')->where('status_setoran !=', 'dibatalkan')->get()->getRow()->total ?? 0;
            $todaySetoran = $db->table('setoran')->selectSum('nominal', 'total')->selectCount('id', 'count')->where('tanggal_setoran', $today)->where('status_setoran !=', 'dibatalkan')->get()->getRow();
            $pendingCount = $db->table('setoran')->where('status_setoran', 'tercatat')->countAllResults();
            $wargaCount = $db->table('users')->where('role', 'user')->countAllResults();

            $targetDana = (float)($activeEvent['target_total'] ?? 50000000);
            $saldoKasNet = (float)$totalTerkumpul - $totalPengeluaran;

            $data['activeEvent']      = $activeEvent;
            $data['eventSummary']     = $eventSummary;
            $data['totalPengeluaran'] = $totalPengeluaran;
            $data['saldoKasNet']      = $saldoKasNet;
            $data['progressGross']    = $targetDana > 0 ? min(100, round(((float)$totalTerkumpul / $targetDana) * 100, 1)) : 0;
            $data['progressNet']      = $targetDana > 0 ? max(0, min(100, round(($saldoKasNet / $targetDana) * 100, 1))) : 0;
            $data['adminStats']       = [
                'total_terkumpul' => (float)$totalTerkumpul,
                'total_hari_ini'   => (float)($todaySetoran->total ?? 0),
                'count_hari_ini'   => (int)($todaySetoran->count ?? 0),
                'count_pending'    => (int)$pendingCount,
                'count_warga'      => (int)$wargaCount,
            ];

            return $this->render('setoran/admin_index', $data);
        } else {
            // User view: fetch active event and user summary
            $acaraModel = new \App\Models\AcaraModel();
            $activeEvent = $acaraModel->getActiveEvent();
            $eventSummary = $acaraModel->getEventSummary($activeEvent['id']);

            $pengeluaranModel = new \App\Models\PengeluaranModel();
            $totalPengeluaran = $pengeluaranModel->getTotalPengeluaran();
            $terkumpulAcara   = (float)($eventSummary['total_terkumpul'] ?? 0);
            $saldoKasNet      = $terkumpulAcara - $totalPengeluaran;
            $targetDana       = (float)($activeEvent['target_total'] ?? 50000000);

            $data['activeEvent']      = $activeEvent;
            $data['eventSummary']     = $eventSummary;
            $data['totalPengeluaran'] = $totalPengeluaran;
            $data['saldoKasNet']      = $saldoKasNet;
            $data['progressGross']    = $targetDana > 0 ? min(100, round(($terkumpulAcara / $targetDana) * 100, 1)) : 0;
            $data['progressNet']      = $targetDana > 0 ? max(0, min(100, round(($saldoKasNet / $targetDana) * 100, 1))) : 0;
            $data['stats']            = $setoranModel->getUserSummary($this->userData['id']);
            return $this->render('setoran/index', $data);
        }
    }

    /**
     * Get Warga Event Obligation and Balance via AJAX
     */
    public function getWargaBalance($userId)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $acaraModel = new \App\Models\AcaraModel();
        $event = $acaraModel->getActiveEvent();

        $db = db_connect();
        
        // Fetch specific citizen obligation in active event
        $acaraWarga = $db->table('acara_warga')
            ->where('acara_id', $event['id'])
            ->where('user_id', $userId)
            ->get()->getRowArray();

        $kewajiban = (float)($acaraWarga['nominal_kewajiban'] ?? $event['tarif_default']);
        $statusWajib = $acaraWarga['status_wajib'] ?? 'wajib';

        // Sum setoran for this user
        $sudahSetor = $db->table('setoran')
            ->selectSum('nominal', 'total')
            ->where('user_id', $userId)
            ->where('status_setoran !=', 'dibatalkan')
            ->get()->getRow()->total ?? 0;

        $sudahSetor = (float)$sudahSetor;
        $sisaTagihan = max(0, $kewajiban - $sudahSetor);

        return $this->response->setJSON([
            'status'        => 'success',
            'user_id'       => (int)$userId,
            'event_nama'    => $event['nama_acara'],
            'kewajiban'     => $kewajiban,
            'sudah_setor'   => $sudahSetor,
            'sisa_tagihan'  => $sisaTagihan,
            'status_wajib'  => $statusWajib,
            'is_lunas'      => ($sisaTagihan <= 0 && $statusWajib === 'wajib'),
        ]);
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
        $userModel = new \App\Models\UserModel();

        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return redirect()->to('/setoran')->with('error', 'Setoran tidak ditemukan.');
        }

        // Check if user owns this setoran (unless admin)
        if ($this->userData['role'] !== 'admin' && $setoran['user_id'] != $this->userData['id']) {
            return redirect()->to('/setoran')->with('error', 'Anda tidak memiliki akses ke setoran ini.');
        }

        $periode = $periodeModel->find($setoran['periode_id']);
        $program = $programModel->find($setoran['program_id']);
        $user = $userModel->find($setoran['user_id']);
        $admin = $setoran['created_by'] ? $userModel->find($setoran['created_by']) : null;

        $data = [
            'title' => 'Detail Setoran',
            'setoran' => $setoran,
            'periode' => $periode,
            'program' => $program,
            'user' => $user,
            'admin' => $admin,
            'isAdminRoute' => $this->userData['role'] === 'admin',
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
        $acaraModel = new \App\Models\AcaraModel();

        $activeEvent = $acaraModel->getActiveEvent();

        $data = [
            'title' => 'Tambah Setoran',
            'users' => $userModel->getActiveUsers(),
            'activeEvent' => $activeEvent,
        ];

        return $this->render('setoran/create', $data);
    }

    /**
     * Store setoran (admin only)
     */
    public function store()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $post = $this->request->getPost();
        
        $programModel = new \App\Models\ProgramModel();
        $periodeModel = new \App\Models\PeriodeModel();
        $userModel    = new \App\Models\UserModel();
        $setoranModel = new \App\Models\SetoranModel();
        $acaraModel   = new \App\Models\AcaraModel();

        // 1. Get Active Event
        $activeEvent = $acaraModel->getActiveEvent();
        
        // 2. Auto-select or create Program for active event
        $program = $programModel->where('status', 'aktif')->first();
        if (!$program) {
            // Auto create fallback program if database empty
            $programId = $programModel->insert([
                'nama_program' => 'Iuran Acara Komunitas',
                'deskripsi'    => 'Program Iuran Acara & Kegiatan Warga',
                'target_dana'  => 50000000,
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            $program = $programModel->find($programId);
        }
        $programId = $program['id'];

        // 3. Auto-create or find Periode for the program
        $periode = $periodeModel->where('program_id', $programId)->first();
        if (!$periode) {
            $periodeId = $periodeModel->insert([
                'program_id'   => $programId,
                'nama_periode' => 'Periode Acara 2026',
                'nominal'      => 200000,
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
        } else {
            $periodeId = $periode['id'];
        }

        // 4. Set default values
        $post['tanggal_setoran'] = $post['tanggal_setoran'] ?? date('Y-m-d');
        $post['status_setoran'] = 'diverifikasi'; // Auto-verified for admin input

        // 5. Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'user_id'         => 'required|numeric',
            'tanggal_setoran' => 'required|valid_date',
            'nominal'         => 'required|numeric|greater_than[0]',
            'keterangan'      => 'permit_empty|max_length[500]',
        ]);

        if (!$validation->run($post)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userId = (int)$post['user_id'];
        
        // 6. Validate Target User
        $targetUser = $userModel->find($userId);
        if (!$targetUser || $targetUser['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Pengguna tidak valid atau tidak aktif.');
        }

        // 7. Auto-register participant in peserta_program if not exists
        $db = \Config\Database::connect();
        $peserta = $db->table('peserta_program')
                      ->where('program_id', $programId)
                      ->where('user_id', $userId)
                      ->get()->getRow();
        if (!$peserta) {
            $db->table('peserta_program')->insert([
                'program_id' => $programId,
                'user_id'    => $userId,
                'status'     => 'aktif',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 8. Insert Setoran Data
        $data = [
            'user_id'         => $userId,
            'program_id'      => $programId,
            'acara_id'        => $activeEvent['id'] ?? null,
            'periode_id'      => $periodeId,
            'tanggal_setoran' => $post['tanggal_setoran'],
            'nominal'         => (float)$post['nominal'],
            'status_setoran'  => $post['status_setoran'],
            'keterangan'      => $post['keterangan'] ?? 'Setoran iuran acara',
            'created_by'      => $this->userData['id'],
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->insert($data)) {
            $this->logActivity('Menambahkan setoran manual', null, $data);
            return redirect()->to('admin/setoran')->with('success', 'Setoran berhasil dicatat dan disimpan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan setoran. Silakan coba lagi.');
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
        $programModel = new \App\Models\ProgramModel();

        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            return redirect()->to('/admin/setoran')->with('error', 'Setoran tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Setoran',
            'setoran' => $setoran,
            'users' => $userModel->getActiveUsers(),
            'programs' => $programModel->findAll(),
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
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
            }
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $isAjax = $this->request->isAJAX();

        $setoranModel = new \App\Models\SetoranModel();
        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            if ($isAjax) return $this->jsonResponse([], 404, 'Setoran tidak ditemukan.');
            return redirect()->back()->with('error', 'Setoran tidak ditemukan.');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'program_id' => 'required|numeric',
            'periode_id' => 'required|numeric',
            'tanggal_setoran' => 'required|valid_date',
            'nominal' => 'required|numeric|greater_than[0]',
            'status_setoran' => 'required|in_list[tercatat,diverifikasi,dikoreksi,dibatalkan]',
            'keterangan' => 'permit_empty|max_length[500]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            if ($isAjax) return $this->jsonResponse([], 422, $validation->getErrors());
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $programId = $this->request->getPost('program_id');
        $periodeId = $this->request->getPost('periode_id');
        $programModel = new \App\Models\ProgramModel();
        $periodeModel = new \App\Models\PeriodeModel();
        
        $targetProgram = $programModel->find($programId);
        if (!$targetProgram || $targetProgram['status'] !== 'aktif') {
            if ($isAjax) return $this->jsonResponse([], 400, 'Program tidak valid atau tidak aktif.');
            return redirect()->back()->withInput()->with('error', 'Program tidak valid atau tidak aktif.');
        }
        
        $targetPeriode = $periodeModel->find($periodeId);
        if (!$targetPeriode) {
            if ($isAjax) return $this->jsonResponse([], 400, 'Periode tidak valid.');
            return redirect()->back()->withInput()->with('error', 'Periode tidak valid.');
        }

        // Cek Periode belongs to Program
        if ($targetPeriode['program_id'] != $programId) {
            if ($isAjax) return $this->jsonResponse([], 400, 'Periode tidak sesuai dengan program.');
            return redirect()->back()->withInput()->with('error', 'Periode tidak sesuai dengan program.');
        }

        // Check for duplicate (excluding current setoran)
        $existing = $setoranModel->where('user_id', $setoran['user_id'])
                                ->where('program_id', $programId)
                                ->where('periode_id', $periodeId)
                                ->where('status_setoran !=', 'dibatalkan')
                                ->where('id !=', $id)
                                ->first();

        if ($existing) {
            if ($isAjax) return $this->jsonResponse([], 400, 'Setoran untuk program ' . $targetProgram['nama_program'] . ' periode ' . $targetPeriode['nama_periode'] . ' sudah tercatat.');
            return redirect()->back()->withInput()->with('error', 'Setoran untuk program ' . $targetProgram['nama_program'] . ' periode ' . $targetPeriode['nama_periode'] . ' sudah tercatat.');
        }

        // Prepare data
        $oldData = $setoran;
        $newData = [
            'program_id' => $programId,
            'periode_id' => $periodeId,
            'tanggal_setoran' => $this->request->getPost('tanggal_setoran'),
            'nominal' => $this->request->getPost('nominal'),
            'status_setoran' => $this->request->getPost('status_setoran'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->update($id, $newData)) {
            // Log activity
            $this->logActivity('Mengedit setoran', $oldData, $newData);
            
            if ($isAjax) return $this->jsonResponse([], 200, 'Setoran berhasil diperbarui.');
            $redirectTo = $this->request->getPost('redirect_to');
            if ($redirectTo && strpos($redirectTo, base_url()) !== false) {
                return redirect()->to($redirectTo)->with('success', 'Setoran berhasil diperbarui.');
            }
            return redirect()->back()->with('success', 'Setoran berhasil diperbarui.');
        }

        if ($isAjax) return $this->jsonResponse([], 500, 'Gagal memperbarui setoran.');
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui setoran.');
    }

    /**
     * Delete setoran (admin only)
     */
    public function delete($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses ditolak.']);
            }
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $isAjax = $this->request->isAJAX();

        $setoranModel = new \App\Models\SetoranModel();
        $setoran = $setoranModel->find($id);

        if (!$setoran) {
            if ($isAjax) return $this->jsonResponse([], 404, 'Setoran tidak ditemukan.');
            return redirect()->back()->with('error', 'Setoran tidak ditemukan.');
        }

        // Soft delete (change status to dibatalkan)
        $data = [
            'status_setoran' => 'dibatalkan',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($setoranModel->update($id, $data)) {
            // Log activity
            $this->logActivity('Membatalkan setoran', $setoran, $data);
            
            if ($isAjax) return $this->jsonResponse([], 200, 'Setoran berhasil dibatalkan.');
            return redirect()->back()->with('success', 'Setoran berhasil dibatalkan.');
        }

        if ($isAjax) return $this->jsonResponse([], 500, 'Gagal membatalkan setoran.');
        return redirect()->back()->with('error', 'Gagal membatalkan setoran.');
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
     * Belum Dicatat - Show users who haven't made setoran for current period (admin only)
     */
    public function belumDicatat()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userModel = new \App\Models\UserModel();
        $acaraModel = new \App\Models\AcaraModel();
        $db = \Config\Database::connect();

        $activeEvent = $acaraModel->getActiveEvent();
        $allWarga = $userModel->where('role', 'user')->where('status', 'active')->findAll();

        $belumLunasList = [];

        foreach ($allWarga as $warga) {
            // Fetch specific citizen obligation in active event
            $acaraWarga = $db->table('acara_warga')
                ->where('acara_id', $activeEvent['id'])
                ->where('user_id', $warga['id'])
                ->get()->getRowArray();

            $statusWajib = $acaraWarga['status_wajib'] ?? 'wajib';
            if ($statusWajib === 'bebas') continue; // Skip exempted citizens

            $kewajiban = (float)($acaraWarga['nominal_kewajiban'] ?? $activeEvent['tarif_default']);

            // Sum setoran for this user
            $sudahSetor = $db->table('setoran')
                ->selectSum('nominal', 'total')
                ->where('user_id', $warga['id'])
                ->where('status_setoran !=', 'dibatalkan')
                ->get()->getRow()->total ?? 0;

            $sudahSetor = (float)$sudahSetor;
            $sisaTagihan = max(0, $kewajiban - $sudahSetor);

            if ($sisaTagihan > 0) {
                $belumLunasList[] = [
                    'user'         => $warga,
                    'activeEvent'  => $activeEvent,
                    'kewajiban'    => $kewajiban,
                    'sudah_setor'  => $sudahSetor,
                    'sisa_tagihan' => $sisaTagihan,
                    'status_wajib' => $statusWajib,
                ];
            }
        }

        $data = [
            'title'          => 'Warga Belum Bayar / Belum Lunas',
            'belumLunasList' => $belumLunasList,
            'activeEvent'    => $activeEvent,
        ];

        return $this->render('setoran/belum_dicatat', $data);
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