<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use CodeIgniter\I18n\Time;


class OrderController extends BaseController
{
    protected $productModel, $orderModel, $orderItemModel;
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->productModel    = new ProductModel();
        $this->orderModel      = new OrderModel();
        $this->orderItemModel  = new OrderItemModel();
        $this->client          = new \GuzzleHttp\Client();
        $this->apiKey          = env('COST_KEY');
    }
public function index()  
{
    $orderModel = new \App\Models\OrderModel();

    $nama    = $this->request->getGet('nama');
    $tanggal = $this->request->getGet('tanggal');
    $status  = $this->request->getGet('status');

    // ✅ 1. Ambil data pesanan sesuai filter
    $builder = $orderModel;

    if (!empty($nama)) {
        $builder->like('nama', $nama);
    }

    if (!empty($tanggal)) {
        $builder->where('DATE(created_at)', $tanggal);
    }

    if (!empty($status)) {
        $builder->where('status', $status);
    }

    $orders = $builder->orderBy('created_at', 'DESC')->findAll();

    // ✅ 2. Ambil data statistik secara terpisah TANPA kondisi filter
    $statusCounts = $orderModel
        ->select("LOWER(status) as status_lower, COUNT(*) as jumlah")
        ->groupBy("LOWER(status)")
        ->findAll();

    // ✅ 3. Siapkan struktur default
    $jumlahStatus = [
        'belum bayar' => 0,
        'dikemas'     => 0,
        'dikirim'     => 0,
        'selesai'     => 0,
    ];

    foreach ($statusCounts as $row) {
        $key = trim($row['status_lower']);
        if (isset($jumlahStatus[$key])) {
            $jumlahStatus[$key] = $row['jumlah'];
        }
    }

    // ✅ 4. Tampilkan ke view
    return view('admin/orders/index', [
        'orders'        => $orders,
        'nama'          => $nama,
        'tanggal'       => $tanggal,
        'status'        => $status,
        'jumlahStatus'  => $jumlahStatus
    ]);
}


   public function produk()
{
    $keyword   = trim($this->request->getGet('keyword'));
    $minPrice  = $this->request->getGet('min_price');
    $maxPrice  = $this->request->getGet('max_price');
    $sort      = $this->request->getGet('sort');

    $builder = new ProductModel();

    // Filter nama
    if (!empty($keyword)) {
        $builder = $builder->like('name', $keyword);
    }

    // Filter harga
    if ($minPrice !== null && is_numeric($minPrice)) {
        $builder = $builder->where('price >=', floatval($minPrice));
    }

    if ($maxPrice !== null && is_numeric($maxPrice)) {
        $builder = $builder->where('price <=', floatval($maxPrice));
    }

    // Sorting
    switch ($sort) {
        case 'price_asc':
            $builder = $builder->orderBy('price', 'ASC');
            break;
        case 'price_desc':
            $builder = $builder->orderBy('price', 'DESC');
            break;
        case 'name_asc':
            $builder = $builder->orderBy('name', 'ASC');
            break;
        default:
            $builder = $builder->orderBy('id', 'DESC');
    }

    $produk = $builder->findAll();

    $data = [
        'produk'    => $produk,
        'keyword'   => $keyword,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
        'sort'      => $sort,
        'role'      => session()->get('role')
    ];

    return view($data['role'] === 'admin' ? 'admin/produk/list' : 'user/produk/list', $data);
}

    public function tambah($id)
    {
        $cart = session()->get('cart') ?? [];
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        session()->set('cart', $cart);
        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function hapus($id)
    {
        $cart = session()->get('cart') ?? [];
        unset($cart[$id]);
        session()->set('cart', $cart);
        return redirect()->back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function keranjang()
    {
        $cart  = session()->get('cart') ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p) continue;

            $diskon = $p['diskon'] ?? 0;
            $hargaDiskon = $p['price'] - ($p['price'] * $diskon / 100);
            $subtotal = $qty * $hargaDiskon;

            $p['quantity'] = $qty;
            $p['subtotal'] = $subtotal;
            $items[] = $p;

            $total += $subtotal;
        }

        $data = [
            'items' => $items,
            'total' => $total,
            'role'  => session()->get('role')
        ];
        return view($data['role'] === 'admin' ? 'admin/keranjang/index' : 'user/keranjang/index', $data);
    }

    public function updateKeranjang()
    {
        $cart = session()->get('cart') ?? [];
        $quantities = $this->request->getPost('quantities');

        if (!$quantities) {
            return redirect()->back()->with('error', 'Tidak ada data jumlah yang dikirim.');
        }

        foreach ($quantities as $id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) {
                unset($cart[$id]);
            } else {
                $cart[$id] = $qty;
            }
        }

        session()->set('cart', $cart);
        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function checkout()
    {
        $cart = session()->get('cart') ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p) continue;

            $diskon = $p['diskon'] ?? 0;
            $hargaDiskon = $p['price'] - ($p['price'] * $diskon / 100);
            $subtotal = $qty * $hargaDiskon;

            $p['quantity'] = $qty;
            $p['subtotal'] = $subtotal;
            $items[] = $p;

            $total += $subtotal;
        }

        return view(session()->get('role') . '/checkout', [
            'items' => $items,
            'total' => $total,
            'role'  => session()->get('role')
        ]);
    }

    public function checkoutSimpan()
    {
        $cart = session()->get('cart') ?? [];
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }

        // Ambil data dari form
        $nama     = $this->request->getPost('nama');
        $alamat   = $this->request->getPost('alamat');
        $no_hp    = $this->request->getPost('no_hp');
        $tujuan   = $this->request->getPost('kelurahan');
        $layanan  = $this->request->getPost('shipping_delivery');
        $ongkir   = $this->request->getPost('shipping_cost');
        $etd      = $this->request->getPost('estimasi_hari');
        $total    = $this->request->getPost('total_harga');

        // ✅ Ambil ID user dari session
        $userId = session()->get('id');

        // Hitung ulang berat total
        $totalBerat = 0;
        foreach ($cart as $id => $qty) {
            $produk = $this->productModel->find($id);
            if (!$produk) continue;
            $beratItem = $produk['weight'] ?? 1000;
            $totalBerat += $beratItem * $qty;
        }

        // ✅ Simpan ke orders
        $orderId = $this->orderModel->insert([
            'user_id'           => $userId,
            'nama'              => $nama,
            'alamat'            => $alamat,
            'no_hp'             => $no_hp,
            'city'              => $tujuan,
            'shipping_cost'     => $ongkir,
            'shipping_delivery' => $layanan,
            'estimasi'          => $etd,
            'total'             => $total
        ]);

        // Simpan ke order_items
        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p) continue;

            $diskon = $p['diskon'] ?? 0;
            $harga = $p['price'] - ($p['price'] * $diskon / 100);
            $subtotal = $qty * $harga;

            $this->orderItemModel->insert([
                'order_id'   => $orderId,
                'product_id' => $id,
                'quantity'   => $qty,
                'price'      => $harga,
                'subtotal'   => $subtotal
            ]);
        }

        session()->remove('cart');
        return redirect()->to('/' . session()->get('role') . '/checkout')->with('success', 'Pesanan berhasil dibuat.');

    }


   public function invoice($id)
{
    $order = $this->orderModel->find($id);

    if (!$order || $order['user_id'] != session()->get('id')) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan atau tidak diizinkan.');
    }

    $items = $this->orderItemModel
                  ->where('order_id', $id)
                  ->findAll();

    foreach ($items as &$item) {
        $product = $this->productModel->find($item['product_id']);
        $item['product_name'] = $product['name'] ?? 'Produk tidak ditemukan';
    }

    return view('user/invoice', [
        'order' => $order,
        'items' => $items
    ]);
}

    public function daftarPesanan()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $orderModel = new OrderModel();
        $userModel  = new UserModel();

        // Ambil hanya user dengan role user
        $userIds = $userModel->where('role', 'user')->findColumn('id');

        $orders = $orderModel
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/orders/index', [
            'orders' => $orders,
            'title'  => 'Daftar Pesanan User'
        ]);
    }

    public function detailPesanan($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $orderModel     = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $userModel      = new UserModel();
        $productModel   = new ProductModel();

        $order = $orderModel->find($id);
        if (!$order) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $user = $userModel->find($order['user_id']);
        if (!$user || $user['role'] !== 'user') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $itemsRaw = $orderItemModel->where('order_id', $id)->findAll();
        $items = [];

        foreach ($itemsRaw as $item) {
            $produk = $productModel->find($item['product_id']);
            $items[] = [
                'name'     => $produk['name'] ?? 'Produk',
                'image'    => $produk['image'] ?? '',
                'quantity' => $item['quantity'],
                'price'    => $item['price'],
                'subtotal' => $item['subtotal']
            ];
        }

        return view('admin/orders/detail', [
            'order' => $order,
            'items' => $items,
            'user'  => $user,
            'title' => 'Detail Pesanan'
        ]);
    }

