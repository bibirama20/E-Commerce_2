<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use CodeIgniter\Cart\Cart;
use CodeIgniter\I18n\Time;


class OrderController extends BaseController
{
    protected $productModel, $orderModel, $orderItemModel;
    protected $client;
    protected $apiKey;
    protected $cart;


    public function __construct()
    {
        $this->productModel    = new ProductModel();
        $this->orderModel      = new OrderModel();
        $this->orderItemModel  = new OrderItemModel();
        $this->client          = new \GuzzleHttp\Client();
        $this->apiKey          = env('COST_KEY');    }
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
            'dibatalkan'  => 0,
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
    $items = session()->get('checkout_items');

    // Jika belum menekan tombol checkout
    if (!$items || count($items) === 0) {
        return redirect()->to('/user/keranjang')->with('error', 'Silakan pilih barang untuk checkout terlebih dahulu.');
    }

    $total = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }

    return view('user/checkout', [
        'items' => $items,
        'total' => $total,
        'role' => 'user'
    ]);
}


public function prosesCheckout()
{
    $cart = session()->get('cart');

    if (!$cart || count($cart) === 0) {
        return redirect()->back()->with('error', 'Keranjang masih kosong');
    }

    // Simpan cart sementara ke session checkout_items
    session()->set('checkout_items', $cart);

    return redirect()->to('/user/checkout');
}


