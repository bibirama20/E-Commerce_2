<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

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

    public function register()
    {
        return view('auth/register');
    }

    public function registerProcess()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[5]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->userModel->insert([
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/')->with('success', 'Berhasil daftar, silakan login.');
    }

   public function forgotPassword()
{
    $resetLink = session()->getFlashdata('reset_link');
    $message = session()->getFlashdata('message');
    $error = session()->getFlashdata('error');

    return view('auth/forgot_password', [
        'reset_link' => $resetLink,
        'message'    => $message,
        'error'      => $error
    ]);
}


    public function forgotPasswordSubmit()
    {
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');

        $user = $this->userModel
            ->where('email', $email)
            ->where('username', $username)
            ->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], ['reset_token' => $token]);

            session()->setFlashdata('reset_link', base_url("/reset-password/{$token}?id=" . $user['id']));
            session()->setFlashdata('message', 'Link reset berhasil dibuat. Silakan cek link di bawah.');

            return redirect()->to('/forgot-password'); // ✅ ganti dari redirect()->back()
        }

        session()->setFlashdata('error', 'Username atau email tidak cocok atau tidak ditemukan.');
        return redirect()->to('/forgot-password'); // ✅ ganti dari redirect()->back()
    }

    public function resetPassword($token)
    {
        $userId = $this->request->getGet('id');

        if (!$userId || !$token) {
            return redirect()->to('/')->with('error', 'Token atau ID tidak valid.');
        }

        $user = $this->userModel
            ->where('id', $userId)
            ->where('reset_token', $token)
            ->first();

        if (!$user) {
            return redirect()->to('/')->with('error', 'Token tidak valid atau telah kadaluarsa.');
        }

        return view('auth/reset_password', [
            'token'   => $token,
            'user_id' => $userId
        ]);
    }

   public function resetPasswordSubmit()
{
    $userId = $this->request->getPost('user_id');
    $token = $this->request->getPost('token');
    $password = $this->request->getPost('password');

    if (empty($userId) || empty($token) || empty($password)) {
        return redirect()->back()->with('error', 'Semua field wajib diisi.');
    }

    // ✅ Validasi token
    $user = $this->userModel
        ->where('id', $userId)
        ->where('reset_token', $token)
        ->first();

    if (!$user) {
        return redirect()->to('/')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
    }

    $this->userModel->update($userId, [
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'reset_token' => null,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    session()->setFlashdata('success', 'Password berhasil diubah. Silakan login.');
    return redirect()->to('/');
}

}
