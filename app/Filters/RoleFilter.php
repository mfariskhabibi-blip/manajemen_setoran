<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Before filter - check user role
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Get required roles from arguments
        $requiredRoles = $arguments ?? [];
        
        if (empty($requiredRoles)) {
            return; // No role requirements
        }
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Get user role
        $userRole = $session->get('role');
        
        // Check if user has required role
        if (!in_array($userRole, $requiredRoles)) {
            // Check for admin role (admin has access to everything)
            if ($userRole === 'admin') {
                return;
            }
            
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
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