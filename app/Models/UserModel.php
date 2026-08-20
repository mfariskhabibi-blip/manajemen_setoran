<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'nama', 'username', 'email', 'password', 
        'nomor_whatsapp', 'role', 'status', 'foto_profil',
        'last_login', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'nama'           => 'required|min_length[3]|max_length[100]',
        'username'       => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
        'email'         => 'required|valid_email|is_unique[users.email]',
        'password'      => 'required|min_length[8]',
        'nomor_whatsapp' => 'required|min_length[10]|max_length[20]',
        'role'          => 'required|in_list[admin,user]',
        'status'        => 'required|in_list[active,inactive,suspended]',
    ];
    
    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter',
        ],
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 50 karakter',
            'is_unique' => 'Username sudah digunakan',
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Email tidak valid',
            'is_unique' => 'Email sudah digunakan',
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 8 karakter',
        ],
        'nomor_whatsapp' => [
            'required' => 'Nomor WhatsApp harus diisi',
            'min_length' => 'Nomor WhatsApp minimal 10 digit',
            'max_length' => 'Nomor WhatsApp maksimal 20 digit',
        ],
    ];
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    /**
     * Hash password sebelum insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
    
    /**
     * Autentikasi user
     */
    public function authenticate($username, $password)
    {
        $user = $this->where('username', $username)
                     ->orWhere('email', $username)
                     ->first();
        
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        // Update last login
        $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
        
        return $user;
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers()
    {
        return $this->where('status', 'active')->findAll();
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        return [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'suspended' => $this->where('status', 'suspended')->countAllResults(),
            'admin' => $this->where('role', 'admin')->countAllResults(),
            'user' => $this->where('role', 'user')->countAllResults(),
        ];
    }
}