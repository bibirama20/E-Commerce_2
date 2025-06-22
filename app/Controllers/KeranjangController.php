<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class KeranjangController extends BaseController
{
    public function tambah($id)
    {
        $productModel = new ProductModel();
        $produk = $productModel->find($id);

        if (!$produk) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan.');
        }

        $keranjang = session()->get('keranjang') ?? [];

        if (isset($keranjang[$id])) {
            $keranjang[$id]['qty'] += 1;
        } else {
            $keranjang[$id] = [
                'id'     => $produk['id'],
                'name'   => $produk['name'],
                'price'  => $produk['price'],
                'qty'    => 1,
                'diskon' => $produk['diskon'] ?? 0 
            ];
        }

        session()->set('keranjang', $keranjang);
        return redirect()->to('/pembeli/keranjang')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function index()
    {
        $sessionCart = session()->get('keranjang') ?? [];
        $productModel = new ProductModel();
        $items = [];
        $total = 0;

        foreach ($sessionCart as $item) {
            $produk = $productModel->find($item['id']);

            $price = $produk['price'];
            $diskon = $produk['diskon'] ?? 0;
            $priceAfterDiskon = $price - ($price * $diskon / 100);
            $subtotal = $priceAfterDiskon * $item['qty'];

            $items[] = [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'price'    => $price,
                'diskon'   => $diskon, 
                'quantity' => $item['qty'],
                'subtotal' => $subtotal
            ];

            $total += $subtotal;
        }

        $data = [
            'items' => $items,
            'total' => $total,
            'role'  => 'pembeli'
        ];

        return view('keranjang/index', $data);
    }

    public function hapus($id)
    {
        $keranjang = session()->get('keranjang') ?? [];

        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
        }

        session()->set('keranjang', $keranjang);
        return redirect()->to('/pembeli/keranjang')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function update()
    {
        $quantities = $this->request->getPost('quantities');
        $keranjang = session()->get('keranjang') ?? [];

        foreach ($quantities as $id => $qty) {
            if (isset($keranjang[$id])) {
                $keranjang[$id]['qty'] = (int)$qty;
            }
        }

        session()->set('keranjang', $keranjang);
        return redirect()->to('/pembeli/keranjang')->with('success', 'Jumlah produk berhasil diperbarui.');
    }
}
