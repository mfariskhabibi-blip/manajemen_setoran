<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table            = 'chats';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['sender_id', 'receiver_id', 'pesan', 'status_baca', 'created_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get chat conversation between two users
     */
    public function getConversation($userId1, $userId2)
    {
        return $this->groupStart()
                        ->where('sender_id', $userId1)
                        ->where('receiver_id', $userId2)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('sender_id', $userId2)
                        ->where('receiver_id', $userId1)
                    ->groupEnd()
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($senderId, $receiverId)
    {
        return $this->where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId)
                    ->set(['status_baca' => 'read'])
                    ->update();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('receiver_id', $userId)
                    ->where('status_baca', 'unread')
                    ->countAllResults();
    }
}
