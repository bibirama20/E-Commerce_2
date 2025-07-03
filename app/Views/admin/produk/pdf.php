<h3>Data Produk ZETANI</h3>
<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Diskon (%)</th>
            <th>Harga Setelah Diskon</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $p): ?>
        <tr>
            <td><?= $p['name'] ?></td>
            <td>Rp<?= number_format($p['price']) ?></td>
            <td><?= isset($p['diskon']) ? $p['diskon'] . '%' : '0%' ?></td>
            <td>
                Rp<?php 
                    $diskon = isset($p['diskon']) ? $p['diskon'] : 0;
                    $hargaSetelahDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                    echo number_format($hargaSetelahDiskon);
                ?>
            </td>
            <td><?= $p['stock'] ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
