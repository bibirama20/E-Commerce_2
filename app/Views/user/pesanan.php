<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4">📦 Daftar Pesanan Anda</h2>

    <!-- 🔍 FORM FILTER KEYWORD -->
    <form method="get" action="<?= base_url('user/pesanan') ?>" class="mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="Cari produk / alamat / kurir..." value="<?= esc($keyword ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex">
                <button class="btn btn-primary me-2" type="submit">🔍 Cari</button>
                <a href="<?= base_url('user/pesanan') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- ✅ FILTER STATUS -->
    <div class="mb-4">
        <?php
        $statusList = ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai'];
        $statusGet = $statusFilter ?? '';
        ?>
        <a href="<?= base_url('user/pesanan') ?>" class="btn btn-sm me-2 <?= empty($statusGet) ? 'btn-dark' : 'btn-outline-dark' ?>">Semua</a>
        <?php foreach ($statusList as $s): ?>
            <a href="<?= base_url('user/pesanan?status=' . urlencode($s)) ?>" 
               class="btn btn-sm me-2 <?= ($statusGet === $s) ? 'btn-dark' : 'btn-outline-dark' ?>">
               <?= esc($s) ?>
            </a>
        <?php endforeach ?>
    </div>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Tidak ada data pesanan.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
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
                        <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= esc($order['alamat']) ?></td>
                        <td><?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</td>
                        <td><span class="badge bg-success">Rp<?= number_format($order['total'], 0, ',', '.') ?></span></td>
                        <td>
                            <?php
                                $status = strtolower($order['status'] ?? 'belum bayar');
                                switch ($status) {
                                    case 'belum bayar': $badge = 'secondary'; break;
                                    case 'dikemas':     $badge = 'warning'; break;
                                    case 'dikirim':     $badge = 'primary'; break;
                                    case 'selesai':     $badge = 'success'; break;
                                    default:            $badge = 'dark';
                                }
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= ucwords($status) ?></span>
                        </td>
                        <td>
                            <a href="<?= base_url('user/orders/detail/' . $order['id']) ?>" class="btn btn-sm btn-info mb-1">🔍 Detail</a>
                            <a href="<?= base_url('user/orders/invoicepdf/' . $order['id']) ?>" class="btn btn-sm btn-danger mb-1" target="_blank">🖨️ PDF</a>
                            <?php if ($order['status'] === 'Dikirim'): ?>
                                <form method="post" action="<?= base_url('user/pesanan/selesaikan/' . $order['id']) ?>" style="display:inline;">
                                    <button type="submit" class="btn btn-sm btn-success mt-1">✅ Selesaikan</button>
                                </form>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>

<?= $this->endSection() ?>
