<h4>Ganti Metode Pembayaran</h4>

<form method="post" action="<?= base_url('user/pesanan/updateMetode/' . $order['id']) ?>">
    <label>Pilih Metode:</label>
    <select name="payment_method" class="form-control" required>
        <option value="bank_transfer">Bank Transfer</option>
        <option value="qris">QRIS</option>
    </select>
    <button type="submit" class="btn btn-primary mt-2">Simpan</button>
</form>
