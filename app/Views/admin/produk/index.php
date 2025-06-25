<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Daftar Produk</h1>
<a href="/admin/produk/create" class="btn btn-primary mb-3">+ Tambah Produk</a>

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
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?= esc($p['name']) ?></td>
        <td>Rp<?= number_format($p['price']) ?></td>
        <td><?= $p['stock'] ?></td>
        <td><img src="<?= base_url('uploads/' . $p['image']) ?>" width="80"></td>
        <td>
          <a href="/admin/produk/edit/<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="/admin/produk/delete/<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
