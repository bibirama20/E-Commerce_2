<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);

            return redirect()->to('/' . $user['role'] . '/dashboard');
        }

        return redirect()->back()->with('error', 'Login gagal: username atau password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    // Opsional: Seeder user dummy (aktifkan jika dibutuhkan)
    /*
    public function registerDummy()
    {
        $userModel = new \App\Models\UserModel();

        // Admin
        $userModel->insert([
            'username' => 'admin',
            'password' => password_hash('putuganteng5k', PASSWORD_DEFAULT),
            'role'     => 'admin'
        ]);

        // User
        $userModel->insert([
            'username' => 'user',
            'password' => password_hash('putuayu2k', PASSWORD_DEFAULT),
            'role'     => 'user'
        ]);

        return 'Dummy user berhasil ditambahkan!';
    }
    */
}
