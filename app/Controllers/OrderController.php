<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use Dompdf\Dompdf;

class OrderController extends BaseController
{
    protected $productModel, $orderModel, $orderItemModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    public function produk()
    {
        $produk = $this->productModel->findAll();
        $data = [
            'produk' => $produk,
            'username' => session()->get('username'),
            'role' => session()->get('role')
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
        $cart = session()->get('cart') ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p || !isset($p['price'])) continue;

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

        return view($data['role'] === 'admin' ? 'admin/keranjang/index' : 'user/keranjang/index', $data);
    }

    public function checkout()
    {
        $cart = session()->get('cart') ?? [];
        $items = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = $this->productModel->find($id);
            if (!$product || !isset($product['price'])) continue;

            $product['quantity'] = $qty;
            $product['subtotal'] = $qty * $product['price'];
            $total += $product['subtotal'];
            $items[] = $product;
        }

        $data = [
            'items' => $items,
            'total' => $total,
            'role' => session()->get('role')
        ];

        return view(session()->get('role') . '/checkout', $data);
    }

    public function checkoutSimpan()
    {
        $cart = session()->get('cart') ?? [];
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }

        $nama = $this->request->getPost('nama');
        $alamat = $this->request->getPost('alamat');
        $no_hp = $this->request->getPost('no_hp');
        $kelurahan = $this->request->getPost('kelurahan'); // subdistrict ID
        $layanan = $this->request->getPost('layanan');     // nama layanan pengiriman
        $ongkir = (int) $this->request->getPost('ongkir');
        $totalHarga = (int) $this->request->getPost('total_harga');

        $orderId = $this->orderModel->insert([
            'user_id'       => session()->get('username'),
            'total'         => $totalHarga,
            'city'          => $layanan, // kolom 'city' bisa diubah jadi 'layanan' jika kamu ingin lebih deskriptif
            'shipping_cost' => $ongkir,
            'nama'          => $nama,
            'alamat'        => $alamat,
            'no_hp'         => $no_hp
        ]);

        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p || !isset($p['price'])) continue;

            $diskon = $p['diskon'] ?? 0;
            $hargaAwal = $p['price'];
            $hargaDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
            $subtotal = $hargaDiskon * $qty;

            $this->orderItemModel->insert([
                'order_id'   => $orderId,
                'product_id' => $id,
                'quantity'   => $qty,
                'price'      => $hargaDiskon,
                'subtotal'   => $subtotal
            ]);
        }

        session()->remove('cart'); // Kosongkan keranjang setelah checkout

        return redirect()->to('/' . session()->get('role') . '/invoice/' . $orderId);
    }

    public function invoice($id)
    {
        $order = $this->orderModel->find($id);
        $items = $this->orderItemModel->where('order_id', $id)->findAll();

        $data = [
            'order' => $order,
            'items' => $items
        ];

        $html = view('invoice', $data);

        $dompdf = new Dompdf();
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

public function getLocation()
{
    $search = $this->request->getGet('search');
    $apiKey = getenv('RAJAONGKIR_API_KEY');

    $client = \Config\Services::curlrequest();
    $response = $client->request('GET', 'https://pro.rajaongkir.com/api/subdistrict', [
        'headers' => ['key' => $apiKey],
        'query' => ['q' => $search]
    ]);

    $data = json_decode($response->getBody(), true);

    $results = [];
    if (!empty($data['rajaongkir']['results'])) {
        foreach ($data['rajaongkir']['results'] as $item) {
            if (stripos($item['subdistrict_name'], $search) !== false) {
                $results[] = $item;
            }
        }
    }

    return $this->response->setJSON($results);
}


public function getCost()
{
    $destination = $this->request->getGet('destination');
    $apiKey = getenv('RAJAONGKIR_API_KEY');
    $origin = getenv('RAJAONGKIR_ORIGIN');

    $client = \Config\Services::curlrequest();
    $response = $client->request('POST', 'https://pro.rajaongkir.com/api/cost', [
        'headers' => [
            'key' => $apiKey,
            'content-type' => 'application/x-www-form-urlencoded',
        ],
        'form_params' => [
            'origin' => $origin,
            'originType' => 'city',
            'destination' => $destination,
            'destinationType' => 'subdistrict',
            'weight' => 1000,
            'courier' => 'jne:pos:tiki'
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    $results = [];

    foreach ($data['rajaongkir']['results'] as $courier) {
        foreach ($courier['costs'] as $cost) {
            foreach ($cost['cost'] as $detail) {
                $results[] = [
                    'service' => $cost['service'],
                    'description' => $courier['name'],
                    'cost' => $detail['value'],
                    'etd' => $detail['etd']
                ];
            }
        }
    }

    return $this->response->setJSON($results);
}

}
