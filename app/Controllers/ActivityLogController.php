<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\UserModel;

class ActivityLogController extends BaseController
{
    protected $activityLogModel;
    protected $userModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->userModel        = new UserModel();
    }

    /**
     * View Activity Logs
     */
    public function index()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userId    = $this->request->getGet('user_id');
        $search    = $this->request->getGet('search');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $db = db_connect();
        $builder = $db->table('log_aktivitas l')
            ->select('l.*, u.nama as user_name, u.username, u.role')
            ->join('users u', 'u.id = l.user_id', 'left');

        if (!empty($userId)) {
            $builder->where('l.user_id', $userId);
        }

        if (!empty($search)) {
            $builder->like('l.aktivitas', $search);
        }

        if (!empty($startDate)) {
            $builder->where('l.waktu >=', $startDate . ' 00:00:00');
        }

        if (!empty($endDate)) {
            $builder->where('l.waktu <=', $endDate . ' 23:59:59');
        }

        $logs = $builder->orderBy('l.waktu', 'DESC')->get()->getResultArray();

        // Build lookup maps for resolving IDs to human-readable names
        $allUsers = $this->userModel->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u['nama'] . ($u['username'] ? ' (' . $u['username'] . ')' : '');
        }

        $programModel = new \App\Models\ProgramModel();
        $allPrograms = $programModel->findAll();
        $programMap = [];
        foreach ($allPrograms as $p) {
            $programMap[$p['id']] = $p['nama_program'];
        }

        $acaraModel = new \App\Models\AcaraModel();
        $allAcaras = $acaraModel->findAll();
        $acaraMap = [];
        foreach ($allAcaras as $a) {
            $acaraMap[$a['id']] = $a['nama_acara'];
        }

        $periodeModel = new \App\Models\PeriodeModel();
        $allPeriodes = $periodeModel->findAll();
        $periodeMap = [];
        foreach ($allPeriodes as $pr) {
            $periodeMap[$pr['id']] = $pr['nama_periode'];
        }

        $data = [
            'title'      => 'Log Aktivitas Sistem',
            'logs'       => $logs,
            'users'      => $allUsers,
            'userMap'    => $userMap,
            'programMap' => $programMap,
            'acaraMap'   => $acaraMap,
            'periodeMap' => $periodeMap,
            'filters'    => [
                'user_id'    => $userId,
                'search'     => $search,
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]
        ];

        return $this->render('admin/activity_log/index', $data);
    }
}
