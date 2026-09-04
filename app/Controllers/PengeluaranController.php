<?php

namespace App\Controllers;

use App\Models\PengeluaranModel;
use App\Models\SetoranModel;

class PengeluaranController extends BaseController
{
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
    }

    /**
     * Index view for both Admin and User
     */
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $isAdmin = ($this->userData['role'] === 'admin');

        // Fetch pengeluaran records
        $pengeluaranList = $this->pengeluaranModel->getWithAdmin();

        // Calculate totals
        $totalPengeluaran = $this->pengeluaranModel->getTotalPengeluaran();

        // Calculate total setoran terkumpul
        $setoranModel = new SetoranModel();
        $setoranStats = $setoranModel->getSetoranStats();
        $totalSetoran = $setoranStats['total_setoran'] ?? 0;

        // Saldo kas net
        $saldoKas = $totalSetoran - $totalPengeluaran;

        $data = [
            'title'            => 'Pengeluaran Kas',
            'pengeluaranList'  => $pengeluaranList,
            'totalPengeluaran' => $totalPengeluaran,
            'totalSetoran'     => $totalSetoran,
            'saldoKas'         => $saldoKas,
            'isAdmin'          => $isAdmin,
        ];

        return $this->render('pengeluaran/index', $data);
    }

    /**
     * Create view (Admin only)
     */
    public function create()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Tambah Catatan Pengeluaran',
        ];

        return $this->render('pengeluaran/create', $data);
    }

    /**
     * Store new pengeluaran (Admin only)
     */
    public function store()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $post = $this->request->getPost();

        // Validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal'    => 'required|valid_date',
            'kategori'   => 'permit_empty|max_length[100]',
            'keterangan' => 'required|min_length[3]',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'file_bukti' => 'permit_empty|max_size[file_bukti,10240]|ext_in[file_bukti,pdf,jpg,jpeg,png,webp,doc,docx]',
        ]);

        if (!$validation->run($post)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $filePath = null;
        $file = $this->request->getFile('file_bukti');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/pengeluaran/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $filePath = 'uploads/pengeluaran/' . $newName;
        }

        $insertData = [
            'admin_id'   => $this->userData['id'],
            'tanggal'    => $post['tanggal'],
            'kategori'   => !empty($post['kategori']) ? $post['kategori'] : 'Umum',
            'keterangan' => $post['keterangan'],
            'jumlah'     => (float)$post['jumlah'],
            'file_path'  => $filePath,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->pengeluaranModel->insert($insertData)) {
            $this->logActivity('Menambahkan pengeluaran kas baru', null, $insertData);
            
            $redirectUrl = ($this->userData['role'] === 'admin') ? '/admin/pengeluaran' : '/pengeluaran';
            return redirect()->to($redirectUrl)->with('success', 'Catatan pengeluaran berhasil disimpan!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan catatan pengeluaran.');
    }

    /**
     * Edit view (Admin only)
     */
    public function edit($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $record = $this->pengeluaranModel->find($id);
        if (!$record) {
            return redirect()->to('/admin/pengeluaran')->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Pengeluaran Kas',
            'pengeluaran' => $record,
        ];

        return $this->render('pengeluaran/edit', $data);
    }

    /**
     * Update record (Admin only)
     */
    public function update($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $record = $this->pengeluaranModel->find($id);
        if (!$record) {
            return redirect()->to('/admin/pengeluaran')->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        $post = $this->request->getPost();

        $validation = \Config\Services::validation();
        $validation->setRules([
            'tanggal'    => 'required|valid_date',
            'kategori'   => 'permit_empty|max_length[100]',
            'keterangan' => 'required|min_length[3]',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'file_bukti' => 'permit_empty|max_size[file_bukti,10240]|ext_in[file_bukti,pdf,jpg,jpeg,png,webp,doc,docx]',
        ]);

        if (!$validation->run($post)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $filePath = $record['file_path'];
        $file = $this->request->getFile('file_bukti');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/pengeluaran/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Remove old file if exists
            if (!empty($record['file_path']) && file_exists(FCPATH . $record['file_path'])) {
                @unlink(FCPATH . $record['file_path']);
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $filePath = 'uploads/pengeluaran/' . $newName;
        }

        $updateData = [
            'tanggal'    => $post['tanggal'],
            'kategori'   => !empty($post['kategori']) ? $post['kategori'] : 'Umum',
            'keterangan' => $post['keterangan'],
            'jumlah'     => (float)$post['jumlah'],
            'file_path'  => $filePath,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->pengeluaranModel->update($id, $updateData)) {
            $this->logActivity('Memperbarui pengeluaran kas', $record, $updateData);
            $redirectTo = $this->request->getPost('redirect_to');
            if ($redirectTo && strpos($redirectTo, base_url()) !== false) {
                return redirect()->to($redirectTo)->with('success', 'Catatan pengeluaran berhasil diperbarui!');
            }
            return redirect()->back()->with('success', 'Catatan pengeluaran berhasil diperbarui!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengeluaran.');
    }

    /**
     * Delete record (Admin only)
     */
    public function delete($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            if ($this->isAjax()) {
                return $this->jsonResponse([], 403, 'Akses ditolak.');
            }
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $record = $this->pengeluaranModel->find($id);
        if (!$record) {
            if ($this->isAjax()) {
                return $this->jsonResponse([], 404, 'Data tidak ditemukan.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Delete file if exists
        if (!empty($record['file_path']) && file_exists(FCPATH . $record['file_path'])) {
            @unlink(FCPATH . $record['file_path']);
        }

        if ($this->pengeluaranModel->delete($id)) {
            $this->logActivity('Menghapus pengeluaran kas', $record, null);
            if ($this->isAjax()) {
                return $this->jsonResponse([], 200, 'Data pengeluaran berhasil dihapus.');
            }
            return redirect()->back()->with('success', 'Data pengeluaran berhasil dihapus.');
        }

        if ($this->isAjax()) {
            return $this->jsonResponse([], 500, 'Gagal menghapus data.');
        }
        return redirect()->back()->with('error', 'Gagal menghapus data.');
    }

    /**
     * Download attached file (Both roles)
     */
    public function download($id)
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $record = $this->pengeluaranModel->find($id);
        if (!$record || empty($record['file_path'])) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fullPath = FCPATH . $record['file_path'];
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'File fisik tidak ditemukan pada server.');
        }

        return $this->response->download($fullPath, null);
    }
}
