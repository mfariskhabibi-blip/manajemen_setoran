<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display list of users with search, role, and status filters
     */
    public function index()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $search = $this->request->getGet('search') ?? '';
        $role = $this->request->getGet('role') ?? '';
        $status = $this->request->getGet('status') ?? '';

        $builder = $this->userModel;

        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('nama', $search)
                ->orLike('username', $search)
                ->orLike('email', $search)
                ->orLike('nomor_whatsapp', $search)
            ->groupEnd();
        }

        if (!empty($role)) {
            $builder = $builder->where('role', $role);
        }

        if (!empty($status)) {
            $builder = $builder->where('status', $status);
        }

        $users = $builder->orderBy('created_at', 'DESC')->paginate(10);

        $data = [
            'title' => 'Data Pengguna',
            'users' => $users,
            'pager' => $this->userModel->pager,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'status' => $status
            ],
            'stats' => $this->userModel->getUserStats()
        ];

        return $this->render('admin/users/index', $data);
    }

    /**
     * Show form to create new user
     */
    public function create()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Tambah Data Pengguna Baru'
        ];

        return $this->render('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nama'           => 'required|min_length[3]|max_length[100]',
            'username'       => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'          => 'required|valid_email|is_unique[users.email]',
            'password'       => 'required|min_length[8]',
            'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
            'role'           => 'required|in_list[admin,user]',
            'status'         => 'required|in_list[active,inactive,suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'nama'           => $this->request->getPost('nama'),
            'username'       => $this->request->getPost('username'),
            'email'          => $this->request->getPost('email'),
            'password'       => $this->request->getPost('password'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
            'role'           => $this->request->getPost('role'),
            'status'         => $this->request->getPost('status'),
        ];

        $this->userModel->insert($userData);
        $newId = $this->userModel->getInsertID();

        $this->logActivity('Menambahkan pengguna baru ID: ' . $newId . ' (' . $userData['username'] . ')', null, $userData);

        return redirect()->to('/admin/users')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Show form to edit user
     */
    public function edit($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userItem = $this->userModel->find($id);

        if (!$userItem) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Data Pengguna: ' . $userItem['nama'],
            'userItem' => $userItem
        ];

        return $this->render('admin/users/edit', $data);
    }

    /**
     * Update user details
     */
    public function update($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userItem = $this->userModel->find($id);
        if (!$userItem) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'nama'           => 'required|min_length[3]|max_length[100]',
            'username'       => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email'          => "required|valid_email|is_unique[users.email,id,{$id}]",
            'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
            'role'           => 'required|in_list[admin,user]',
            'status'         => 'required|in_list[active,inactive,suspended]',
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama'           => $this->request->getPost('nama'),
            'username'       => $this->request->getPost('username'),
            'email'          => $this->request->getPost('email'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
            'role'           => $this->request->getPost('role'),
            'status'         => $this->request->getPost('status'),
        ];

        if (!empty($password)) {
            $updateData['password'] = $password;
        }

        $this->userModel->update($id, $updateData);

        $this->logActivity('Memperbarui pengguna ID: ' . $id, $userItem, $updateData);

        return redirect()->to('/admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Toggle user status (active/suspended)
     */
    public function toggleStatus($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->jsonResponse(null, 403, 'Akses ditolak.');
        }

        $userItem = $this->userModel->find($id);
        if (!$userItem) {
            return $this->jsonResponse(null, 404, 'Pengguna tidak ditemukan.');
        }

        if ($userItem['id'] == $this->userData['id']) {
            return $this->jsonResponse(null, 400, 'Tidak dapat mengubah status akun sendiri.');
        }

        $newStatus = $userItem['status'] === 'active' ? 'suspended' : 'active';
        $this->userModel->update($id, ['status' => $newStatus]);

        $this->logActivity('Mengubah status pengguna ID: ' . $id . ' ke ' . $newStatus);

        return $this->jsonResponse(['status' => $newStatus], 200, 'Status pengguna berhasil diperbarui.');
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return $this->jsonResponse(null, 403, 'Akses ditolak.');
        }

        $userItem = $this->userModel->find($id);
        if (!$userItem) {
            return $this->jsonResponse(null, 404, 'Pengguna tidak ditemukan.');
        }

        if ($userItem['id'] == $this->userData['id']) {
            return $this->jsonResponse(null, 400, 'Tidak dapat menghapus akun sendiri.');
        }

        $this->userModel->delete($id);
        $this->logActivity('Menghapus pengguna ID: ' . $id . ' (' . $userItem['username'] . ')', $userItem, null);

        return $this->jsonResponse(null, 200, 'Pengguna berhasil dihapus.');
    }
}
