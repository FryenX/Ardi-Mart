<?php

namespace App\Models;

use CodeIgniter\Model;

class usersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'uuid';

    protected $allowedFields = [
        'id',
        'uuid',
        'name',
        'level_id',
        'username',
        'email',
        'password',
        'image',
        'created_at',
        'updated_at'
    ];

    public function searchData($search)
    {
        return $this->table('users')->join('levels', 'levels.id=users.level_id')->like('users.name', $search)->orLike('info', $search)->orderBy('users.level_id', 'ASC');
    }

    public function login($username, $password)
    {
        $user = $this->db->table('users')->join('levels', 'levels.id=users.level_id')->where('username', $username)->get()->getRowArray();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }

    public function verifyEMail($email)
    {
        return $this->where('email', $email)->first();
    }
}
