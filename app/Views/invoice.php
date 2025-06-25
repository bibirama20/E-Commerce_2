<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <tr>
        <th>Produk</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    <?php foreach ($items as $i): ?>
<tr>
    <td>
        <?= esc($i['name']) ?><br>
        <?php if ($i['image']): ?>
            <img src="<?= base_url('uploads/' . $i['image']) ?>" width="80">
        <?php endif; ?>
    </td>
    <td><?= $i['quantity'] ?></td>
    <td>Rp<?= number_format($i['subtotal']) ?></td>
</tr>
<?php endforeach ?>

</table>
