<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Keranjang Belanja</h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="alert alert-info">Keranjang Anda kosong.</div>
<?php else: ?>

<form method="post" action="/<?= esc($role) ?>/keranjang/update">
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Diskon</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                    $diskon = $item['diskon'] ?? 0;
                    $hargaAwal = $item['price'];
                    $hargaSetelahDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
                    $subtotal = $hargaSetelahDiskon * $item['quantity'];
                ?>
                <tr>
                    <td><?= esc($item['name']) ?></td>
                    <td>Rp<?= number_format($hargaAwal, 0, ',', '.') ?></td>
                    <td><?= $diskon ?>%</td>
                    <td>
                        <input type="number" name="quantities[<?= esc($item['id']) ?>]" value="<?= esc($item['quantity']) ?>" min="1" class="form-control" style="width: 80px;">
                    </td>
                    <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                    <td>
                        <a href="/<?= esc($role) ?>/keranjang/hapus/<?= esc($item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus item ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <?php
    $totalSetelahDiskon = 0;
    foreach ($items as $item) {
        $diskon = $item['diskon'] ?? 0;
        $hargaAwal = $item['price'];
        $hargaSetelahDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
        $subtotal = $hargaSetelahDiskon * $item['quantity'];
        $totalSetelahDiskon += $subtotal;
    }
?>
<p><strong>Total: Rp<?= number_format($totalSetelahDiskon, 0, ',', '.') ?></strong></p>


    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-warning">Update Jumlah</button>
        <a href="/<?= esc($role) ?>/checkout" class="btn btn-success">Lanjut ke Checkout</a>
    </div>
</form>

<?php endif; ?>

<?= $this->endSection() ?>
