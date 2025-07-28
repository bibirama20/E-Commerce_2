<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $role = service('uri')->getSegment(1); ?>

<div class="container mt-4">
    <h2 class="mb-4 text-primary-emphasis">📦 <strong>Kelola Pesanan</strong></h2>

    <!-- Statistik Status -->
    <div class="row g-3 mb-4">
        <?php
            $statusList = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan'];
            $colors = [
                'Belum Bayar' => 'secondary',
                'Dikemas'     => 'warning',
                'Dikirim'     => 'primary',
                'Selesai'     => 'success',
                'Dibatalkan'  => 'danger'
            ];
        ?>
        <?php foreach ($statusList as $status): ?>
            <div class="col-6 col-md-2">
                <div class="card shadow-sm border border-light-subtle rounded-4">
                    <div class="card-body text-center text-<?= $colors[$status] ?>">
                        <div class="fw-semibold"><?= $status ?></div>
                        <div class="display-6 fw-bold"><?= $jumlahStatus[strtolower($status)] ?? 0 ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Cetak PDF -->
    <form method="get" action="<?= base_url("$role/orders/printAll") ?>" target="_blank" class="row g-2 mb-4 p-3 bg-white rounded-4 shadow-sm border border-primary-subtle">
        <div class="col-md-3">
            <label class="form-label">📅 Dari Tanggal</label>
            <input type="date" name="from" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">📅 Sampai Tanggal</label>
            <input type="date" name="to" class="form-control" required>
        </div>
        <div class="col-md-3 align-self-end">
            <input type="hidden" name="pdf" value="yes">
            <button type="submit" class="btn btn-outline-primary w-100">🖨️ Cetak PDF</button>
        </div>
    </form>

    <!-- Flash Success -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success rounded-3 shadow-sm"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Filter Nama & Tanggal -->
    <form method="get" action="/admin/orders" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="nama" class="form-control" placeholder="🔍 Cari nama pemesan..." value="<?= esc($nama ?? '') ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control" value="<?= esc($tanggal ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex">
                <button class="btn btn-primary me-2" type="submit">🔎 Filter</button>
                <a href="/admin/orders" class="btn btn-outline-secondary">🔄 Reset</a>
            </div>
        </div>
    </form>

    <!-- Filter Status -->
    <div class="mb-4">
        <?php $statusGet = $_GET['status'] ?? ''; ?>
        <a href="/admin/orders" class="btn btn-sm me-2 <?= empty($statusGet) ? 'btn-primary' : 'btn-outline-primary' ?>">📋 Semua</a>
        <?php foreach ($statusList as $s): ?>
            <a href="<?= base_url('admin/orders?status=' . urlencode($s)) ?>" 
               class="btn btn-sm me-2 <?= ($statusGet === $s) ? 'btn-primary' : 'btn-outline-primary' ?>">
               <?= esc($s) ?>
            </a>
        <?php endforeach ?>
    </div>

    <!-- Tabel Pesanan -->
    <div class="table-responsive shadow-sm border rounded-4">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-primary text-center">
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
                        <td colspan="9" class="text-center text-muted">Belum ada pesanan.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td class="text-center"><?= esc($order['id']) ?></td>
                            <td><?= esc($order['nama']) ?></td>
                            <td><?= esc($order['no_hp']) ?></td>
                            <td><?= esc($order['alamat']) ?></td>
                            <td><?= esc($order['shipping_delivery']) ?> <br><small class="text-muted">(<?= esc($order['estimasi']) ?>)</small></td>
                            <td class="text-end"><span class="badge bg-success">Rp <?= number_format($order['total'], 0, ',', '.') ?></span></td>
                            <td class="text-center">
                                <?php
                                    $status = strtolower($order['status']);
                                    $badge = match ($status) {
                                        'belum bayar' => 'secondary',
                                        'dikemas'     => 'warning',
                                        'dikirim'     => 'primary',
                                        'selesai'     => 'success',
                                        'dibatalkan'  => 'danger',
                                        default       => 'dark'
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc(ucwords($order['status'])) ?></span>
                                <?php if (!in_array($order['status'], ['Selesai', 'Dibatalkan'])) : ?>
                                    <div class="mt-2">
                                        <a href="/admin/order/status/ubah/<?= $order['id'] ?>" class="btn btn-sm btn-outline-dark">🛠️ Ubah Status</a>
                                    </div>
                                <?php endif ?>
                            </td>
                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                            <td class="text-center">
                                <?php if (!in_array($order['status'], ['Selesai', 'Dibatalkan'])) : ?>
                                    <a href="/admin/order/resi/<?= $order['id'] ?>" class="btn btn-sm btn-outline-success mb-1">📦 Resi</a>
                                    <a href="/admin/order/detail/<?= $order['id'] ?>" class="btn btn-sm btn-outline-info">🔍 Detail</a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
