<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4">Checkout</h2>
    <div class="row">
        <!-- FORM CHECKOUT -->
        <div class="col-md-6">
            <form id="checkoutForm" method="post" action="/<?= $role ?>/checkout/simpan">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No HP</label>
                    <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                </div>
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat">
                </div> 
                <div class="col-12">
                    <label for="kelurahan" class="form-label">Kelurahan</label>
                    <select class="form-control" name="kelurahan" id="kelurahan" required></select>
                </div>
                <div class="col-12">
                    <label for="layanan" class="form-label">Layanan</label>
                    <select class="form-control" name="layanan" id="layanan" required></select>
                </div>

                <!-- HIDDEN -->
                <input type="hidden" id="ongkir" name="shipping_cost">
                <input type="hidden" id="service" name="shipping_delivery">
                <input type="hidden" id="etd" name="estimasi_hari">
                <input type="hidden" id="total_harga" name="total_harga">
        </div>

        <!-- RINGKASAN KERANJANG -->
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
                    <?php $totalProduk = 0; foreach ($items as $item): ?>
                        <?php
                            $diskon = $item['diskon'] ?? 0;
                            $hargaAwal = $item['price'];
                            $hargaDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
                            $subtotal = $hargaDiskon * $item['quantity'];
                            $totalProduk += $subtotal;
                        ?>
                        <tr>
                            <td><?= esc($item['name']) ?></td>
                            <td>
                                <?php if ($diskon > 0): ?>
                                    <small><s>Rp<?= number_format($hargaAwal) ?></s></small><br>
                                    Rp<?= number_format($hargaDiskon) ?>
                                <?php else: ?>
                                    Rp<?= number_format($hargaAwal) ?>
                                <?php endif ?>
                            </td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp<?= number_format($subtotal) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <tr>
                        <td colspan="3"><strong>Subtotal</strong></td>
                        <td><strong>Rp<?= number_format($totalProduk) ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-3">
                <label><strong>Total Harga "Harga Produk + Ongkir"</strong></label>
                <p id="total"><em>Silakan pilih layanan untuk menghitung total</em></p>
            </div>
            <div class="mb-3">
                <label><strong>Estimasi Pengiriman</strong></label>
                <p id="estimasi"><em>Belum dipilih</em></p>
            </div>

            <div class="text-end d-flex justify-content-between mt-4">
                <a href="<?= base_url(session()->get('role') . '/produk') ?>" class="btn btn-danger">
                    Batalkan Checkout
                </a>
                 <a href="<?= base_url(session()->get('role') . '/produk') ?>" class="btn btn-success">
                    Batalkan Checkout
                </a>
            </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!-- Select2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
const BASE_URL = '<?= base_url() ?>';
let ongkir = 0;
let totalProduk = <?= $totalProduk ?? 0 ?>;

$(document).ready(function() {
    var ongkir = 0;
    var total = 0;

    hitungTotal();

    $('#kelurahan').select2({
        placeholder: 'Ketik nama kelurahan...',
        ajax: {
            url: '<?= base_url('get-location') ?>',
            dataType: 'json',
            delay: 1500,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.subdistrict_name + ", " + item.district_name + ", " + item.city_name + ", " + item.province_name + ", " + item.zip_code
                        };
                    })
                };
            },
            cache: true
        },
        language: {
            inputTooShort: () => 'Ketik minimal 3 huruf...',
            searching: () => '🔄 Mencari lokasi...',
            noResults: () => '❌ Tidak ditemukan.'
        },
        minimumInputLength: 3
    });

    $("#kelurahan").on('change', function() {
        var id_kelurahan = $(this).val();
        $("#layanan").empty();
        ongkir = 0;

        $.ajax({
            url: "<?= site_url('get-cost') ?>",
            type: 'GET',
            data: {
                'destination': id_kelurahan,
            },
            dataType: 'json',
            success: function(data) {
                data.forEach(function(item) {
                    var label = item["description"] + " (" + item["service"] + ") - Estimasi " + item["etd"] + " Hari - Rp" + parseInt(item["cost"]).toLocaleString('id-ID');
                    $("#layanan").append($('<option>', {
                        value: JSON.stringify(item),
                        text: label
                    }));
                });
                hitungTotal();
            },
        });
    });

    $("#layanan").on('change', function() {
        let selected = JSON.parse($(this).val());
        ongkir = parseInt(selected.cost);
        $("#ongkir").val(selected.cost);
        $("#service").val(selected.service);
        $("#etd").val(selected.etd);
        $("#estimasi").text(selected.etd + " Hari");
        hitungTotal();
    });

    function hitungTotal() {
        total = ongkir + totalProduk;

        $("#total").html("Rp " + total.toLocaleString('id-ID'));
        $("#total_harga").val(total);
    }
});
</script>
<?= $this->endSection() ?>
