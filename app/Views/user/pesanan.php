<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold text-center">
        <i class="bi bi-box-seam-fill me-2"></i> Daftar Pesanan
    </h2>
    <!-- 🔍 FORM FILTER KEYWORD -->
    <form method="get" action="<?= base_url('user/pesanan') ?>" class="mb-4">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="keyword" class="form-control shadow-sm" placeholder="🔎 Cari produk / alamat / kurir..." value="<?= esc($keyword ?? '') ?>">
            </div>
            <div class="col-md-4 d-flex">
                <button class="btn btn-primary me-2 shadow-sm" type="submit">Cari</button>
                <a href="<?= base_url('user/pesanan') ?>" class="btn btn-outline-secondary shadow-sm">Reset</a>
            </div>
        </div>
    </form>

    <!-- ✅ FILTER STATUS -->
    <div class="mb-4">
        <?php
        $statusFilters = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai', 'Dibatalkan'];
        $statusGet = $statusFilter ?? '';
        ?>
        <a href="<?= base_url('user/pesanan') ?>" class="btn btn-sm me-2 <?= empty($statusGet) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
        <?php foreach ($statusFilters as $s): ?>
            <a href="<?= base_url('user/pesanan?status=' . urlencode($s)) ?>" 
               class="btn btn-sm me-2 <?= ($statusGet === $s) ? 'btn-primary' : 'btn-outline-primary' ?>">
               <?= esc($s) ?>
            </a>
        <?php endforeach ?>
    </div>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info shadow-sm">Tidak ada data pesanan.</div>
    <?php else: ?>
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pemesan</th>
                        <th>Tanggal</th>
                        <th>Alamat</th>
                        <th>Layanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= esc($order['nama_pemesan'] ?? '-') ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= esc($order['alamat']) ?></td>
                            <td><?= esc($order['shipping_delivery']) ?> <br><small class="text-muted">(<?= esc($order['estimasi']) ?>)</small></td>
                            <td><span class="badge bg-success">Rp<?= number_format($order['total'], 0, ',', '.') ?></span></td>
                            <td class="text-center">
                                <?php
                                  $badge = match (strtolower($order['status'])) {
                                    'belum bayar' => 'secondary',
                                    'dikemas'     => 'warning',
                                    'dikirim'     => 'primary',
                                    'selesai'     => 'success',
                                    'dibatalkan'  => 'danger',
                                    default       => 'dark'
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= esc(ucwords($order['status'])) ?></span>
                            </td>
                            <td>
                                <?php if (!in_array($order['status'], ['Selesai', 'Dibatalkan'])): ?>
                                   <a href="<?= base_url('user/orders/detail/' . $order['id']) ?>" class="btn btn-sm btn-primary mb-1 w-100">🔍 Detail</a>
                                    <a href="<?= base_url('user/orders/invoicepdf/' . $order['id']) ?>" class="btn btn-sm btn-danger mb-1 w-100" target="_blank">🖨️ PDF</a>

                                    <?php if ($order['status'] === 'Dikirim'): ?>
                                        <form method="post" action="<?= base_url('user/pesanan/selesaikan/' . $order['id']) ?>" class="d-grid gap-2 mt-1">
                                            <button type="submit" class="btn btn-sm btn-success">✅ Selesaikan</button>
                                        </form>
                                    <?php endif ?>

                                    <?php if ($order['status'] === 'Belum Bayar'): ?>
                                        <a href="<?= base_url('user/pesanan/bayar/' . $order['id']) ?>" class="btn btn-sm btn-primary mt-1 w-100">💳 Bayar Sekarang</a>

                                        <?php if (empty($order['payment_changed']) || $order['payment_changed'] == 0): ?>
                                            <a href="<?= base_url('user/pesanan/gantiMetode/' . $order['id']) ?>" class="btn btn-sm btn-warning mt-1 w-100">🔄 Ganti Metode</a>
                                        <?php endif ?>
                                    <?php endif ?>
                                <?php else: ?>
                                    <a href="<?= base_url('user/orders/detail/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary w-100">🔍 Lihat</a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>

<?= $this->endSection() ?>
