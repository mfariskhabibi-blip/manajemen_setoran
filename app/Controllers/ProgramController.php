<?php

namespace App\Controllers;

use App\Models\ProgramModel;
use App\Models\PesertaProgramModel;
use App\Models\PeriodeModel;
use App\Models\UserModel;
use App\Models\ActivityLogModel;

class ProgramController extends BaseController
{
    protected $programModel;
    protected $pesertaProgramModel;
    protected $periodeModel;
    protected $userModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
        $this->pesertaProgramModel = new PesertaProgramModel();
        $this->periodeModel = new PeriodeModel();
        $this->userModel = new UserModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin()
    {
        $session = session();
        return $session->get('role') === 'admin';
    }

    /**
     * Index - List all programs
     */
    public function index()
    {
        if ($this->isAdmin()) {
            // Admin view
            $programs = $this->programModel->getAllWithStats();
            
            $data = [
                'title' => 'Program Iuran',
                'programs' => $programs,
                'stats' => $this->programModel->getProgramStats(),
            ];

            return $this->render('program/index', $data);
        } else {
            // User view
            $session = session();
            $userPrograms = $this->programModel->getProgramsForUser($session->get('user_id'));
            
            $data = [
                'title' => 'Program Iuran Saya',
                'userPrograms' => $userPrograms,
            ];

            return $this->render('program/user_index', $data);
        }
    }

    /**
     * Create new program
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Tambah Program Iuran',
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('program/create', $data);
    }

    /**
     * Store new program
     */
    public function store()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $rules = [
            'kode_program' => 'required|min_length[3]|max_length[50]|is_unique[programs.kode_program]',
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'tujuan' => 'required',
            'target_dana' => 'required|numeric|greater_than_equal_to[0]',
            'kewajiban_default' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'kode_program' => $this->request->getPost('kode_program'),
            'nama_program' => $this->request->getPost('nama_program'),
            'tujuan' => $this->request->getPost('tujuan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'target_dana' => $this->request->getPost('target_dana'),
            'kewajiban_default' => $this->request->getPost('kewajiban_default'),
            'status' => 'aktif',
        ];

        $programId = $this->programModel->insert($data);

        // Log activity
        $this->logActivity('Menambahkan Program', null, json_encode($data));

        return redirect()->to('/program')->with('success', 'Program iuran berhasil dibuat');
    }

    /**
     * Show program detail
     */
    public function show($id)
    {
        $program = $this->programModel->find($id);
        if (!$program) {
            return redirect()->to('/program')->with('error', 'Program tidak ditemukan');
        }

        if ($this->isAdmin()) {
            // Admin view
            $program = $this->programModel->getProgramWithStats($id);
            $periodes = $this->periodeModel->getByProgram($id);
            $participants = $this->pesertaProgramModel->getByProgram($id);

            $data = [
                'title' => 'Detail Program Iuran',
                'program' => $program,
                'periodes' => $periodes,
                'participants' => $participants,
            ];

            return $this->render('program/show', $data);
        } else {
            // User view
            $session = session();
            $userId = $session->get('user_id');
            
            // Check if user is participant
            if (!$this->programModel->isUserParticipant($id, $userId)) {
                return redirect()->to('/program')->with('error', 'Anda tidak memiliki akses ke program ini');
            }

            $periodes = $this->periodeModel->getByProgram($id);
            $summary = $this->pesertaProgramModel->getUserProgramSummary($userId, $id);

            $data = [
                'title' => 'Detail Program Iuran',
                'program' => $program,
                'periodes' => $periodes,
                'summary' => $summary,
                'periodeModel' => $this->periodeModel,
            ];

            return $this->render('program/user_show', $data);
        }
    }

