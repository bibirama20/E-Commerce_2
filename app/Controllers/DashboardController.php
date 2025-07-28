<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Controllers\BaseController;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $productModel;
    protected $orderModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->orderModel  = new OrderModel();
    }

    public function admin()
    {
        if (session()->get('role') !== 'admin') return redirect()->to('/user/dashboard');

        $data = [
            'username'          => session()->get('username'),
            'total_produk'      => $this->productModel->countAll(),
            'produk_terbaru'    => $this->productModel->orderBy('id', 'DESC')->findAll(5),
            'total_pesanan'     => $this->orderModel->countAll(),
            'total_pendapatan'  => $this->orderModel->selectSum('total')->first()['total'] ?? 0,
            'pesanan_hari_ini'  => $this->orderModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
        ];

        return view('admin/dashboard', $data);
    }

    public function user()
    {
        if (session()->get('role') !== 'user') return redirect()->to('/admin/dashboard');

        $data = [
            'username'       => session()->get('username'),
            'total_produk'   => $this->productModel->countAll(),
            'produk_terbaru' => $this->productModel->orderBy('id', 'DESC')->findAll(5),
        ];

        return view('user/dashboard', $data);
    }

public function statistik()
{
    $orderModel = new \App\Models\OrderModel();

    // Total pendapatan
    $totalPendapatan = $orderModel->selectSum('total')->first()['total'] ?? 0;

    // Jumlah pesanan
    $jumlahPesanan = $orderModel->countAllResults();

    // Rata-rata transaksi
    $rataRataTransaksi = $jumlahPesanan > 0 ? ($totalPendapatan / $jumlahPesanan) : 0;

    // Grafik penjualan bulanan
    $dataBulanan = $orderModel->select("MONTH(created_at) as bulan, SUM(total) as total")
        ->groupBy("MONTH(created_at)")
        ->orderBy("MONTH(created_at)", "ASC")
        ->findAll();

    $bulan = [];
    $totalPerBulan = [];
    foreach ($dataBulanan as $row) {
        $bulan[] = date('F', mktime(0, 0, 0, $row['bulan'], 10)); // nama bulan
        $totalPerBulan[] = $row['total'];
    }

    return view('admin/statistik', [
        'totalPendapatan' => $totalPendapatan,
        'jumlahPesanan' => $jumlahPesanan,
        'rataRataTransaksi' => $rataRataTransaksi,
        'bulan' => $bulan,
        'totalPerBulan' => $totalPerBulan
    ]);
}

public function daftarUser()
{
    $userModel = new \App\Models\UserModel();
    $search = $this->request->getGet('q');

    $query = $userModel;
    if ($search) {
        $query = $query->like('username', $search);
    }

    $users = $query->findAll();

    $adminUsers = array_filter($users, fn($user) => $user['role'] === 'admin');
    $normalUsers = array_filter($users, fn($user) => $user['role'] === 'user');

    return view('admin/daftar_user', [
        'adminUsers'  => $adminUsers,
        'normalUsers' => $normalUsers,
        'search'      => $search,
    ]);
}


    public function editUser($id)
{
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);

    if (!$user) {
        return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
    }

    return view('admin/edit_user', ['user' => $user]);
}

public function updateUser($id)
{
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);

    if (!$user) {
        return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
    }

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');
    $role = $this->request->getPost('role');

    $data = [
        'username' => $username,
        'role' => $role,
    ];

    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    $userModel->update($id, $data);

    return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui');
}

 public function delete($id)
    {
        $model = new UserModel();
        $user = $model->find($id);
        if (!$user) {
            return redirect()->back()->with('errors', ['User tidak ditemukan.']);
        }

        $model->delete($id);

        return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus.');
    }
    
public function listAdmin()
{
    $userModel = new \App\Models\UserModel();
    $admins = $userModel->where('role', 'admin')->findAll();

    return view('admin/users/admin_list', ['users' => $admins]);
}

public function listUser()
{
    $userModel = new \App\Models\UserModel();
    $users = $userModel->where('role', 'user')->findAll();

    return view('admin/users/user_list', ['users' => $users]);
}

public function create()
{
    return view('admin/tambah_user');
}

public function store()
{
    $userModel = new \App\Models\UserModel();

    $data = [
        'username' => $this->request->getPost('username'),
        'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'role'     => $this->request->getPost('role'),
    ];

    $userModel->insert($data);

    // Redirect ke route yang ada
    return redirect()->to(base_url('admin/users'))->with('success', 'User berhasil ditambahkan.');
}


}
