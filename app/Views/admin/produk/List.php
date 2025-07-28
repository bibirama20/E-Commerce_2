<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Flash Message -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm rounded"><?= session()->getFlashdata('success') ?></div>
<?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger shadow-sm rounded"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- Filter Form -->
<form method="get" class="row g-3 mb-5 align-items-end bg-light p-4 rounded shadow-sm border">
    <h2 class="mb-4 fw-bold text-primary text-center">
    <i class="bi bi-shop me-2"></i> Daftar Produk
</h2>
    <div class="col-md-3">
        <label for="keyword" class="form-label">🔍 Kata Kunci</label>
        <input type="text" name="keyword" class="form-control shadow-sm" placeholder="Cari nama produk..." value="<?= esc($keyword ?? '') ?>">
    </div>
    <div class="col-md-2">
        <label for="min_price" class="form-label">💰 Harga Min</label>
        <input type="number" name="min_price" class="form-control shadow-sm" placeholder="Min" value="<?= esc($min_price ?? '') ?>">
    </div>
    <div class="col-md-2">
        <label for="max_price" class="form-label">💰 Harga Max</label>
        <input type="number" name="max_price" class="form-control shadow-sm" placeholder="Max" value="<?= esc($max_price ?? '') ?>">
    </div>
    <div class="col-md-2">
        <label for="sort" class="form-label">Urutkan</label>
        <select name="sort" class="form-select shadow-sm">
            <option value="">Pilih</option>
            <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
            <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Harga Termahal</option>
            <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
        </select>
    </div>
    <div class="col-md-1 d-grid">
        <button type="submit" class="btn btn-outline-primary shadow-sm"> Filter </button>
    </div>
    <div class="col-md-2 d-grid">
        <a href="<?= base_url(session()->get('role') . '/produk') ?>" class="btn btn-outline-secondary shadow-sm"> Reset </a>
    </div>
</form>

<!-- Produk Grid -->
 <form method="get" class="row g-3 mb-5 align-items-end bg-light p-4 rounded shadow-sm border">
<div class="row">
    <?php if (!empty($produk)): ?>
        <?php foreach ($produk as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex">
                <div class="card shadow-sm h-100 border-0 rounded-4 w-100 position-relative">

                    <!-- Diskon -->
                    <?php if (!empty($p['diskon'])): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-pill px-3 py-2 fs-6 shadow">
                            -<?= $p['diskon'] ?>%
                        </span>
                    <?php endif; ?>

                    <!-- Gambar Produk -->
                    <img src="<?= base_url('uploads/' . $p['image']) ?>" class="card-img-top rounded-top" alt="<?= esc($p['name']) ?>"
                         style="height: 200px; object-fit: cover;">

                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-semibold text-dark mb-2"><?= esc($p['name']) ?></h6>

                        <!-- Harga -->
                        <?php if (!empty($p['diskon'])):
                            $diskon = $p['diskon'];
                            $hargaDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                        ?>
                            <p class="mb-0 text-muted text-decoration-line-through small">Rp<?= number_format($p['price']) ?></p>
                            <p class="fw-bold text-danger fs-6 mb-2">Rp<?= number_format($hargaDiskon) ?></p>
                        <?php else: ?>
                            <p class="fw-bold text-primary fs-6 mb-2">Rp<?= number_format($p['price']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            </form>
        <?php endforeach ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">Produk tidak ditemukan.</div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
