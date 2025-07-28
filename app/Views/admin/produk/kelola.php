<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">
        <i class="bi bi-box-seam"></i> Kelola Produk
    </h2>
    <div>
        <a href="/admin/produk/pdf" class="btn btn-outline-danger">
            <i class="bi bi-printer-fill"></i> Cetak PDF
        </a>
        <a href="/admin/kelola-produk/tambah" class="btn btn-primary ms-2">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success shadow-sm"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<!-- 🔍 Filter Produk -->
<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body">
        <form method="get" action="/admin/kelola-produk">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control" placeholder="🔍 Cari nama produk..." value="<?= esc($keyword ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Harga min" value="<?= esc($min_price ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Harga max" value="<?= esc($max_price ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="">Urutkan</option>
                        <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Termurah</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Termahal</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button class="btn btn-primary me-2" type="submit"><i class="bi bi-filter"></i> Filter</button>
                    <a href="/admin/kelola-produk" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 📦 Tabel Produk -->
<div class="table-responsive shadow-sm rounded-4 bg-white">
    <table class="table table-hover table-bordered align-middle mb-0">
        <thead class="table-primary text-center">
            <tr>
                <th>Produk</th>
                <th>Harga Asli</th>
                <th>Diskon</th>
                <th>Harga Diskon</th>
                <th>Stok</th>
                <th>Berat</th>
                <th>Gambar</th>
                <th style="width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php foreach ($produk as $p): ?>
            <tr>
                <td><strong><?= esc($p['name']) ?></strong></td>
                <td>Rp<?= number_format($p['price']) ?></td>
                <td>
                    <?php if (!empty($p['diskon'])): ?>
                        <span class="badge bg-danger"><?= $p['diskon'] ?>%</span>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif ?>
                </td>
                <td class="text-success">
                    Rp<?php 
                        $diskon = $p['diskon'] ?? 0;
                        $hargaSetelahDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                        echo number_format($hargaSetelahDiskon);
                    ?>
                </td>
                <td><?= esc($p['stock']) ?></td>
                <td><?= esc($p['weight'] ?? '-') ?> gr</td>
                <td>
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= base_url('uploads/' . $p['image']) ?>" alt="gambar" class="img-thumbnail" style="max-width: 80px;">
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif ?>
                </td>
                <td>
                    <div class="d-flex flex-column gap-1">
                        <a href="/admin/kelola-produk/edit/<?= $p['id'] ?>" class="btn btn-sm btn-warning w-100">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="/admin/kelola-produk/delete/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger w-100"
                           onclick="return confirm('Yakin ingin menghapus produk ini?')">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
