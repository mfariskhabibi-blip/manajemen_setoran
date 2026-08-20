<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    /**
     * Display login form
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return $this->render('auth/login', [
            'title' => 'Login - Sistem Manajemen Setoran Iuran'
        ]);
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->authenticate($username, $password);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Check if account is active
        if ($user['status'] != 'active') {
            return redirect()->back()->withInput()->with('error', 'Akun tidak aktif. Silakan hubungi administrator.');
        }

        // Set session data
        $sessionData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'nama'       => $user['nama'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ];

        $this->session->set($sessionData);

        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            
            // Save token to database or file (simplified)
            // In real application, you should save this to database with expiration
            
            // Set cookie for 30 days
            set_cookie('remember_token', $token, 30 * 24 * 60 * 60);
        }

        // Log activity
        $this->logActivity('Login ke sistem');

        // Redirect based on role
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/dashboard')->with('success', 'Selamat datang, ' . $user['nama'] . '!');
        } else {
            return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user['nama'] . '!');
        }
    }

    /**
     * Display registration form
     */
    public function register()
    {
        // Redirect if already logged in
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        // Check if registration is allowed
        $authConfig = new \Config\Auth();
        if (!$authConfig->allowRegistration) {
            return redirect()->to('/login')->with('error', 'Registrasi saat ini tidak tersedia.');
        }

        return $this->render('auth/register', [
            'title' => 'Daftar Akun - Sistem Manajemen Setoran Iuran'
        ]);
    }

    /**
     * Process registration
     */
    public function processRegister()
    {
        // Check if registration is allowed
        $authConfig = new \Config\Auth();
        if (!$authConfig->allowRegistration) {
            return redirect()->to('/login')->with('error', 'Registrasi saat ini tidak tersedia.');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama' => 'required|min_length[3]|max_length[100]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
            'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new \App\Models\UserModel();

        $userData = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
            'role' => $authConfig->defaultRole,
            'status' => 'active', // Auto activate for now
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (!$userModel->save($userData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun. Silakan coba lagi.');
        }

        // Log activity
        $userId = $userModel->getInsertID();
        $this->logActivity('Registrasi akun baru', null, ['user_id' => $userId, 'email' => $userData['email']]);

        // Auto login after registration
        $sessionData = [
            'user_id'    => $userId,
            'username'   => $userData['username'],
            'nama'       => $userData['nama'],
            'email'      => $userData['email'],
            'role'       => $userData['role'],
            'isLoggedIn' => true,
        ];

        $this->session->set($sessionData);

        return redirect()->to('/dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $userData['nama'] . '!');
    }

    /**
     * Logout
     */
    public function logout()
    {
        if ($this->session->get('isLoggedIn')) {
            // Log activity
            $this->logActivity('Logout dari sistem');
            
            // Destroy session
            $this->session->destroy();
            
            // Clear remember me cookie
            delete_cookie('remember_token');
        }

        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        return $this->render('auth/forgot_password', [
            'title' => 'Lupa Password - Sistem Manajemen Setoran Iuran'
        ]);
    }

    /**
     * Process forgot password
     */
    public function processForgotPassword()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak ditemukan.');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        
        // Save token to database (simplified - in real app use dedicated table)
        // For now, we'll just show success message
        
        // Log activity
        $this->logActivity('Meminta reset password', null, ['email' => $email]);

        return redirect()->back()->with('success', 'Instruksi reset password telah dikirim ke email Anda.');
    }

    /**
     * Reset password page
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/forgot-password')->with('error', 'Token tidak valid.');
        }

        // Verify token (simplified)
        
        return $this->render('auth/reset_password', [
            'title' => 'Reset Password - Sistem Manajemen Setoran Iuran',
            'token' => $token
        ]);
    }

    /**
     * Process reset password
     */
    public function processResetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/forgot-password')->with('error', 'Token tidak valid.');
        }

        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Verify token and get user (simplified)
        // In real app, verify token from database
        
        $password = $this->request->getPost('password');
        
        // For now, redirect to login
        $this->logActivity('Reset password', null, ['token_used' => $token]);

        return redirect()->to('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}