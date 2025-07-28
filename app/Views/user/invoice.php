<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            🧾 Detail Pesanan #<?= esc($order['id']) ?>
        </h3>
        <span class="text-muted small">
            <?= date('d M Y, H:i', strtotime($order['created_at'] ?? now())) ?>
        </span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="border rounded shadow-sm p-3 bg-white">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-person-fill"></i> Informasi Pemesan</h6>
                <p class="mb-2"><strong>👤 Nama:</strong> <?= esc($order['nama']) ?></p>
                <p class="mb-2"><strong>📱 No HP:</strong> <?= esc($order['no_hp']) ?></p>
                <p class="mb-0"><strong>🏠 Alamat:</strong> <?= esc($order['alamat']) ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded shadow-sm p-3 bg-white">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-truck"></i> Pengiriman & Total</h6>
                <p class="mb-2"><strong>🚚 Layanan:</strong> <?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</p>
                <p class="mb-2"><strong>💸 Ongkir:</strong> Rp<?= number_format($order['shipping_cost'], 0, ',', '.') ?></p>
                <p class="mb-0"><strong>🧮 Total:</strong>
                    <span class="badge bg-success fs-6 ms-2">
                        Rp<?= number_format($order['total'], 0, ',', '.') ?>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-bag-check"></i> Produk yang Dipesan</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle shadow-sm bg-white">
            <thead class="table-primary text-center">
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="text-start"><?= esc($item['product_name']) ?></td>
                        <td><?= esc($item['quantity']) ?></td>
                        <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fc;
    }

    table th, table td {
        vertical-align: middle !important;
    }

    .table thead th {
        background-color: #cfe2ff;
    }
</style>

<?= $this->endSection() ?>
