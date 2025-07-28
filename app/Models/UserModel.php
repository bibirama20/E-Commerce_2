<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'role',
        'reset_token',
        'reset_expires',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi input
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[6]', // kosongkan saat update jika tidak ubah password
        'role'     => 'required|in_list[admin,user]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah digunakan.',
        ],
        'role' => [
            'in_list' => 'Role harus admin atau user.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Ambil semua user dengan role 'user'.
     */
    public function getOnlyUsers()
    {
        return $this->where('role', 'user')->findAll();
    }

    /**
     * Fungsi login untuk user biasa.
     */
    public function loginUser($email, $password)
    {
        $user = $this->where(['email' => $email, 'role' => 'user'])->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Fungsi login untuk admin.
     */
    public function loginAdmin($email, $password)
    {
        $user = $this->where(['email' => $email, 'role' => 'admin'])->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Ambil user berdasarkan reset token (dengan batas waktu kadaluarsa).
     */
    public function getUserByResetToken(string $token)
    {
        return $this->where('reset_token', $token)
                    ->where('reset_expires >=', date('Y-m-d H:i:s'))
                    ->first();
    }
}