public function adminIndex()
{
    $orderModel = new OrderModel();

    // Ambil semua pesanan
    $orders = $orderModel->orderBy('created_at', 'DESC')->findAll();

    // Hitung jumlah status
    $statusCounts = $orderModel
        ->select("LOWER(status) as status_lower, COUNT(*) as jumlah")
        ->groupBy("LOWER(status)")
        ->findAll();

    // Default jumlah semua status
    $jumlahStatus = [
        'belum bayar' => 0,
        'dikemas'     => 0,
        'dikirim'     => 0,
        'selesai'     => 0,
    ];

    foreach ($statusCounts as $row) {
        $key = strtolower(trim($row['status_lower']));
        if (isset($jumlahStatus[$key])) {
            $jumlahStatus[$key] = $row['jumlah'];
        }
    }

    return view('admin/orders/index', [
        'orders'        => $orders,
        'jumlahStatus'  => $jumlahStatus,
        'nama'          => '',
        'tanggal'       => '',
        'status'        => ''
    ]);
}


public function detail($orderId)
{
    $orderModel = new \App\Models\OrderModel();
    $orderItemModel = new \App\Models\OrderItemModel();
    $productModel = new \App\Models\ProductModel();

    $order = $orderModel->find($orderId);
    if (!$order) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan.');
    }

    $items = $orderItemModel->where('order_id', $orderId)->findAll();

    // Tambahkan nama produk ke setiap item
    foreach ($items as &$item) {
        $product = $productModel->find($item['product_id']);
        $item['product_name'] = $product['name'] ?? 'Produk tidak ditemukan';
        $item['subtotal'] = $item['price'] * $item['quantity'];
    }

    return view('admin/orders/detail', [
        'order' => $order,
        'items' => $items
    ]);
}
// cetak resi orders
public function resi($id)
{
    $orderModel = new \App\Models\OrderModel();
    $orderItemModel = new \App\Models\OrderItemModel();
    $productModel = new \App\Models\ProductModel();

    $order = $orderModel->find($id);
    if (!$order) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan');
    }

    // Ambil item dari pesanan
    $itemsRaw = $orderItemModel->where('order_id', $id)->findAll();

    // Tambahkan nama produk ke tiap item
    $items = [];
    foreach ($itemsRaw as $item) {
        $product = $productModel->find($item['product_id']);
        $items[] = [
            'name' => $product ? $product['name'] : 'Produk tidak ditemukan',
            'quantity' => $item['quantity'],
            'subtotal' => $item['subtotal'],
            'price' => $item['price'] ?? 0,
        ];
    }

    return view('admin/orders/resi_pdf', [
        'order' => $order,
        'items' => $items
    ]);
}

