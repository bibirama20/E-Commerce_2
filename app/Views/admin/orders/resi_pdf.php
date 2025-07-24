<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resi Pengiriman</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            padding: 20px;
            background-color: #f8f8f8;
            color: #333;
        }

        .resi-container {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            color: #139043ff;
        }

        .section {
            margin-bottom: 25px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            margin-bottom: 8px;
        }

        .produk {
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .produk-item {
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #888;
        }

        .print-btn-container {
            text-align: center;
            margin-top: 30px;
        }

        .print-btn {
            padding: 10px 20px;
            background-color: #139043ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .print-btn:hover {
            background-color: #0f7a36;
        }

        /* 🖨️ Tambahan agar sidebar/header/footer tidak ikut tercetak */
        @media print {
            .print-btn-container {
                display: none !important;
            }

            body {
                margin: 15mm 10mm;
                background-color: #fff !important;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="resi-container" id="resi">
        <div class="header">
            <h2>Resi Pengiriman</h2>
        </div>

        <div class="section">
            <div class="label">Pengirim:</div>
            <div class="value">Zetani, 089767896789, Kota Semarang</div>
        </div>

        <div class="section">
            <div class="label">Penerima:</div>
            <div class="value"><?= esc($order['nama']) ?> (<?= esc($order['no_hp']) ?>)</div>
            <div class="value"><?= esc($order['alamat']) ?></div>
        </div>

        <div class="section">
            <div class="label">Layanan Pengiriman:</div>
            <div class="value"><?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</div>
        </div>

        <div class="section">
            <div class="label">Ongkir:</div>
            <div class="value">Rp<?= number_format($order['shipping_cost'], 0, ',', '.') ?></div>

            <div class="label">Total Pembayaran:</div>
            <div class="value">Rp<?= number_format($order['total'], 0, ',', '.') ?></div>
        </div>

        <div class="section produk">
            <div class="label">Produk:</div>
            <?php foreach ($items as $item): ?>
                <div class="produk-item">
                    <?= esc($item['name']) ?> - <?= $item['quantity'] ?> pcs<br>
                    Subtotal: Rp<?= number_format($item['subtotal'], 0, ',', '.') ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer">
            Terima kasih telah berbelanja di Zetani!
        </div>
    </div>

    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Resi</button>
    </div>

</body>
</html>
