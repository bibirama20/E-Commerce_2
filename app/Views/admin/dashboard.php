<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Banner Promo -->
<div class="p-4 mb-4 text-white rounded" style="background: linear-gradient(135deg, rgb(45, 191, 207) 0%, rgb(62, 196, 118) 100%);">
    <h2 class="fw-bold">🌿 Promo Obat Pertanian Terbaik!</h2>
    <p class="mb-0">Diskon hingga <strong>50%</strong> untuk pestisida, dan nutrisi tanaman. Cocok untuk petani masa kini!</p>
</div>

<!-- Carousel Produk Terbaru -->
<?php if (!empty($produk_terbaru)): ?>
    <?php $produk_terbaru_terbatas = array_slice($produk_terbaru, 0, 5); ?>

    <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel">

        <!-- Indikator Slide -->
        <div class="carousel-indicators">
            <?php foreach ($produk_terbaru_terbatas as $i => $p): ?>
                <button type="button"
                        data-bs-target="#carouselExampleIndicators"
                        data-bs-slide-to="<?= $i ?>"
                        class="<?= $i === 0 ? 'active' : '' ?>"
                        aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Slide Isi -->
        <div class="carousel-inner rounded shadow">
            <?php foreach ($produk_terbaru_terbatas as $i => $p): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <img src="<?= base_url('uploads/' . esc($p['image'])) ?>"
                         class="d-block w-100"
                         style="height: 550px; object-fit: cover;"
                         alt="Obat pertanian <?= esc($p['name']) ?>"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/1200x300?text=Gambar+Obat+Pertanian+Tidak+Tersedia';">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Berikutnya</span>
        </button>
    </div>
<?php else: ?>
    <div class="alert alert-warning">Tidak ada produk terbaru untuk ditampilkan.</div>
<?php endif; ?>

<?= $this->endSection() ?>
