<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use Dompdf\Dompdf;

class ProductController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // Halaman kelola produk (admin only)
    public function kelola()
{
    $keyword   = trim($this->request->getGet('keyword'));
    $minPrice  = $this->request->getGet('min_price');
    $maxPrice  = $this->request->getGet('max_price');
    $sort      = $this->request->getGet('sort');

    // Mulai builder query
    $builder = $this->productModel;

    // 1. Filter nama produk (jika ada)
    if (!empty($keyword)) {
        $builder = $builder->like('name', $keyword);
    }

    // 2. Filter harga minimum
    if ($minPrice !== null && is_numeric($minPrice)) {
        $builder = $builder->where('price >=', floatval($minPrice));
    }

    // 3. Filter harga maksimum
    if ($maxPrice !== null && is_numeric($maxPrice)) {
        $builder = $builder->where('price <=', floatval($maxPrice));
    }

    // 4. Sorting
    switch ($sort) {
        case 'name_asc':
            $builder = $builder->orderBy('name', 'ASC');
            break;
        case 'price_asc':
            $builder = $builder->orderBy('price', 'ASC');
            break;
        case 'price_desc':
            $builder = $builder->orderBy('price', 'DESC');
            break;
        default:
            $builder = $builder->orderBy('id', 'DESC'); // default sorting terbaru
    }

    // Eksekusi query
    $produk = $builder->findAll();

    // Kirim ke view
    $data = [
        'produk'    => $produk,
        'keyword'   => $keyword,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
        'sort'      => $sort
    ];

    return view('admin/produk/kelola', $data);
}


    // Form tambah produk
    public function create()
    {
        return view('admin/produk/create');
    }

    // Simpan produk baru
    public function store()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'   => 'required',
            'price'  => 'required|numeric|greater_than[0]',
            'stock'  => 'required|integer|greater_than_equal_to[0]',
            'diskon' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'image'  => 'uploaded[image]|is_image[image]|max_size[image,2048]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $image = $this->request->getFile('image');
        $imageName = null;

        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageName = $image->getRandomName();
            $image->move('uploads', $imageName);
        }

        $data = [
            'name'   => $this->request->getPost('name'),
            'price'  => floatval($this->request->getPost('price')),
            'stock'  => intval($this->request->getPost('stock')),
            'diskon' => intval($this->request->getPost('diskon')),
            'image'  => $imageName
        ];

        $this->productModel->save($data);
        return redirect()->to('/admin/kelola-produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Tampilkan form edit produk
    public function edit($id)
    {
        $produk = $this->productModel->find($id);

        if (!$produk) {
            return redirect()->to('/admin/kelola-produk')->with('error', 'Produk tidak ditemukan.');
        }

        return view('admin/produk/edit', ['produk' => $produk]);
    }

    // Simpan update produk
    public function update($id)
    {
        $produkLama = $this->productModel->find($id);

        if (!$produkLama) {
            return redirect()->to('/admin/kelola-produk')->with('error', 'Produk tidak ditemukan.');
        }

        // Validasi
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'   => 'required',
            'price'  => 'required|numeric|greater_than[0]',
            'stock'  => 'required|integer|greater_than_equal_to[0]',
            'diskon' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'image'  => 'permit_empty|is_image[image]|max_size[image,2048]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $image = $this->request->getFile('image');
        $imageName = $produkLama['image'];

        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageName = $image->getRandomName();
            $image->move('uploads', $imageName);

            // Hapus gambar lama jika ada
            if (!empty($produkLama['image']) && file_exists('uploads/' . $produkLama['image'])) {
                unlink('uploads/' . $produkLama['image']);
            }
        }

        $data = [
            'name'   => $this->request->getPost('name'),
            'price'  => floatval($this->request->getPost('price')),
            'stock'  => intval($this->request->getPost('stock')),
            'diskon' => intval($this->request->getPost('diskon')),
            'image'  => $imageName
        ];

        $this->productModel->update($id, $data);
        return redirect()->to('/admin/kelola-produk')->with('success', 'Produk berhasil diperbarui.');
    }

    // Hapus produk
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('order_items');
        $cek = $builder->where('product_id', $id)->countAllResults();

        if ($cek > 0) {
            return redirect()->to('/admin/kelola-produk')->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam pesanan.');
        }

        $produk = $this->productModel->find($id);
        if ($produk && !empty($produk['image']) && file_exists('uploads/' . $produk['image'])) {
            unlink('uploads/' . $produk['image']);
        }

        $this->productModel->delete($id);
        return redirect()->to('/admin/kelola-produk')->with('success', 'Produk berhasil dihapus.');
    }

    // Cetak PDF daftar produk
    public function pdf()
    {
        $data = [
            'produk' => $this->productModel->findAll()
        ];

        $html = view('admin/produk/pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('data-produk.pdf', ['Attachment' => false]);
    }

    public function dashboard()
{
    $data = [
        'produk' => $this->productModel->findAll()
    ];
    return view('admin/dashboard', $data);
}

}