public function simpanProdukTerpilih()
{
    $selected = $this->request->getPost('selected_ids'); // misal: "1,3,5"
    $selectedIds = explode(',', $selected);
    session()->set('checkout_selected', $selectedIds);

    return redirect()->to('/user/checkout'); // Atau redirect ke halaman form pengiriman
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

public function checkoutPilih()
{
    $selected = $this->request->getPost('selected_items') ?? [];

    if (empty($selected)) {
        return redirect()->back()->with('error', 'Pilih minimal 1 barang untuk checkout.');
    }

    // Cast ke int untuk mencegah mismatch saat dicari di $cart
    $selected = array_map('intval', $selected);

    session()->set('checkout_selected', $selected);
    return redirect()->to('/' . session()->get('role') . '/checkout');
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
        'dibatalkan'     => 0,
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

    // Mulai query dasar untuk user terkait
    $builder = $orderModel
        ->where('orders.user_id', $userId)
        ->select('orders.*, orders.nama AS nama_pemesan'); // ✅ Tambahkan nama pemesan

    // Filter berdasarkan status jika ada
    if ($status) {
        $builder->where('orders.status', $status);
    }

    // Filter berdasarkan keyword jika ada
    if ($keyword) {
        // Cari order_id berdasarkan produk yang cocok dengan keyword
        $orderIdsByProduct = $orderItemModel
            ->select('order_id')
            ->join('products', 'products.id = order_items.product_id')
            ->like('products.name', $keyword)
            ->groupBy('order_id')
            ->findColumn('order_id');

        $builder->groupStart()
            ->like('orders.nama', $keyword)
            ->orLike('orders.alamat', $keyword)
            ->orLike('orders.shipping_delivery', $keyword)
            ->orLike('orders.status', $keyword);

        if (!empty($orderIdsByProduct)) {
            $builder->orWhereIn('orders.id', $orderIdsByProduct);
        }

        $builder->groupEnd();
    }

    // Ambil semua pesanan user dengan filter di atas
    $orders = $builder->orderBy('orders.created_at', 'DESC')->findAll();

    // Cek setiap pesanan untuk update status otomatis
    foreach ($orders as &$order) {
        // ✅ Otomatis batalkan jika belum bayar lebih dari 24 jam
        if ($order['status'] === 'Belum Bayar' && !empty($order['created_at'])) {
            $createdAt = \CodeIgniter\I18n\Time::parse($order['created_at'], 'Asia/Jakarta');
            $now = \CodeIgniter\I18n\Time::now('Asia/Jakarta');

            if ($createdAt->addHours(24)->isBefore($now)) {
                $orderModel->update($order['id'], ['status' => 'Dibatalkan']);
                $order['status'] = 'Dibatalkan'; // Update status lokal juga
            }
        }

        // ✅ Otomatis menjadi "Selesai" jika sudah dikirim lebih dari 3 hari
        if ($order['status'] === 'Dikirim' && !empty($order['updated_at'])) {
            $updatedAt = \CodeIgniter\I18n\Time::parse($order['updated_at'], 'Asia/Jakarta');
            if ($updatedAt->addDays(3)->isBefore(\CodeIgniter\I18n\Time::now('Asia/Jakarta'))) {
                $orderModel->update($order['id'], ['status' => 'Selesai']);
                $order['status'] = 'Selesai';
            }
        }

        // ✅ Ambil item produk untuk setiap pesanan
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

public function bayar($id)
{
    $orderModel = new \App\Models\OrderModel();
    $order = $orderModel->find($id);

    if (!$order || $order['user_id'] != session()->get('id')) {
        return redirect()->to('/user/pesanan')->with('error', 'Data tidak ditemukan');
    }

    return view('user/bayar', ['order' => $order]);
}
public function gantiMetode($id)
{
    $orderModel = new \App\Models\OrderModel();
    $order = $orderModel->find($id);

    if (!$order || $order['user_id'] != session()->get('id')) {
        return redirect()->to('/user/pesanan')->with('error', 'Data tidak valid');
    }

    // Hanya boleh ganti jika belum pernah
    if ($order['payment_changed']) {
        return redirect()->to('/user/pesanan')->with('error', 'Metode hanya bisa diganti sekali');
    }

    return view('user/ganti_metode', ['order' => $order]);
}

public function updateMetode($id)
{
    $orderModel = new \App\Models\OrderModel();
    $metodeBaru = $this->request->getPost('payment_method');

    $order = $orderModel->find($id);

    if (!$order || $order['user_id'] != session()->get('id') || $order['payment_changed']) {
        return redirect()->to('/user/pesanan')->with('error', 'Tidak diizinkan');
    }

    $orderModel->update($id, [
        'payment_method' => $metodeBaru,
        'payment_changed' => 1
    ]);

    return redirect()->to('/user/pesanan/bayar/' . $id)->with('success', 'Metode pembayaran diperbarui.');
}

public function simpanOrder()
{
    $cart = session()->get('cart');

    if (empty($cart)) {
        return redirect()->back()->with('error', 'Keranjang kosong.');
    }

    $orderModel = new OrderModel();
    $orderItemModel = new OrderItemModel();

    // Simpan data order
    $dataOrder = [
        'user_id'       => session()->get('user_id'),
        'nama'          => $this->request->getPost('nama'),
        'no_hp'         => $this->request->getPost('no_hp'),
        'alamat'        => $this->request->getPost('alamat'),
        'shipping_delivery' => $this->request->getPost('shipping_delivery'),
        'estimasi'      => $this->request->getPost('estimasi'),
        'total'         => $this->request->getPost('total'),
        'status'        => 'Dikemas', // ⬅️ Ubah otomatis dari awal
    ];

    $orderModel->insert($dataOrder);
    $orderId = $orderModel->getInsertID();

    foreach ($cart as $item) {
        $orderItemModel->insert([
            'order_id'  => $orderId,
            'product_id'=> $item['id'],
            'quantity'  => $item['qty'],
            'price'     => $item['price'],
            'subtotal'  => $item['subtotal'],
        ]);
    }

    // Hapus session cart setelah order disimpan
    session()->remove('cart');

    return redirect()->to('/user/orders')->with('success', 'Pesanan berhasil dibuat dan akan segera dikemas.');
}

public function pilih()
{
    $cart = session()->get('cart') ?? [];
    $selectedIds = $this->request->getPost('selected_items') ?? [];
    $quantities = $this->request->getPost('quantities') ?? [];

    if (empty($selectedIds)) {
        session()->setFlashdata('error', 'Silakan pilih minimal satu produk untuk checkout.');
        return redirect()->back();
    }

    $selectedIds = array_map('intval', $selectedIds); // pastikan int
    $selectedItems = [];

    foreach ($selectedIds as $id) {
        if (!isset($cart[$id])) continue;

        $product = $this->productModel->find($id);
        if (!$product) continue;

        $qty = isset($quantities[$id]) ? (int)$quantities[$id] : $cart[$id];
        $diskon = $product['diskon'] ?? 0;
        $hargaDiskon = $product['price'] - ($product['price'] * $diskon / 100);
        $subtotal = $qty * $hargaDiskon;

        $product['quantity'] = $qty;
        $product['subtotal'] = $subtotal;

        $selectedItems[] = $product;
    }

    if (empty($selectedItems)) {
        session()->setFlashdata('error', 'Produk yang dipilih tidak ditemukan di keranjang.');
        return redirect()->back();
    }

    session()->set('checkout_items', $selectedItems);
    return redirect()->to('/user/checkout');
}

public function batalkanCheckout()
{
    session()->remove('checkout_items');
    return redirect()->to('/user/keranjang');
}



}