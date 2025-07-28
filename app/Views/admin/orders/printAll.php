<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Terjual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #007bff;
        }
        .header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .tanggal-cetak {
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 16px;
            color: #141414ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
        }
            .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Toko Zetani</h1>
        <p>Jl. Nasional, Semarang</p>
        <p>Telp: (000) 00000</p>
    </div>

    <div class="tanggal-cetak">
        Dicetak: <?= date('d-m-Y H:i') ?>
    </div>

    <h2>LAPORAN BARANG TERJUAL</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemesan</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($orders as $order): 
                if (!empty($items[$order['id']])) :
                    foreach ($items[$order['id']] as $item):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($order['nama']) ?></td>
                    <td><?= esc($order['no_hp']) ?></td>
                    <td><?= esc($order['alamat']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                    <td><?= esc($item['product_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                </tr>
            <?php 
                    endforeach;
                endif;
            endforeach; 
            ?>
        </tbody>
    </table>
     <div class="footer">
        Laporan ini dihasilkan secara otomatis.
    </div>

</body>
</html>
