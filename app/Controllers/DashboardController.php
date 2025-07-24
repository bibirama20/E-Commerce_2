<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Controllers\BaseController;

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


}
