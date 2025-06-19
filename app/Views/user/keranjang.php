<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Keranjang</h3>
<table class="table">
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['quantity'] ?> x Rp<?= number_format($item['price']) ?></td>
            <td>Rp<?= number_format($item['subtotal']) ?></td>
        </tr>
    <?php endforeach ?>
</table>
<p><strong>Total: Rp<?= number_format($total) ?></strong></p>

<form action="/user/checkout/simpan" method="post">
    <label>Kota Pengiriman:</label>
    <select name="city" class="form-control">
        <?php foreach ($cities as $city => $harga): ?>
            <option value="<?= $city ?>"><?= $city ?> - Rp<?= number_format($harga) ?></option>
        <?php endforeach ?>
    </select><br>
    <button class="btn btn-primary">Checkout & Cetak Invoice</button>
</form>
<?= $this->endSection()?>