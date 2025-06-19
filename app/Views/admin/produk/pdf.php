<h3>Data Produk</h3>
<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th> <!-- Tambahan kolom gambar -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $p): ?>
        <tr>
            <td><?= $p['name'] ?></td>
            <td>Rp<?= number_format($p['price']) ?></td>
            <td><?= $p['stock'] ?></td>
            <td>
                <img src="<?= base_url('uploads/' . $p['image']) ?>" width="80">
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
