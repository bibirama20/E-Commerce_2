<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>🛍️ Daftar Produk</h2>

<!-- Flash Message -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Filter Form -->
<form method="get" class="row g-3 mb-4">
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
            <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
            <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Harga Termahal</option>
            <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
        </select>
    </div>
    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Terapkan</button>
    </div>
    <div class="col-md-1 d-grid">
       <a href="<?= base_url(session()->get('role') . '/produk') ?>" class="btn btn-secondary">Reset</a>

    </div>
</form>

<!-- Produk Grid -->
<div class="row">
    <?php if (!empty($produk)): ?>
        <?php foreach ($produk as $p): ?>
            <div class="col-md-4 col-sm-6 mb-4 d-flex">
                <div class="card shadow-sm h-100 border-0 position-relative w-100">

                    <!-- Label Diskon -->
                    <?php if (!empty($p['diskon'])): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                            Diskon <?= $p['diskon'] ?>%
                        </span>
                    <?php endif; ?>

                    <!-- Gambar Produk -->
                    <img src="<?= base_url('uploads/' . $p['image']) ?>" class="card-img-top"
                         alt="<?= esc($p['name']) ?>" style="height: 200px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= esc($p['name']) ?></h5>

                        <!-- Harga -->
                        <?php if (!empty($p['diskon'])):
                            $diskon = $p['diskon'];
                            $hargaDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                        ?>
                            <p class="mb-1 text-muted text-decoration-line-through">
                                Rp<?= number_format($p['price']) ?>
                            </p>
                            <p class="fw-bold text-danger">
                                Rp<?= number_format($hargaDiskon) ?>
                                <small class="text-muted">(<?= $diskon ?>% OFF)</small>
                            </p>
                        <?php else: ?>
                            <p class="fw-bold">Rp<?= number_format($p['price']) ?></p>
                        <?php endif; ?>

                        <!-- Tombol Keranjang -->
                        <a href="<?= base_url(session()->get('role') . '/keranjang/tambah/' . $p['id']) ?>"
                           class="btn btn-sm btn-primary mt-auto">
                            Tambah ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">Produk tidak ditemukan.</div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
