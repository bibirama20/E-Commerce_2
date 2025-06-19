<h2>INVOICE</h2>
<p>Nama: <?= $order['nama'] ?><br>
Alamat: <?= $order['alamat'] ?><br>
No HP: <?= $order['no_hp'] ?><br>
Ekspedisi: <?= strtoupper($order['city']) ?><br>
Ongkir: Rp<?= number_format($order['shipping_cost']) ?><br>
Total: Rp<?= number_format($order['total']) ?></p>

<hr>
<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <tr><th>Produk</th><th>Qty</th><th>Subtotal</th></tr>
    <?php foreach ($items as $i): ?>
        <tr>
            <td><?= $i['product_id'] ?></td>
            <td><?= $i['quantity'] ?></td>
            <td>Rp<?= number_format($i['subtotal']) ?></td>
        </tr>
    <?php endforeach?>
</table>