<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h2>Checkout</h2>

<form method="post" action="/<?= $role ?>/checkout/simpan">
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label>No HP</label>
        <input type="text" name="no_hp" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Jenis Pengiriman</label>
        <select name="ekspedisi" class="form-control" required>
            <option value="jne">JNE</option>
            <option value="pos">POS Indonesia</option>
            <option value="tiki">TIKI</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">🖨️ Simpan & Cetak Invoice</button>
</form>

<?= $this->endSection()?>