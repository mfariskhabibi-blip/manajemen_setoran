<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupMessageModel extends Model
{
    protected $table            = 'group_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['group_id', 'sender_id', 'pesan', 'created_at'];

    protected $useTimestamps = false;
    
    /**
     * Get group conversation
     */
    public function getGroupConversation($groupId)
    {
        return $this->select('group_messages.*, users.nama as sender_name')
                    ->join('users', 'users.id = group_messages.sender_id')
                    ->where('group_id', $groupId)
                    ->orderBy('group_messages.created_at', 'ASC')
                    ->findAll();
    }
}
