<?php

namespace App\Controllers;

use App\Models\AcaraModel;
use App\Models\AcaraWargaModel;
use App\Models\UserModel;

class AcaraController extends BaseController
{
    protected $acaraModel;
    protected $acaraWargaModel;
    protected $userModel;

    public function __construct()
    {
        $this->acaraModel      = new AcaraModel();
        $this->acaraWargaModel = new AcaraWargaModel();
        $this->userModel       = new UserModel();
    }

    /**
     * Index - Display Event Settings Page
     */
    public function index()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $event   = $this->acaraModel->getActiveEvent();
        $summary = $this->acaraModel->getEventSummary($event['id']);

        // Auto sync warga to event if empty
        $this->acaraWargaModel->syncAllWargaToEvent($event['id'], $event['tarif_default']);

        $search       = $this->request->getGet('search');
        $statusFilter = $this->request->getGet('status_wajib');
        $wargaList    = $this->acaraWargaModel->getWargaListForEvent($event['id'], $search, $statusFilter);

        $data = [
            'title'        => 'Pengaturan Acara',
            'event'        => $event,
            'summary'      => $summary,
            'wargaList'    => $wargaList,
            'activeTab'    => $this->request->getGet('tab') ?? 'info',
            'search'       => $search,
            'statusFilter' => $statusFilter,
        ];

        return $this->render('admin/acara/index', $data);
    }

    /**
     * Update Event Basic Information
     */
    public function updateInfo()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $eventId = $this->request->getPost('event_id');
        $event   = $this->acaraModel->find($eventId);

        if (!$event) {
            return redirect()->to('/admin/acara')->with('error', 'Acara tidak ditemukan.');
        }

        $rules = [
            'nama_acara'          => 'required|min_length[3]|max_length[255]',
            'tanggal_pelaksanaan' => 'required|valid_date',
            'lokasi'              => 'required|max_length[255]',
            'status'              => 'required|in_list[perencanaan,aktif,selesai,nonaktif]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/acara?tab=info')->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama_acara'          => $this->request->getPost('nama_acara'),
            'tanggal_pelaksanaan' => $this->request->getPost('tanggal_pelaksanaan'),
            'lokasi'              => $this->request->getPost('lokasi'),
            'deskripsi'           => $this->request->getPost('deskripsi'),
            'status'              => $this->request->getPost('status'),
        ];

        $this->acaraModel->update($eventId, $updateData);
        $this->logActivity('Memperbarui Informasi Acara', $event, $updateData);

        return redirect()->to('/admin/acara?tab=info')->with('success', 'Informasi acara berhasil diperbarui.');
    }

    /**
     * Update Target & Fee Scheme
     */
    public function updateSkema()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $eventId = $this->request->getPost('event_id');
        $event   = $this->acaraModel->find($eventId);

        if (!$event) {
            return redirect()->to('/admin/acara')->with('error', 'Acara tidak ditemukan.');
        }

        $rules = [
            'target_total'        => 'required|numeric|greater_than_equal_to[0]',
            'skema_tarif'         => 'required|in_list[flat,tiered]',
            'tarif_default'       => 'required|numeric|greater_than_equal_to[0]',
            'deadline_pembayaran' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/admin/acara?tab=skema')->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'target_total'        => $this->request->getPost('target_total'),
            'skema_tarif'         => $this->request->getPost('skema_tarif'),
            'tarif_default'       => $this->request->getPost('tarif_default'),
            'deadline_pembayaran' => $this->request->getPost('deadline_pembayaran'),
        ];

        $this->acaraModel->update($eventId, $updateData);

        // Option to apply new default tariff to all regular citizens
        if ($this->request->getPost('apply_all_warga') == '1') {
            $db = db_connect();
            $db->table('acara_warga')
                ->where('acara_id', $eventId)
                ->update(['nominal_kewajiban' => $updateData['tarif_default']]);
        }

        $this->logActivity('Memperbarui Target & Skema Iuran Acara', $event, $updateData);

        return redirect()->to('/admin/acara?tab=skema')->with('success', 'Target & skema iuran acara berhasil diperbarui.');
    }

    /**
     * Toggle Citizen Obligation Status (wajib / bebas) via AJAX
     */
    public function toggleWajib()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $eventId = $this->request->getPost('event_id');
        $userId  = $this->request->getPost('user_id');

        $acaraWarga = $this->acaraWargaModel->where('acara_id', $eventId)->where('user_id', $userId)->first();

        if ($acaraWarga) {
            $newStatus = $acaraWarga['status_wajib'] === 'wajib' ? 'bebas' : 'wajib';
            $this->acaraWargaModel->update($acaraWarga['id'], ['status_wajib' => $newStatus]);
        } else {
            $event     = $this->acaraModel->find($eventId);
            $newStatus = 'wajib';
            $this->acaraWargaModel->insert([
                'acara_id'          => $eventId,
                'user_id'           => $userId,
                'kategori_warga'    => 'Warga Reguler',
                'nominal_kewajiban' => $event['tarif_default'] ?? 200000.00,
                'status_wajib'      => $newStatus,
            ]);
        }

        $this->logActivity('Ubah Status Wajib Iuran Warga', ['user_id' => $userId], ['new_status' => $newStatus]);

        return $this->response->setJSON([
            'status'     => 'success',
            'new_status' => $newStatus,
            'message'    => 'Status kewajiban iuran warga berhasil diperbarui.',
        ]);
    }

    /**
     * Update Citizen Specific Tariff / Category via AJAX
     */
    public function updateWargaTarif()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $eventId          = $this->request->getPost('event_id');
        $userId           = $this->request->getPost('user_id');
        $kategori         = $this->request->getPost('kategori_warga');
        $nominalKewajiban = $this->request->getPost('nominal_kewajiban');

        $acaraWarga = $this->acaraWargaModel->where('acara_id', $eventId)->where('user_id', $userId)->first();

        if ($acaraWarga) {
            $this->acaraWargaModel->update($acaraWarga['id'], [
                'kategori_warga'    => $kategori,
                'nominal_kewajiban' => $nominalKewajiban,
            ]);
        } else {
            $this->acaraWargaModel->insert([
                'acara_id'          => $eventId,
                'user_id'           => $userId,
                'kategori_warga'    => $kategori,
                'nominal_kewajiban' => $nominalKewajiban,
                'status_wajib'      => 'wajib',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Tarif dan kategori warga berhasil diperbarui.',
        ]);
    }

    /**
     * Synchronize All Citizens
     */
    public function syncWarga($eventId)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $event = $this->acaraModel->find($eventId);
        if ($event) {
            $this->acaraWargaModel->syncAllWargaToEvent($eventId, $event['tarif_default']);
        }

        return redirect()->to('/admin/acara?tab=warga')->with('success', 'Daftar warga berhasil disinkronisasi.');
    }
}