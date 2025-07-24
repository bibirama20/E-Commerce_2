<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $order['id'] ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 40px;
            color: #333;
        }

        .invoice-container {
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 8px;
            max-width: 700px;
            margin: 0 auto;
        }

        h2, h3 {
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #28a745;
            margin-bottom: 20px;
        }

        .header h2 {
            color: #28a745;
        }

        .info p {
            margin: 4px 0;
        }

        .label-status {
            display: inline-block;
            background-color: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            margin-left: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="header">
        <h2>ZETANI</h2>
        <h3>Invoice #<?= $order['id'] ?></h3>
        <small><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></small>
    </div>

    <div class="info">
        <p><strong>Nama Pemesan:</strong> <?= esc($order['nama']) ?></p>
        <p><strong>Alamat:</strong> <?= esc($order['alamat']) ?></p>
        <p><strong>Status:</strong> 
            <span class="label-status"><?= esc($order['status']) ?></span>
        </p>
        <p><strong>Pengiriman:</strong> <?= esc($order['shipping_delivery']) ?> (<?= esc($order['estimasi']) ?>)</p>
    </div>

    <table>
        <thead>
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
                    <td><?= $item['quantity'] ?></td>
                    <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td><strong>Rp<?= number_format($order['total'], 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di Zetani<br>
        Sukses Selalu
    </div>
</div>
</body>
</html>
