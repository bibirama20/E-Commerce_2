<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h3>🧾 Detail Pesanan #<?= esc($order['id']) ?></h3>

    <div class="row mt-3 mb-4">
        <div class="col-md-6">
            <p><strong>Nama:</strong> <?= esc($order['nama']) ?></p>
            <p><strong>No HP:</strong> <?= esc($order['no_hp']) ?></p>
            <p><strong>Alamat:</strong> <?= esc($order['alamat']) ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Layanan:</strong> <?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</p>
            <p><strong>Ongkir:</strong> Rp<?= number_format($order['shipping_cost'], 0, ',', '.') ?></p>
            <p><strong>Total:</strong> <span class="badge bg-success">Rp<?= number_format($order['total'], 0, ',', '.') ?></span></p>
        </div>
    </div>

    <h5>🛒 Produk yang Dipesan:</h5>
    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['product_name']) ?></td>
                    <td><?= esc($item['quantity']) ?></td>
                    <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