//print detail orders

public function printAll()
{
    $orderModel = new \App\Models\OrderModel();
    $db = \Config\Database::connect();

    // Ambil filter tanggal
    $from = $this->request->getGet('from');
    $to   = $this->request->getGet('to');

    // Ambil data pesanan
    $query = $orderModel->orderBy('created_at', 'DESC');
    if ($from && $to) {
        $query->where("DATE(created_at) >=", $from)
              ->where("DATE(created_at) <=", $to);
    }
    $orders = $query->findAll();

    // Ambil detail item
    $itemsRaw = $db->table('order_items')
        ->select('order_items.order_id, products.name AS product_name, order_items.quantity, order_items.price')
        ->join('products', 'products.id = order_items.product_id', 'left')
        ->get()
        ->getResultArray();

    // Kelompokkan item
    $items = [];
    foreach ($itemsRaw as $item) {
        $items[$item['order_id']][] = $item;
    }

    // Filter hanya pesanan yang punya barang
    $filteredOrders = array_filter($orders, function ($order) use ($items) {
        return isset($items[$order['id']]);
    });

    // Cek apakah user minta PDF
    if ($this->request->getGet('pdf') === 'yes') {
        $html = view('admin/orders/printAll', [
            'orders' => $filteredOrders,
            'items' => $items
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }

    // Default: tampilkan halaman biasa
    return view('admin/orders/printAll', [
        'orders' => $filteredOrders,
        'items' => $items
    ]);
}

//detail pesanan user

public function pesanan()
{
    $orderModel = new \App\Models\OrderModel();
    $orderItemModel = new \App\Models\OrderItemModel();
    $userId = session()->get('id');
    $status = $this->request->getGet('status');
    $keyword = $this->request->getGet('keyword');

    $builder = $orderModel->where('user_id', $userId);

    if ($status) {
        $builder->where('status', $status);
    }

    // Jika ada keyword, cari di order dan item produk
    if ($keyword) {
        $orderIdsByProduct = $orderItemModel
            ->select('order_id')
            ->join('products', 'products.id = order_items.product_id')
            ->like('products.name', $keyword)
            ->groupBy('order_id')
            ->findColumn('order_id');

        $builder->groupStart()
                ->like('nama', $keyword)
                ->orLike('alamat', $keyword)
                ->orLike('shipping_delivery', $keyword)
                ->orLike('status', $keyword);

        if (!empty($orderIdsByProduct)) {
            $builder->orWhereIn('id', $orderIdsByProduct);
        }

        $builder->groupEnd();
    }

    $orders = $builder->orderBy('created_at', 'DESC')->findAll();

    // Proses status otomatis dan ambil item pesanan
    foreach ($orders as &$order) {
        if ($order['status'] === 'Dikirim' && isset($order['updated_at'])) {
            $updatedAt = Time::parse($order['updated_at']);
            if ($updatedAt->addDays(3)->isBefore(Time::now())) {
                $orderModel->update($order['id'], ['status' => 'Selesai']);
                $order['status'] = 'Selesai';
            }
        }

        $order['items'] = $orderItemModel
            ->select('order_items.*, products.name')
            ->join('products', 'products.id = order_items.product_id')
            ->where('order_id', $order['id'])
            ->findAll();
    }

    return view('user/pesanan', [
        'orders' => $orders,
        'statusFilter' => $status,
        'keyword' => $keyword
    ]);
}

public function selesaikanPesanan($id)
{
    $orderModel = new \App\Models\OrderModel();
    $order = $orderModel->find($id);

    // Cek apakah pesanan milik user yang sedang login
    if (!$order || $order['user_id'] != session()->get('id')) {
        return redirect()->to('/user/pesanan')->with('error', 'Pesanan tidak ditemukan atau tidak valid.');
    }

    // Ubah status hanya jika masih "Dikirim"
    if ($order['status'] === 'Dikirim') {
        $orderModel->update($id, ['status' => 'Selesai']);
        return redirect()->to('/user/pesanan')->with('success', 'Pesanan telah ditandai selesai.');
    }

    return redirect()->to('/user/pesanan');
}

public function invoicePdf($id)
{
    $order = $this->orderModel->find($id);

    // Pastikan order milik user yang login
    if (!$order || $order['user_id'] != session()->get('id')) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Pesanan tidak ditemukan atau tidak valid.');
    }

    // Ambil item pesanan
    $items = $this->orderItemModel
        ->where('order_id', $id)
        ->findAll();

    foreach ($items as &$item) {
        $product = $this->productModel->find($item['product_id']);
        $item['product_name'] = $product['name'] ?? 'Produk tidak ditemukan';
    }

    // Load view HTML
    $html = view('user/invoice_pdf', [
        'order' => $order,
        'items' => $items
    ]);

    // Generate PDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $this->response
        ->setContentType('application/pdf')
        ->setBody($dompdf->output());
}

public function ubahStatus($id)
{
    $orderModel = new \App\Models\OrderModel();
    $order = $orderModel->find($id);

    if (!$order) {
        return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
    }

    // Status sesuai format di database (huruf kapital awal)
    $statusSaatIni = trim($order['status']);

    $statusBerikutnya = [
        'Belum Bayar' => 'Dikemas',
        'Dikemas'     => 'Dikirim',
        'Dikirim'     => 'Selesai',
        'Selesai'     => null // Tidak bisa lanjut
    ];

    $statusSelanjutnya = $statusBerikutnya[$statusSaatIni] ?? null;

    if (!$statusSelanjutnya) {
        return redirect()->back()->with('warning', 'Status tidak bisa diubah lagi.');
    }

    // Update status
    $updated = $orderModel->update($id, ['status' => $statusSelanjutnya]);

    if ($updated === false) {
        return redirect()->back()->with('error', 'Gagal mengubah status.');
    }

    return redirect()->back()->with('success', "Status diubah menjadi $statusSelanjutnya.");
}

}