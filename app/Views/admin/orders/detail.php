<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4" id="printArea">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Detail Pesanan #<?= esc($order['id']) ?></h3>
        <button onclick="window.print()" class="btn btn-outline-primary d-print-none">🖨️ Cetak</button>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <p><strong>Nama Pemesan:</strong> <?= esc($order['nama'] ?? '-') ?></p>
            <p><strong>No HP:</strong> <?= esc($order['no_hp'] ?? '-') ?></p>
            <p><strong>Alamat:</strong> <?= esc($order['alamat'] ?? '-') ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Kurir:</strong> <?= esc($order['shipping_delivery'] ?? '-') ?> | 
               <strong>Estimasi:</strong> <?= esc($order['estimasi'] ?? '-') ?></p>
            <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($order['created_at'] ?? now())) ?></p>
            <p><strong>Total:</strong> <span class="badge bg-success">Rp<?= number_format($order['total'], 0, ',', '.') ?></span></p>
        </div>
    </div>

    <h5>Produk Dipesan:</h5>
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-success">
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
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

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* Sembunyikan sidebar dan header */
    aside, nav, .sidebar, .navbar, .d-print-none {
        display: none !important;
    }
}
</style>

<?= $this->endSection() ?>
