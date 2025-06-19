<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<h2>🛍️ Daftar Produk</h2>

<div class="row">
    <?php foreach ($produk as $p): ?>
        <div class="col-md-4 col-sm-6 mb-4 d-flex">
            <div class="card shadow-sm h-100 border-0 position-relative w-100">

                <!-- Label Diskon -->
                <?php if (!empty($p['diskon'])): ?>
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Diskon <?= $p['diskon'] ?>%</span>
                <?php endif; ?>

                <!-- Gambar -->
                <img src="<?= base_url('uploads/' . $p['image']) ?>" class="card-img-top" alt="<?= esc($p['name']) ?>" style="height: 200px; object-fit: cover;">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= esc($p['name']) ?></h5>

                    <!-- Harga -->
                    <?php if (!empty($p['diskon'])): 
                        $diskon = $p['diskon'];
                        $hargaDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                    ?>
                        <p class="mb-1 text-muted text-decoration-line-through">Rp<?= number_format($p['price']) ?></p>
                        <p class="fw-bold text-danger">Rp<?= number_format($hargaDiskon) ?> <small class="text-muted">(<?= $diskon ?>% OFF)</small></p>
                    <?php else: ?>
                        <p class="fw-bold">Rp<?= number_format($p['price']) ?></p>
                    <?php endif; ?>

                    <!-- Tombol -->
                    <a href="<?= base_url(session()->get('role') . '/keranjang/tambah/' . $p['id']) ?>" class="btn btn-sm btn-primary mt-auto">Tambah ke Keranjang</a>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<?= $this->endSection() ?>
