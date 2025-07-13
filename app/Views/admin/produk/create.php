<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Tambah Produk</h2>
<form method="post" action="/admin/kelola-produk/simpan" enctype="multipart/form-data">
    <input name="name" placeholder="Nama" class="form-control mb-2" required>

    <input name="stock" type="number" placeholder="Stok" class="form-control mb-2" required>

    <input name="price" type="number" placeholder="Harga" class="form-control mb-2" required>

    <input name="diskon" type="number" placeholder="Diskon (%)" class="form-control mb-2" min="0" max="100">

    <!-- Tambahkan input berat -->
    <input name="weight" type="number" placeholder="Berat (gram)" class="form-control mb-2" min="1" required>
    <div class="form-text mb-2">Berat produk dalam gram, misal 250 = 250gr</div>

    <input type="file" name="image" class="form-control mb-2" required>
    <div class="form-text mb-2">Pilih gambar produk (.jpg, .png), maksimal 2MB</div>

    <button class="btn btn-primary">Simpan</button>
</form>

<?= $this->endSection() ?>
