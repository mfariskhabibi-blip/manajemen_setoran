<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;
use App\Models\GroupModel;
use App\Models\GroupMemberModel;
use App\Models\GroupMessageModel;

class ChatController extends BaseController
{
    public function index()
    {
        if (!$this->userData) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Obrolan'
        ];

        return $this->render('chat/index', $data);
    }

    public function ping()
    {
        if (!$this->isAjax() || !$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }
        
        $userModel = new UserModel();
        $userModel->update($this->userData['id'], ['last_login' => date('Y-m-d H:i:s')]);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function getContacts()
    {
        if (!$this->isAjax() || !$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userModel = new UserModel();
        $chatModel = new ChatModel();
        $groupModel = new GroupModel();
        $groupMemberModel = new GroupMemberModel();
        $groupMsgModel = new GroupMessageModel();
        $db = \Config\Database::connect();

        $contacts = [];

        // 1. Get Private Users
        $users = $userModel->where('id !=', $this->userData['id'])->findAll();
        foreach ($users as $u) {
            // Unread count
            $unread = $chatModel->where('sender_id', $u['id'])
                                ->where('receiver_id', $this->userData['id'])
                                ->where('status_baca', 'unread')
                                ->countAllResults();
            
            // Last message
            $lastMsg = $chatModel->groupStart()
                                    ->where('sender_id', $this->userData['id'])->where('receiver_id', $u['id'])
                                 ->groupEnd()
                                 ->orGroupStart()
                                    ->where('sender_id', $u['id'])->where('receiver_id', $this->userData['id'])
                                 ->groupEnd()
                                 ->orderBy('created_at', 'DESC')
                                 ->first();

            $contacts[] = [
                'id' => $u['id'],
                'type' => 'user',
                'name' => $u['nama'],
                'avatar' => substr($u['nama'], 0, 1),
                'role' => $u['role'],
                'unread_count' => $unread,
                'last_message' => $lastMsg ? $lastMsg['pesan'] : '',
                'last_time' => $lastMsg ? date('H:i', strtotime($lastMsg['created_at'])) : '',
                'last_timestamp' => $lastMsg ? strtotime($lastMsg['created_at']) : 0,
                'is_online' => (strtotime($u['last_login']) > (time() - 300)), // Online if active in last 5 mins
                'last_seen' => $u['last_login']
            ];
        }

        // 2. Get Groups where user is member
        $myGroups = $groupMemberModel->where('user_id', $this->userData['id'])->findAll();
        foreach ($myGroups as $mg) {
            $group = $groupModel->find($mg['group_id']);
            if ($group) {
                // Last group message
                $lastGroupMsg = $groupMsgModel->where('group_id', $group['id'])
                                              ->orderBy('created_at', 'DESC')
                                              ->first();
                $contacts[] = [
                    'id' => $group['id'],
                    'type' => 'group',
                    'name' => $group['nama_grup'],
                    'avatar' => substr($group['nama_grup'], 0, 1),
                    'role' => 'Grup Diskusi',
                    'unread_count' => 0, // Simplified for groups
                    'last_message' => $lastGroupMsg ? $lastGroupMsg['pesan'] : '',
                    'last_time' => $lastGroupMsg ? date('H:i', strtotime($lastGroupMsg['created_at'])) : '',
                    'last_timestamp' => $lastGroupMsg ? strtotime($lastGroupMsg['created_at']) : 0,
                    'is_online' => false,
                    'last_seen' => null
                ];
            }
        }

        // Sort by last_timestamp DESC (recent chats first)
        usort($contacts, function($a, $b) {
            return $b['last_timestamp'] <=> $a['last_timestamp'];
        });

        return $this->jsonResponse($contacts);
    }

    public function getMessages($contactId, $type = 'user')
    {
        if (!$this->isAjax() || !$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        if ($type == 'group') {
            $groupMsgModel = new GroupMessageModel();
            $messages = $groupMsgModel->getGroupConversation($contactId);
        } else {
            $chatModel = new ChatModel();
            $chatModel->markAsRead($contactId, $this->userData['id']);
            $messages = $chatModel->getConversation($this->userData['id'], $contactId);
        }
        
        return $this->jsonResponse($messages);
    }

    public function sendMessage()
    {
        if (!$this->isAjax() || !$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $receiverId = $this->request->getPost('receiver_id');
        $type = $this->request->getPost('type') ?? 'user';
        $pesan = $this->request->getPost('pesan');

        if (empty($receiverId) || empty($pesan)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Data tidak lengkap']);
        }

        $data = [];
        
        if ($type == 'group') {
            $groupMsgModel = new GroupMessageModel();
            $data = [
                'group_id' => $receiverId,
                'sender_id' => $this->userData['id'],
                'pesan' => $pesan,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $groupMsgModel->insert($data);
            $data['sender_name'] = $this->userData['nama'];
        } else {
            $chatModel = new ChatModel();
            $data = [
                'sender_id' => $this->userData['id'],
                'receiver_id' => $receiverId,
                'pesan' => $pesan,
                'status_baca' => 'unread',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $chatModel->insert($data);
        }

        return $this->jsonResponse(['status' => 'success', 'message' => 'Pesan terkirim', 'data' => $data]);
    }

    /**
     * Get total unread message count for the current user (for navbar badge)
     */
    public function getUnreadCount()
    {
        if (!$this->userData) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $chatModel = new ChatModel();
        $count = $chatModel
            ->where('receiver_id', $this->userData['id'])
            ->where('status_baca', 'unread')
            ->countAllResults();

        return $this->response->setJSON(['status' => 'success', 'count' => (int)$count]);
    }

    /**
     * Create default group for all users
     */
    public function createDefaultGroup()
    {
        if (!$this->userData || $this->userData['role'] !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $groupModel = new GroupModel();
        $groupMemberModel = new GroupMemberModel();
        $userModel = new UserModel();

        // Check if default group exists
        $defaultGroup = $groupModel->where('nama_grup', 'Grup Diskusi Warga')->first();

        if (!$defaultGroup) {
            // Create default group
            $groupId = $groupModel->insert([
                'nama_grup' => 'Grup Diskusi Warga',
                'deskripsi' => 'Grup diskusi untuk seluruh warga yang terdaftar',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $defaultGroup = $groupModel->find($groupId);
        }

        // Get all active users
        $users = $userModel->where('status', 'active')->findAll();

        // Add all users to the group
        $addedCount = 0;
        foreach ($users as $user) {
            // Check if user is already in group
            $existingMember = $groupMemberModel->where('group_id', $defaultGroup['id'])
                                               ->where('user_id', $user['id'])
                                               ->first();

            if (!$existingMember) {
                $groupMemberModel->insert([
                    'group_id' => $defaultGroup['id'],
                    'user_id' => $user['id'],
                    'joined_at' => date('Y-m-d H:i:s')
                ]);
                $addedCount++;
            }
        }

        return redirect()->to('/chat')->with('success', "Grup diskusi berhasil dibuat. $addedCount pengguna ditambahkan ke grup.");
    }
}
