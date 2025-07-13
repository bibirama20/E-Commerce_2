<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use Dompdf\Dompdf;

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
    $tujuan   = $this->request->getPost('kelurahan'); // ✅ name="kelurahan" di form
    $layanan  = $this->request->getPost('shipping_delivery');
    $ongkir   = $this->request->getPost('shipping_cost');
    $etd      = $this->request->getPost('estimasi_hari');
    $total    = $this->request->getPost('total_harga');

    // ✅ Hitung ulang berat total dari cart
    $totalBerat = 0;
    foreach ($cart as $id => $qty) {
        $produk = $this->productModel->find($id);
        if (!$produk) continue;

        $beratItem = $produk['weight'] ?? 0;
        if ($beratItem < 1) $beratItem = 1000; // default 1kg
        $totalBerat += $beratItem * $qty;
    }

    // ✅ Simpan data pesanan ke tabel orders
    $orderId = $this->orderModel->insert([
        'user_id'           => session()->get('username'),
        'nama'              => $nama,
        'alamat'            => $alamat,
        'no_hp'             => $no_hp,
        'city'              => $tujuan,
        'shipping_cost'     => $ongkir,
        'shipping_delivery' => $layanan,
        'estimasi'          => $etd,
        'total'             => $total
    ]);

    // ✅ Simpan semua item ke order_items
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

    // ✅ Siapkan data untuk dikirim ke WhatsApp
    $items = [];
    foreach ($cart as $id => $qty) {
        $p = $this->productModel->find($id);
        if (!$p) continue;

        $items[] = [
            'name'     => $p['name'],
            'price'    => $p['price'],
            'quantity' => $qty
        ];
    }

    // ✅ Kirim notifikasi WA
    try {
        $client = \Config\Services::curlrequest();
        $client->request('POST', base_url('wa/send'), [
            'form_params' => [
                'no_hp'   => $no_hp,
                'nama'    => $nama,
                'alamat'  => $alamat,
                'layanan' => json_encode([
                    'description' => $layanan,
                    'service'     => $layanan,
                    'etd'         => $etd
                ]),
                'items'   => json_encode($items),
                'total'   => $total
            ],
            'timeout' => 5
        ]);
    } catch (\Throwable $e) {
        log_message('error', 'Gagal mengirim WA: ' . $e->getMessage());
    }

    // ✅ Bersihkan session cart
    session()->remove('cart');

    return redirect()->back()->with('success', 'Pesanan berhasil diproses dan dikirim ke WhatsApp.');
}


    public function invoice($id)
    {
        $order = $this->orderModel->find($id);
        $itemsRaw = $this->orderItemModel->where('order_id', $id)->findAll();

        $items = [];
        foreach ($itemsRaw as $item) {
            $produk = $this->productModel->find($item['product_id']);
            $items[] = [
                'name'     => $produk['name'] ?? 'Produk',
                'image'    => $produk['image'] ?? '',
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ];
        }

        $html = view('invoice', ['order' => $order, 'items' => $items]);

        $pdf = new Dompdf(['isRemoteEnabled' => true]);
        $pdf->loadHtml($html);
        $pdf->setPaper('A5', 'portrait');
        $pdf->render();
        $pdf->stream('invoice_' . $id . '.pdf', ['Attachment' => false]);
    }

}
