<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = $session->get('role');

        if ($arguments) {
            $allowedRole = $arguments[0];
            if ($role !== $allowedRole) {
                return redirect()->to('/' . $role . '/dashboard')->with('error', 'Akses ditolak.');
            }
        }

        // Jika role cocok, lanjut akses
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan}
    }
    }