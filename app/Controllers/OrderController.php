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
                continue; // lewati jika produk tidak ditemukan atau tanpa harga
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
        $data = [
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
        $ekspedisi = $this->request->getPost('ekspedisi');

        $ongkir = 15000; // dummy ongkir

        $total = 0;
        foreach ($cart as $id => $qty) {
            $p = $this->productModel->find($id);
            if (!$p || !isset($p['price'])) continue;
            $total += $p['price'] * $qty;
        }

        $grandTotal = $total + $ongkir;

        $orderId = $this->orderModel->insert([
            'user_id' => session()->get('username'),
            'total' => $grandTotal,
            'city' => $ekspedisi,
            'shipping_cost' => $ongkir,
            'nama' => $nama,
            'alamat' => $alamat,
            'no_hp' => $no_hp
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
                'price'      => $hargaDiskon, // tambahkan field ini di model & tabel
                'subtotal'   => $subtotal
            ]);
        }

        $role = session()->get('role');
        return redirect()->to("/$role/invoice/" . $orderId);

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
            unset($cart[$id]); // Hapus item kalau jumlah < 1
        } else {
            $cart[$id] = $qty; // Update jumlah
        }
    }

    session()->set('cart', $cart); // Simpan kembali
    return redirect()->back()->with('success', 'Keranjang berhasil diperbarui.');
}

}