    /**
     * Edit program
     */
    public function edit($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $program = $this->programModel->find($id);
        if (!$program) {
            return redirect()->to('/program')->with('error', 'Program tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Program Iuran',
            'program' => $program,
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('program/edit', $data);
    }

    /**
     * Update program
     */
    public function update($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $program = $this->programModel->find($id);
        if (!$program) {
            return redirect()->to('/program')->with('error', 'Program tidak ditemukan');
        }

        $rules = [
            'kode_program' => "required|min_length[3]|max_length[50]|is_unique[programs.kode_program,id,{$id}]",
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'tujuan' => 'required',
            'target_dana' => 'required|numeric|greater_than_equal_to[0]',
            'kewajiban_default' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $oldData = $program;
        $newData = [
            'kode_program' => $this->request->getPost('kode_program'),
            'nama_program' => $this->request->getPost('nama_program'),
            'tujuan' => $this->request->getPost('tujuan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'target_dana' => $this->request->getPost('target_dana'),
            'kewajiban_default' => $this->request->getPost('kewajiban_default'),
            'status' => $this->request->getPost('status'),
        ];

        $this->programModel->update($id, $newData);

        // Log activity
        $this->logActivity('Mengubah Program', $oldData, $newData);

        return redirect()->to('/program')->with('success', 'Program iuran berhasil diperbarui');
    }

    /**
     * Delete program
     */
    public function delete($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $program = $this->programModel->find($id);
        if (!$program) {
            return redirect()->to('/program')->with('error', 'Program tidak ditemukan');
        }

        // Check if program has participants
        $participantsCount = $this->pesertaProgramModel->getParticipantsCount($id);
        if ($participantsCount > 0) {
            return redirect()->to('/program')->with('error', 'Program tidak dapat dihapus karena masih memiliki peserta');
        }

        $this->programModel->delete($id);

        // Log activity
        $this->logActivity('Menghapus Program', $program, null);

        return redirect()->to('/program')->with('success', 'Program iuran berhasil dihapus');
    }

    /**
     * Add participant to program
     */
    public function addParticipant($programId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $users = $this->userModel->getActiveUsers();
        $program = $this->programModel->find($programId);

        $data = [
            'title' => 'Tambah Peserta Program',
            'program' => $program,
            'users' => $users,
            'periodes' => $this->periodeModel->getByProgram($programId),
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('program/add_participant', $data);
    }

    /**
     * Store participant
     */
    public function storeParticipant()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $rules = [
            'program_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'total_kewajiban' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $programId = $this->request->getPost('program_id');
        $userId = $this->request->getPost('user_id');
        $periodeId = $this->request->getPost('periode_id');
        $periodeId = ($periodeId && $periodeId != '0') ? $periodeId : null;
        $totalKewajiban = $this->request->getPost('total_kewajiban');

        $result = $this->pesertaProgramModel->addUserToProgram($programId, $userId, $totalKewajiban, $periodeId);

        if (!$result) {
            return redirect()->back()->with('error', 'Pengguna sudah terdaftar sebagai peserta program ini');
        }

        // Log activity
        $this->logActivity('Menambahkan Peserta Program', null, [
            'program_id' => $programId,
            'user_id' => $userId,
            'total_kewajiban' => $totalKewajiban,
        ]);

        return redirect()->to("/program/show/{$programId}")->with('success', 'Peserta berhasil ditambahkan');
    }

    /**
     * Remove participant from program
     */
    public function removeParticipant($programId, $userId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $this->pesertaProgramModel->removeUserFromProgram($programId, $userId);

        // Log activity
        $this->logActivity('Menghapus Peserta Program', [
            'program_id' => $programId,
            'user_id' => $userId,
        ], null);

        return redirect()->to("/program/show/{$programId}")->with('success', 'Peserta berhasil dihapus dari program');
    }

    /**
     * Add periode to program
     */
    public function addPeriode($programId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $program = $this->programModel->find($programId);
        if (!$program) {
            return redirect()->to('/program')->with('error', 'Program tidak ditemukan');
        }

        $data = [
            'title' => 'Tambah Periode Program',
            'program' => $program,
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('program/add_periode', $data);
    }

    /**
     * Store periode
     */
    public function storePeriode()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $rules = [
            'program_id' => 'required|numeric',
            'nama_periode' => 'required|min_length[3]|max_length[100]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
            'nominal_kewajiban' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $programId = $this->request->getPost('program_id');
        $namaPeriode = $this->request->getPost('nama_periode');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $nominalKewajiban = $this->request->getPost('nominal_kewajiban');

        // Check if periode dates overlap with existing periodes in the same program
        if ($this->periodeModel->checkOverlap($programId, $tanggalMulai, $tanggalSelesai)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal periode bertabrakan dengan periode yang sudah ada di program ini');
        }

        $data = [
            'program_id' => $programId,
            'nama_periode' => $namaPeriode,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'nominal_kewajiban' => $nominalKewajiban,
            'status' => 'belum_aktif',
        ];

        $this->periodeModel->insert($data);

        // Log activity
        $this->logActivity('Menambahkan Periode Program', null, [
            'program_id' => $programId,
            'nama_periode' => $namaPeriode,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'nominal_kewajiban' => $nominalKewajiban,
        ]);

        return redirect()->to("/program/show/{$programId}")->with('success', 'Periode berhasil ditambahkan');
    }

    /**
     * Delete periode from program
     */
    public function deletePeriode($programId, $periodeId)
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $periode = $this->periodeModel->find($periodeId);
        
        if (!$periode || $periode['program_id'] != $programId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Periode tidak ditemukan']);
        }

        // Check if periode has setoran
        $setoranModel = new \App\Models\SetoranModel();
        $hasSetoran = $setoranModel->where('periode_id', $periodeId)
                                  ->where('status_setoran !=', 'dibatalkan')
                                  ->countAllResults() > 0;

        if ($hasSetoran) {
            return $this->response->setJSON(['success' => false, 'message' => 'Periode tidak dapat dihapus karena sudah memiliki setoran']);
        }

        $this->periodeModel->delete($periodeId);

        // Log activity
        $this->logActivity('Menghapus Periode Program', [
            'program_id' => $programId,
            'periode_id' => $periodeId,
            'nama_periode' => $periode['nama_periode'],
        ], null);

        return $this->response->setJSON(['success' => true, 'message' => 'Periode berhasil dihapus']);
    }

    /**
     * Update periode status
     */
    public function updatePeriodeStatus($programId, $periodeId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $periode = $this->periodeModel->find($periodeId);
        
        if (!$periode || $periode['program_id'] != $programId) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan');
        }

        $newStatus = $this->request->getPost('status');
        
        if (!in_array($newStatus, ['belum_aktif', 'aktif', 'selesai'])) {
            return redirect()->back()->with('error', 'Status tidak valid');
        }

        $this->periodeModel->update($periodeId, ['status' => $newStatus]);

        // Log activity
        $this->logActivity('Mengubah Status Periode Program', [
            'program_id' => $programId,
            'periode_id' => $periodeId,
            'old_status' => $periode['status'],
        ], [
            'new_status' => $newStatus,
        ]);

        return redirect()->to("/admin/program/{$programId}")->with('success', 'Status periode berhasil diperbarui');
    }
}
