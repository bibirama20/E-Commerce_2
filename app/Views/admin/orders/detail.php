<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4" id="printArea">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-0">🧾 Detail Pesanan #<?= esc($order['id']) ?></h4>
            <small class="text-muted">📅 <?= date('d-m-Y H:i', strtotime($order['created_at'] ?? now())) ?></small>
        </div>
        <button onclick="window.print()" class="btn btn-primary d-print-none shadow-sm rounded-pill px-4">
            <i class="bi bi-printer"></i> Cetak PDF
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="border rounded-3 shadow-sm p-4 bg-white">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-person-circle"></i> Informasi Pemesan</h6>
                <ul class="list-unstyled mb-0">
                    <li><strong>👤 Nama:</strong> <?= esc($order['nama'] ?? '-') ?></li>
                    <li><strong>📱 No HP:</strong> <?= esc($order['no_hp'] ?? '-') ?></li>
                    <li><strong>🏠 Alamat:</strong> <?= esc($order['alamat'] ?? '-') ?></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded-3 shadow-sm p-4 bg-white">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-truck"></i> Pengiriman</h6>
                <ul class="list-unstyled mb-0">
                    <li><strong>🚛 Kurir:</strong> <?= esc($order['shipping_delivery'] ?? '-') ?></li>
                    <li><strong>⏱️ Estimasi:</strong> <?= esc($order['estimasi'] ?? '-') ?></li>
                    <li>
                        <strong>💰 Total:</strong> 
                        <span class="badge bg-success fs-6 ms-1">
                            Rp<?= number_format($order['total'], 0, ',', '.') ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <h5 class="fw-semibold text-primary border-bottom pb-2 mb-3"><i class="bi bi-bag-check-fill"></i> Produk Dipesan</h5>
    <div class="table-responsive">
        <table class="table table-bordered align-middle shadow-sm rounded">
            <thead class="table-primary text-center">
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
                        <td class="text-center"><?= esc($item['quantity']) ?></td>
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
        background-color: #f8f9fa;
    }
    .table thead th {
        vertical-align: middle;
    }
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
        aside, nav, .sidebar, .navbar, .d-print-none {
            display: none !important;
        }
    }
</style>

<?= $this->endSection() ?>
