<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Terjual</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

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

</body>
</html>
