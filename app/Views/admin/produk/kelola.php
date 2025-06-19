<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2> 🛠️ Kelola Produk</h2>
<a href="/admin/kelola-produk/tambah" class="btn btn-success mb-3">+ Tambah Produk</a>
<a href="/admin/produk/pdf" class="btn btn-danger mb-3 float-end">🖨️ Cetak PDF</a>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $p): ?>
        <tr>
            <td><?= $p['name'] ?></td>
            <td>Rp<?= number_format($p['price']) ?></td>
            <td><?= $p['stock'] ?></td>
            <td><img src="<?= base_url('uploads/' . $p['image']) ?>" width="80"></td>
            <td>
                <a href="/admin/kelola-produk/edit/<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="/admin/kelola-produk/delete/<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection()?>