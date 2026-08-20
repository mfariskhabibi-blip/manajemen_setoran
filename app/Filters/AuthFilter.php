<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Before filter - check if user is logged in
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Routes yang tidak perlu login
        $publicRoutes = [
            'login',
            'register',
            'auth/login',
            'auth/register',
            'auth/process-login',
            'auth/process-register'
        ];
        
        $currentRoute = service('router')->getMatchedRoute()[0] ?? '';
        
        // Skip untuk route publik
        if (in_array($currentRoute, $publicRoutes)) {
            return;
        }
        
        // Cek apakah user sudah login
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek apakah akun masih aktif
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($session->get('user_id'));
        
        if (!$user || $user['status'] != 'active') {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Akun tidak aktif atau tidak ditemukan.');
        }
    }

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada action setelah request
    }
}