<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Identitas Toko -->
<div class="bg-white p-4 rounded-4 shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap border border-primary-subtle">
    <div>
        <h1 class="h4 mb-1 text-primary fw-bold">🛒 Zetani - Toko Pertanian Modern</h1>
        <small class="text-secondary">📍 Jalan Nasional, Semarang | ☎️ (000) 00000</small>
    </div>
    <div>
        <a href="<?= base_url(session()->get('role') . '/produk') ?>" class="btn btn-primary mt-3 mt-sm-0 shadow-sm">
            🛍️ Lihat Semua Produk
        </a>
    </div>
</div>

<!-- Banner Promo -->
<div class="p-4 mb-4 text-white rounded-4 shadow" style="background: linear-gradient(135deg, #007bff 0%, #00bfff 100%);">
    <h2 class="fw-bold">🌿 Promo Obat Pertanian Terbaik!</h2>
    <p class="mb-0">Diskon hingga <strong>50%</strong> untuk pestisida dan nutrisi tanaman. Solusi untuk petani masa kini!</p>
</div>

<!-- Carousel Produk Terbaru -->
<?php if (!empty($produk_terbaru)): ?>
    <?php $produk_terbaru_terbatas = array_slice($produk_terbaru, 0, 5); ?>

    <div id="carouselProdukZetani" class="carousel slide mb-4 rounded-4 shadow border border-primary-subtle" data-bs-ride="carousel">

        <!-- Indikator -->
        <div class="carousel-indicators">
            <?php foreach ($produk_terbaru_terbatas as $i => $p): ?>
                <button type="button"
                        data-bs-target="#carouselProdukZetani"
                        data-bs-slide-to="<?= $i ?>"
                        class="<?= $i === 0 ? 'active' : '' ?>"
                        aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Gambar Slide -->
        <div class="carousel-inner rounded-top-4">
            <?php foreach ($produk_terbaru_terbatas as $i => $p): ?>
                <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                    <img src="<?= base_url('uploads/' . esc($p['image'])) ?>"
                         class="d-block w-100"
                         style="height: 500px; object-fit: cover; border-top-left-radius: 1rem; border-top-right-radius: 1rem;"
                         alt="Obat pertanian <?= esc($p['name']) ?>"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/1200x500?text=Gambar+Produk+Tidak+Tersedia';">
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselProdukZetani" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselProdukZetani" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Berikutnya</span>
        </button>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center border border-primary-subtle rounded-4">Tidak ada produk terbaru untuk ditampilkan saat ini.</div>
<?php endif; ?>

<?= $this->endSection() ?>
