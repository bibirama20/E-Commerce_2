<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card shadow rounded-4 border-0">
        <div class="card-body p-5">
            <h2 class="mb-4 text-primary fw-bold">✏️ Edit Produk</h2>

            <form action="/admin/kelola-produk/update/<?= $produk['id'] ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label text-primary">Nama Produk</label>
                    <input type="text" name="name" id="name" class="form-control rounded-3 border-primary" value="<?= esc($produk['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label text-primary">Harga</label>
                    <input type="number" name="price" id="price" class="form-control rounded-3 border-primary" value="<?= $produk['price'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="diskon" class="form-label text-primary">Diskon (%)</label>
                    <input type="number" name="diskon" id="diskon" class="form-control rounded-3 border-primary" value="<?= $produk['diskon'] ?? 0 ?>" min="0" max="100">
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label text-primary">Stok</label>
                    <input type="number" name="stock" id="stock" class="form-control rounded-3 border-primary" value="<?= $produk['stock'] ?>" required>
                </div>

                <div class="mb-3">
                    <label for="weight" class="form-label text-primary">Berat (gram)</label>
                    <input type="number" name="weight" id="weight" class="form-control rounded-3 border-primary" value="<?= $produk['weight'] ?? 0 ?>" min="1" required>
                    <div class="form-text">Masukkan berat produk dalam gram (misal: 250 untuk 250gr)</div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label text-primary">Gambar Produk</label>
                    <input type="file" name="image" id="image" class="form-control rounded-3 border-primary">
                    <?php if (!empty($produk['image'])): ?>
                        <div class="mt-3">
                            <img src="<?= base_url('uploads/' . $produk['image']) ?>" width="120" class="rounded shadow-sm border">
                            <p class="text-muted small mt-1">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">💾 Simpan</button>
                    <a href="<?= base_url('/admin/kelola-produk') ?>" class="btn btn-outline-secondary px-4 rounded-pill">← Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
