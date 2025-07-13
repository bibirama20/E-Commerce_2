<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Edit Produk</h2>

<form action="/admin/kelola-produk/update/<?= $produk['id'] ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" name="name" id="name" class="form-control" value="<?= esc($produk['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" name="price" id="price" class="form-control" value="<?= $produk['price'] ?>" required>
    </div>

    <div class="mb-3">
        <label for="diskon" class="form-label">Diskon (%)</label>
        <input type="number" name="diskon" id="diskon" class="form-control"
            value="<?= $produk['diskon'] ?? 0 ?>" min="0" max="100">
    </div>

    <div class="mb-3">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" name="stock" id="stock" class="form-control" value="<?= $produk['stock'] ?>" required>
    </div>

    <!-- Input Berat Produk -->
    <div class="mb-3">
        <label for="weight" class="form-label">Berat (gram)</label>
        <input type="number" name="weight" id="weight" class="form-control" value="<?= $produk['weight'] ?? 0 ?>" min="1" required>
        <div class="form-text">Masukkan berat produk dalam gram (misal: 250 untuk 250gr)</div>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Gambar Produk</label>
        <input type="file" name="image" id="image" class="form-control">
        <?php if (!empty($produk['image'])): ?>
            <div class="mt-2">
                <img src="<?= base_url('uploads/' . $produk['image']) ?>" width="120">
                <p class="text-muted">Gambar saat ini</p>
            </div>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Edit Produk</button>
    <a href="<?= base_url('/admin/kelola-produk') ?>" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>
