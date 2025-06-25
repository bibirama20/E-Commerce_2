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
    protected $apikey;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
    }

    public function produk()
    {
        $produk = $this->productModel->findAll();

        $data = [
            'produk' => $produk,
            'username' => session()->get('username'),
            'role' => session()->get('role')
        ];

        if ($data['role'] === 'admin') {
            return view('admin/produk/list', $data);
        } else {
            return view('user/produk/list', $data);
        }
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
        $cart = session()->get('cart') ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);

            if (!$p || !isset($p['price'])) {
                continue;
            }

            $p['quantity'] = $qty;
            $p['subtotal'] = $qty * $p['price'];
            $total += $p['subtotal'];
            $items[] = $p;
        }

        $data = [
            'items' => $items,
            'total' => $total,
            'role' => session()->get('role')
        ];

        if ($data['role'] === 'admin') {
            return view('admin/keranjang/index', $data);
        } else {
            return view('user/keranjang/index', $data);
        }
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
        $hargaAwal = $p['price'];
        $hargaDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
        $subtotal = $hargaDiskon * $qty;

        $p['quantity'] = $qty;
        $p['subtotal'] = $subtotal;
        $items[] = $p;

        $total += $subtotal;
    }

    return view(session()->get('role') . '/checkout', [
        'role'  => session()->get('role'),
        'items' => $items,
        'total' => $total
    ]);
}


    public function checkoutSimpan()
{
    $cart = session()->get('cart') ?? [];
    if (empty($cart)) {
        return redirect()->back()->with('error', 'Keranjang kosong.');
    }

    $nama     = $this->request->getPost('nama');
    $alamat   = $this->request->getPost('alamat');
    $no_hp    = $this->request->getPost('no_hp');
    $destination = $this->request->getPost('destination');
    $ongkir   = $this->request->getPost('shipping_cost');
    $layanan  = $this->request->getPost('shipping_delivery');
    $etd      = $this->request->getPost('estimasi_hari');
    $total    = $this->request->getPost('total_harga');

    $orderId = $this->orderModel->insert([
        'user_id'           => session()->get('username'),
        'nama'              => $nama,
        'alamat'            => $alamat,
        'no_hp'             => $no_hp,
        'city'              => $destination,
        'shipping_cost'     => $ongkir,
        'shipping_delivery' => $layanan,
        'estimasi'          => $etd,
        'total'             => $total
    ]);

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
    return redirect()->to('/' . session()->get('role') . '/invoice/' . $orderId);
}


    public function invoice($id)
{
    $order = $this->orderModel->find($id);
    $itemsRaw = $this->orderItemModel->where('order_id', $id)->findAll();

    // Ambil detail produk (nama & gambar)
    $items = [];
    foreach ($itemsRaw as $item) {
        $produk = $this->productModel->find($item['product_id']);
        $items[] = [
            'name' => $produk['name'] ?? 'Produk',
            'image' => $produk['image'] ?? null,
            'quantity' => $item['quantity'],
            'subtotal' => $item['subtotal']
        ];
    }

    $data = [
        'order' => $order,
        'items' => $items
    ];

    $html = view('invoice', $data);

    $dompdf = new Dompdf(['isRemoteEnabled' => true]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A5', 'portrait');
    $dompdf->render();
    $dompdf->stream("invoice_{$id}.pdf", ['Attachment' => false]);
}


    public function updateKeranjang()
    {
        $cart = session()->get('cart') ?? [];
        $quantities = $this->request->getPost('quantities');

        if (!$quantities) {
            return redirect()->back()->with('error', 'Tidak ada data jumlah yang dikirim.');
        }

        foreach ($quantities as $id => $qty) {
            $qty = (int) $qty;

            if ($qty < 1) {
                unset($cart[$id]);
            } else {
                $cart[$id] = $qty;
            }
        }

        session()->set('cart', $cart);
        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function cekOngkir()
    {
        $cfg = config('RajaOngkir');
        $client = service('rajaongkir');
        $response = $client->post('cost', [
            'headers' => ['key' => $cfg->apiKey],
            'form_params' => [
                'origin' => $cfg->origin,
                'destination' => $this->request->getPost('destination'),
                'weight' => $this->request->getPost('weight'),
                'courier' => $this->request->getPost('courier')
            ]
        ]);
        return $this->response->setJSON($response->getJSON());
    }

    public function batalCheckout()
    {
        session()->remove('cart');
        return redirect()->to('/' . session()->get('role') . '/produk')
            ->with('success', 'Checkout dibatalkan. Keranjang dikosongkan.');
    }

    public function getLocation()
{
    try {
        $search = $this->request->getGet('search');
        $response = $this->client->request(
            'GET',
            'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . urlencode($search) . '&limit=50',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'key'    => $this->apiKey,
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);
        $locations = $body['data'] ?? [];

        $formatted = array_map(function ($loc) {
            return [
                'id'   => $loc['id'] ?? $loc['location_id'],
                'text' => $loc['label'] ?? $loc['subdistrict_name'] . ', ' . $loc['city_name']
            ];
        }, $locations);

        return $this->response->setJSON(['results' => $formatted]);

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        return $this->response->setJSON([
            'results' => [],
            'error' => 'Limit RajaOngkir API sudah habis. Coba lagi besok.'
        ]);
    }
}

    public function getCost()
{
    $destination = $this->request->getGet('destination');
    $weight = 1000;
    $courier = $this->request->getGet('courier') ?? 'jne';

    if (!$destination) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tujuan tidak valid']);
    }

    $response = $this->client->request(
        'POST',
        'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost',
        [
            'multipart' => [
                ['name' => 'origin', 'contents' => '64999'],
                ['name' => 'destination', 'contents' => $destination],
                ['name' => 'weight', 'contents' => $weight],
                ['name' => 'courier', 'contents' => $courier]
            ],
            'headers' => [
                'accept' => 'application/json',
                'key'    => $this->apiKey,
            ],
        ]
    );

    $body = json_decode($response->getBody(), true);

    if (isset($body['data'])) {
        $services = array_map(function ($item) use ($courier) {
            return [
                'courier' => $courier,
                'service' => $item['service'],
                'etd'     => $item['etd'],
                'cost'    => $item['cost']
            ];
        }, $body['data']);

        return $this->response->setJSON(['success' => true, 'data' => $services]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Layanan tidak tersedia']);
}

}
