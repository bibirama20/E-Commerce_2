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
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="destination" class="form-label">Kota Tujuan</label>
                    <select name="destination" id="destination" class="form-select" required></select>
                </div>

                <div class="mb-3">
                    <label for="layanan" class="form-label">Pilih Layanan</label>
                    <select id="layanan" name="layanan" class="form-select" required>
                        <option disabled selected value="">Pilih kota dulu...</option>
                    </select>
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
                            <td>Rp<?= number_format($hargaDiskon) ?></td>
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

            <!-- Pindahan: Total, Estimasi, Tombol -->
            <div class="mb-3">
                <label><strong>Total Harga</strong></label>
                <p id="total"><em>Silakan pilih layanan untuk menghitung total</em></p>
            </div>
            <div class="mb-3">
                <label><strong>Estimasi Pengiriman</strong></label>
                <p id="estimasi"><em>Belum dipilih</em></p>
            </div>
            <div class="text-end d-flex justify-content-between mt-4">
                <a href="<?= base_url(session()->get('role') . '/batalCheckout') ?>" class="btn btn-danger">
                    Batalkan Checkout
                </a>
                <button type="submit" class="btn btn-success">Lanjutkan Checkout</button>
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

function hitungTotal() {
    let total = ongkir + totalProduk;
    $('#total').html("IDR " + total.toLocaleString('id-ID'));
    $('#total_harga').val(total);
}

$(document).ready(function () {
    $('#destination').select2({
        placeholder: 'Ketik nama kota...',
        minimumInputLength: 3,
        delay: 500,
        ajax: {
            url: BASE_URL + 'get-location',
            dataType: 'json',
            data: params => ({ search: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true
        },
        language: {
            inputTooShort: () => 'Ketik minimal 3 huruf...',
            searching: () => '🔄 Mencari kota...',
            noResults: () => '❌ Kota tidak ditemukan.'
        }
    });

    $('#destination').on('change', function () {
        let destination = $(this).val();
        $('#layanan').empty().append('<option disabled selected value="">Memuat layanan...</option>');

        $.each(['jne', 'pos', 'tiki'], function (_, courier) {
            $.get(BASE_URL + 'get-cost', { destination, courier }, function (res) {
                if (res.success) {
                    res.data.forEach(function (layanan) {
                        let optionText = `${layanan.courier.toUpperCase()} - ${layanan.service} | Estimasi ${layanan.etd} Hari | Rp${layanan.cost.toLocaleString('id-ID')}`;
                        let optionValue = JSON.stringify({
                            courier: layanan.courier,
                            service: layanan.service,
                            etd: layanan.etd,
                            cost: layanan.cost
                        });
                        $('#layanan').append(`<option value='${optionValue}'>${optionText}</option>`);
                    });
                }
            });
        });
    });

    $('#layanan').on('change', function () {
        let selected = JSON.parse($(this).val());
        ongkir = selected.cost;
        $('#ongkir').val(selected.cost);
        $('#service').val(`${selected.courier.toUpperCase()} - ${selected.service}`);
        $('#etd').val(selected.etd);
        $('#estimasi').text(`${selected.etd} Hari`);
        hitungTotal();
    });
});
</script>
<?= $this->endSection() ?>
