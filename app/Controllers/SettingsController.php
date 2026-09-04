<?php

namespace App\Controllers;

use App\Models\UserModel;

class SettingsController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display Admin & System Settings
     */
    public function index()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title'        => 'Pengaturan Sistem & Admin',
            'user'         => $this->userData,
            'app_name'     => 'Sistem Manajemen Data Setoran Iuran Terpadu',
            'org_name'     => 'Manajemen Setoran',
            'contact_email'=> 'admin@setoran.org',
            'contact_phone'=> '081234567890',
        ];

        return $this->render('admin/settings/index', $data);
    }

    /**
     * Update Settings
     */
    public function update()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $nama          = $this->request->getPost('nama');
        $email         = $this->request->getPost('email');
        $nomorWhatsapp = $this->request->getPost('nomor_whatsapp');

        $rules = [
            'nama'           => 'required|min_length[3]|max_length[100]',
            'email'          => "required|valid_email|is_unique[users.email,id,{$this->userData['id']}]",
            'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama'           => $nama,
            'email'          => $email,
            'nomor_whatsapp' => $nomorWhatsapp,
        ];

        $this->userModel->update($this->userData['id'], $updateData);

        $this->logActivity('Memperbarui pengaturan admin', $this->userData, $updateData);

        return redirect()->to('/admin/settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
