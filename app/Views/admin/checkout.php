<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Tambahkan Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<h2>Checkout</h2>

<div class="row">
    <div class="col-md-6">
        <form action="/<?= $role ?>/checkout/simpan" method="post" class="row g-3">
            <input type="hidden" name="total_harga" id="total_harga" value="">

            <div class="col-12">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" required value="<?= session()->get('username') ?>">
            </div>

            <div class="col-12">
                <label for="alamat" class="form-label">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" required></textarea>
            </div>

            <div class="col-12">
                <label for="no_hp" class="form-label">No. HP / WhatsApp</label>
                <input type="text" name="no_hp" class="form-control" required>
            </div>

            <div class="col-12">
                <label for="kelurahan" class="form-label">Kelurahan / Tujuan</label>
                <select name="kelurahan" id="kelurahan" class="form-control" required></select>
            </div>

            <div class="col-12">
                <label for="ekspedisi" class="form-label">Layanan Pengiriman</label>
                <select name="ekspedisi" id="ekspedisi" class="form-control" required></select>
            </div>

            <div class="col-12">
                <label for="ongkir" class="form-label">Ongkir</label>
                <input type="text" id="ongkir" class="form-control" name="ongkir" readonly>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success">Simpan & Cetak Invoice</button>
            </div>
        </form>
    </div>

    <div class="col-md-6">
        <h5>Ringkasan Keranjang</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($items as $item): ?>
                    <tr>
                        <td><?= esc($item['name']) ?></td>
                        <td>Rp<?= number_format($item['price']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp<?= number_format($item['subtotal']) ?></td>
                    </tr>
                    <?php $total += $item['subtotal']; ?>
                <?php endforeach ?>
                <tr>
                    <td colspan="3">Subtotal</td>
                    <td>Rp<?= number_format($total) ?></td>
                </tr>
                <tr>
                    <td colspan="3">Total</td>
                    <td><span id="total_text">Rp<?= number_format($total) ?></span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(function(){
    let total = <?= $total ?>;
    let ongkir = 0;

    // Dropdown kelurahan (subdistrict)
    $('#kelurahan').select2({
        placeholder: 'Ketik nama kelurahan...',
        ajax: {
            url: '<?= base_url('get-location') ?>',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.subdistrict_id,
                            text: item.subdistrict_name + ', ' + item.district_name + ', ' + item.city_name
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    // Ketika kelurahan dipilih, ambil layanan ongkir
    $('#kelurahan').on('change', function () {
        const dest = $(this).val();
        $('#ekspedisi').html('<option selected>Loading...</option>');

        $.getJSON('<?= base_url('get-cost') ?>', { destination: dest }, function (data) {
            $('#ekspedisi').empty();
            data.forEach(function (item) {
                $('#ekspedisi').append(new Option(
                    `${item.description} - ${item.service} (ETD: ${item.etd} hari)`,
                    item.cost
                ));
            });
        });
    });

    // Ketika layanan dipilih, hitung ongkir & total
    $('#ekspedisi').on('change', function () {
        ongkir = parseInt($(this).val()) || 0;
        $('#ongkir').val('Rp' + ongkir.toLocaleString('id-ID'));
        $('#total_text').text('Rp' + (total + ongkir).toLocaleString('id-ID'));
        $('#total_harga').val(total + ongkir);
    });
});
</script>
<?= $this->endSection() ?>
