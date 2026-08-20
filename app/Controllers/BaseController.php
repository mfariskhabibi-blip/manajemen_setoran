<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'url', 'session', 'html'];

    /**
     * Session instance
     *
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * User data
     *
     * @var array|null
     */
    protected $userData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->session = service('session');
        
        // Initialize user data if logged in
        if ($this->session->get('isLoggedIn')) {
            $userModel = new \App\Models\UserModel();
            $this->userData = $userModel->find($this->session->get('user_id'));
        }
    }

    /**
     * Initialize controller
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load helpers
        helper($this->helpers);

        // Load services
        $this->request = service('request');
        
        // Set timezone
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Render view with layout
     */
    protected function render($view, $data = [])
    {
        // Merge user data with view data
        $data['user'] = $this->userData;
        $data['title'] = $data['title'] ?? 'Sistem Manajemen Data Setoran Iuran Terpadu';
        
        // Check user role for layout
        if ($this->userData) {
            $data['layout'] = $this->userData['role'] === 'admin' ? 'admin/layout' : 'user/layout';
        } else {
            $data['layout'] = 'auth/layout';
        }

        return view($view, $data);
    }

    /**
     * Check if user has permission
     */
    protected function hasPermission($permission)
    {
        if (!$this->userData) {
            return false;
        }

        // Admin has all permissions
        if ($this->userData['role'] === 'admin') {
            return true;
        }

        // Check specific permissions for user role
        $userRole = $this->userData['role'];
        
        // Define permissions for each role
        $permissions = [
            'user' => [
                'view_dashboard',
                'view_setoran',
                'view_riwayat',
                'view_profile',
                'edit_profile',
                'use_chat',
            ],
        ];

        return isset($permissions[$userRole]) && in_array($permission, $permissions[$userRole]);
    }

    /**
     * Log activity
     */
    protected function logActivity($activity, $dataBefore = null, $dataAfter = null)
    {
        if (!$this->userData) {
            return;
        }

        $logModel = new \App\Models\ActivityLogModel();
        
        $logData = [
            'user_id' => $this->userData['id'],
            'aktivitas' => $activity,
            'data_sebelum' => $dataBefore ? json_encode($dataBefore) : null,
            'data_sesudah' => $dataAfter ? json_encode($dataAfter) : null,
            'waktu' => date('Y-m-d H:i:s'),
        ];

        $logModel->insert($logData);
    }

    /**
     * Send JSON response
     */
    protected function jsonResponse($data, $status = 200, $message = '')
    {
        $response = service('response');
        
        $responseData = [
            'status' => $status >= 200 && $status < 300 ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
        ];

        return $response->setStatusCode($status)
                       ->setJSON($responseData);
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax()
    {
        return $this->request->isAJAX();
    }

    /**
     * Get pagination data
     */
    protected function getPagination($totalRows, $perPage = 10)
    {
        $pager = service('pager');
        
        return $pager->makeLinks(
            $this->request->getVar('page') ?? 1,
            $perPage,
            $totalRows,
            'default_full'
        );
    }
}
