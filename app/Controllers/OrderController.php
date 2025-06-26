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
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
    }

    public function produk()
    {
        $produk = $this->productModel->findAll();
        $data = [
            'produk'   => $produk,
            'username' => session()->get('username'),
            'role'     => session()->get('role')
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

        $nama     = $this->request->getPost('nama');
        $alamat   = $this->request->getPost('alamat');
        $no_hp    = $this->request->getPost('no_hp');
        $tujuan   = $this->request->getPost('destination');
        $layanan  = $this->request->getPost('shipping_delivery');
        $ongkir   = $this->request->getPost('shipping_cost');
        $etd      = $this->request->getPost('estimasi_hari');
        $total    = $this->request->getPost('total_harga');

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

public function getLocation()
{
    $search = $this->request->getGet('search');
    $apiKey = getenv('RAJAONGKIR_API_KEY');

    try {
        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', 'https://api.rajaongkir.com/starter/city', [
            'headers' => ['key' => $apiKey]
        ]);

        $data = json_decode($response->getBody(), true);
        $results = [];

        if (!empty($data['rajaongkir']['results'])) {
            foreach ($data['rajaongkir']['results'] as $item) {
                if (stripos($item['city_name'], $search) !== false) {
                    $results[] = [
                        'id'   => $item['city_id'],
                        'text' => $item['city_name'] . ', ' . $item['province']
                    ];
                }
            }
        }

        return $this->response->setJSON(['results' => $results]);
    } catch (\Exception $e) {
        return $this->response->setJSON(['results' => [], 'error' => $e->getMessage()]);
    }
}


   public function getCost()
{
    $destination = $this->request->getGet('destination');
    $apiKey = getenv('RAJAONGKIR_API_KEY');
    $origin = getenv('RAJAONGKIR_ORIGIN') ?? '501';

    try {
        $client = \Config\Services::curlrequest();
        $response = $client->request('POST', 'https://api.rajaongkir.com/starter/cost', [
            'headers' => [
                'key' => $apiKey,
                'content-type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'origin' => $origin,
                'destination' => $destination,
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
                        'courier' => $courier['code'],
                        'service' => $cost['service'],
                        'etd' => $detail['etd'],
                        'cost' => $detail['value']
                    ];
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $results]);
    } catch (\Exception $e) {
        return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
    }
}



}
