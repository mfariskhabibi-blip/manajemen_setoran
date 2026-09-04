<?php

namespace App\Controllers;

class ProfileController extends BaseController
{
    /**
     * Show user profile
     */
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->userData['id']);

        $data = [
            'title' => 'Profil Saya',
            'user' => $user,
        ];

        return $this->render('profile/index', $data);
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->userData['id']);

        $data = [
            'title' => 'Edit Profil',
            'user' => $user,
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('profile/edit', $data);
    }

    /**
     * Update profile
     */
    public function update()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->userData['id']);

        if (!$user) {
            return redirect()->to('/profile')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $this->userData['id'] . ']',
            'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
        ];

        // Handle profile photo upload
        $photo = $this->request->getFile('foto_profil');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move('uploads/profile', $newName);
            $data['foto_profil'] = $newName;
        }

        $userModel->update($this->userData['id'], $data);

        // Update session
        $session = session();
        $session->set('nama', $data['nama']);
        $session->set('email', $data['email']);

        // Log activity
        $this->logActivity('Mengubah Profil', 'User', $this->userData['id'], json_encode($user), json_encode($data));

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Ubah Password',
            'validation' => \Config\Services::validation(),
        ];

        return $this->render('profile/change_password', $data);
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($this->userData['id']);

        if (!$user) {
            return redirect()->to('/profile')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai');
        }

        $userModel->update($this->userData['id'], [
            'password' => $this->request->getPost('new_password'),
        ]);

        // Log activity
        $this->logActivity('Mengubah Password', 'User', $this->userData['id'], null, null);

        return redirect()->to('/profile')->with('success', 'Password berhasil diubah');
    }
}
