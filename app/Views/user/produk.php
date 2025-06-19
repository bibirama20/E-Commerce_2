<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Belanja Produk</h3>
<?php foreach ($products as $p): ?>
    <div>
        <strong><?= esc($p['name']) ?></strong><br>
        Rp<?= number_format($p['price']) ?><br>
        <a href="/user/keranjang/tambah/<?= $p['id'] ?>" class="btn btn-sm btn-success">Tambah</a>
    </div><hr>
<?php endforeach ?>
