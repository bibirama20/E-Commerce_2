<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Controllers\BaseController; // ✅ Tambahkan ini

class DashboardController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

   public function admin()
    {
        if (session()->get('role') !== 'admin') return redirect()->to('/user/dashboard');

        $data = [
            'username' => session()->get('username'),
            'total_produk' => $this->productModel->countAll(),
            'produk_terbaru' => $this->productModel->orderBy('id', 'DESC')->findAll(5)
        ];
        return view('admin/dashboard', $data);
    }

    public function user()
    {
        if (session()->get('role') !== 'user') return redirect()->to('/admin/dashboard');

        $data = [
            'username' => session()->get('username'),
            'total_produk' => $this->productModel->countAll(),
            'produk_terbaru' => $this->productModel->orderBy('id', 'DESC')->findAll(5)
        ];
        return view('user/dashboard', $data);
    }
}
