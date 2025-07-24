<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $role = service('uri')->getSegment(1); ?>

<h2>📋 Kelola Pesanan</h2>

<!-- 📊 Statistik Status -->
<div class="row mb-4">
    <?php
        $statusList = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai'];
        $colors = ['Belum Bayar' => 'secondary', 'Dikemas' => 'warning', 'Dikirim' => 'primary', 'Selesai' => 'success'];
    ?>
    <?php foreach ($statusList as $status): ?>
        <div class="col-md-3">
            <div class="card border-<?= $colors[$status] ?>">
                <div class="card-body text-<?= $colors[$status] ?>">
                    <h5 class="card-title"><?= $status ?></h5>
                    <h3 class="fw-bold">
                        <?= isset($jumlahStatus[strtolower($status)]) ? $jumlahStatus[strtolower($status)] : 0 ?>
                    </h3>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<!--filter pdf -->
<form method="get" action="<?= base_url("$role/orders/printAll") ?>" target="_blank" class="row g-2 mb-3 d-print-none">
    <div class="col-md-3">
        <label>Dari Tanggal</label>
        <input type="date" name="from" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label>Sampai Tanggal</label>
        <input type="date" name="to" class="form-control" required>
    </div>
    <div class="col-md-3 align-self-end">
        <input type="hidden" name="pdf" value="yes">
        <button type="submit" class="btn btn-outline-primary">🖨️ Cetak PDF</button>
    </div>
</form>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<!-- 🔍 FORM FILTER TANGGAL / NAMA -->
<form method="get" action="/admin/orders" class="mb-4">
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" name="nama" class="form-control" placeholder="Cari nama pemesan..." value="<?= esc($nama ?? '') ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="tanggal" class="form-control" value="<?= esc($tanggal ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex">
            <button class="btn btn-primary me-2" type="submit">Filter</button>
            <a href="/admin/orders" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

<!-- ✅ FILTER STATUS -->
<div class="mb-4">
    <?php 
        $statusFilters = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai'];
        $statusGet = $_GET['status'] ?? '';
    ?>
    <a href="/admin/orders" class="btn btn-sm me-2 <?= empty($statusGet) ? 'btn-dark' : 'btn-outline-dark' ?>">Semua</a>
    <?php foreach ($statusFilters as $s): ?>
        <a href="<?= base_url('admin/orders?status=' . urlencode($s)) ?>" 
           class="btn btn-sm me-2 <?= ($statusGet === $s) ? 'btn-dark' : 'btn-outline-dark' ?>">
           <?= esc($s) ?>
        </a>
    <?php endforeach ?>
</div>

<!-- 📋 TABEL PESANAN -->
<table class="table table-bordered">
    <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Nama Pemesan</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Layanan</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)) : ?>
            <tr>
                <td colspan="9" class="text-center">Belum ada pesanan.</td>
            </tr>
        <?php else : ?>
            <?php foreach ($orders as $order) : ?>
                <tr>
                    <td><?= esc($order['id']) ?></td>
                    <td><?= esc($order['nama']) ?></td>
                    <td><?= esc($order['no_hp']) ?></td>
                    <td><?= esc($order['alamat']) ?></td>
                    <td><?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</td>
                    <td><span class="badge bg-success">Rp <?= number_format($order['total'], 0, ',', '.') ?></span></td>
                    <td>
                        <?php
                            $status = strtolower($order['status']);
                            $badge = match ($status) {
                                'belum bayar' => 'secondary',
                                'dikemas'     => 'warning',
                                'dikirim'     => 'primary',
                                'selesai'     => 'success',
                                default       => 'dark'
                            };
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= esc(ucwords($order['status'])) ?></span>

                        <?php if ($order['status'] !== 'Selesai') : ?>
                            <div class="mt-1">
                                <a href="/admin/order/status/ubah/<?= $order['id'] ?>" class="btn btn-sm btn-outline-dark">➡️ Ubah Status</a>
                            </div>
                        <?php endif ?>
                    </td>
                    <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <a href="/admin/order/resi/<?= $order['id'] ?>" class="btn btn-sm btn-success mb-1">📦 Cetak Resi</a>
                        <a href="/admin/order/detail/<?= $order['id'] ?>" class="btn btn-sm btn-info">🔍 Detail</a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>
