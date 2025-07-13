<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>🛠️ Kelola Produk</h2>

<a href="/admin/kelola-produk/tambah" class="btn btn-success mb-3">+ Tambah Produk</a>
<a href="/admin/produk/pdf" class="btn btn-danger mb-3 float-end">🖨 Cetak PDF</a>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<!-- 🔍 FORM PENCARIAN, FILTER, SORT -->
<form method="get" action="/admin/kelola-produk" class="mb-4">
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" name="keyword" class="form-control" placeholder="Cari nama produk..."
                value="<?= esc($keyword ?? '') ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="Harga min"
                value="<?= esc($min_price ?? '') ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="Harga max"
                value="<?= esc($max_price ?? '') ?>">
        </div>
        <div class="col-md-2">
            <select name="sort" class="form-select">
                <option value="">Urutkan</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Termahal</option>
            </select>
        </div>
        <div class="col-md-3 d-flex">
            <button class="btn btn-primary me-2" type="submit">Filter</button>
            <a href="/admin/kelola-produk" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

<!-- 📋 TABEL PRODUK -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Diskon (%)</th>
            <th>Harga Setelah Diskon</th>
            <th>Stok</th>
            <th>Berat (gram)</th> <!-- ✅ Tambahkan kolom Berat -->
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $p): ?>
        <tr>
            <td><?= esc($p['name']) ?></td>
            <td>Rp<?= number_format($p['price']) ?></td>
            <td><?= isset($p['diskon']) ? $p['diskon'] . '%' : '0%' ?></td>
            <td>
                Rp<?php 
                    $diskon = isset($p['diskon']) ? $p['diskon'] : 0;
                    $hargaSetelahDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                    echo number_format($hargaSetelahDiskon);
                ?>
            </td>
            <td><?= esc($p['stock']) ?></td>
            <td><?= esc($p['weight'] ?? '-') ?></td> <!-- ✅ Tampilkan berat produk -->
            <td>
                <?php if (!empty($p['image'])): ?>
                    <img src="<?= base_url('uploads/' . $p['image']) ?>" width="80">
                <?php else: ?>
                    <span class="text-muted">Tidak ada gambar</span>
                <?php endif ?>
            </td>
            <td>
                <a href="/admin/kelola-produk/edit/<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="/admin/kelola-produk/delete/<?= $p['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>


<?= $this->endSection() ?>
