<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$role = $role ?? 'user';        // Default jika $role belum didefinisikan
$items = $items ?? [];          // Default array kosong jika $items belum ada

$totalProduk = 0;
$totalBerat = 0;

if (!empty($items)) {
    foreach ($items as $item) {
        $diskon = $item['diskon'] ?? 0;
        $hargaAwal = (int)$item['price'];
        $hargaDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
        $subtotal = $hargaDiskon * (int)$item['quantity'];
        $totalProduk += $subtotal;

        $beratItem = $item['weight'] ?? 0;
        if ($beratItem < 1) $beratItem = 1000;
        $totalBerat += $beratItem * (int)$item['quantity'];
    }
}
?>

<div class="container mt-5 mb-5">
<h2 class="mb-4 text-center text-primary">
    <i class="bi bi-bag-check-fill me-2"></i> Checkout Pesanan
</h2>

    <div class="row">
        <!-- Form Pengiriman -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm p-4">
                <h5 class="mb-3">📦 Informasi Pengiriman</h5>
                <form id="checkoutForm" method="post" action="/<?= esc($role) ?>/checkout/simpan">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="kelurahan" class="form-label">Kelurahan</label>
                        <select class="form-control" name="kelurahan" id="kelurahan" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="layanan" class="form-label">Layanan Pengiriman</label>
                        <select class="form-control" name="layanan" id="layanan" required></select>
                    </div>

                    <input type="hidden" id="ongkir" name="shipping_cost">
                    <input type="hidden" id="service" name="shipping_delivery">
                    <input type="hidden" id="etd" name="estimasi_hari">
                    <input type="hidden" id="total_harga" name="total_harga">
            </div>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 bg-light">
                <h5 class="mb-3">🧾 Ringkasan Keranjang</h5>
                <?php if (!empty($items)): ?>
                <table class="table table-sm table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $diskon = $item['diskon'] ?? 0;
                                $hargaAwal = (int)$item['price'];
                                $hargaDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
                                $subtotal = $hargaDiskon * (int)$item['quantity'];
                            ?>
                            <tr>
                                <td><?= esc($item['name'] ?? '-') ?></td>
                                <td>
                                    <?php if ($diskon > 0): ?>
                                        <small><s>Rp<?= number_format($hargaAwal, 0, ',', '.') ?></s></small><br>
                                        <strong>Rp<?= number_format($hargaDiskon, 0, ',', '.') ?></strong>
                                    <?php else: ?>
                                        Rp<?= number_format($hargaAwal, 0, ',', '.') ?>
                                    <?php endif ?>
                                </td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr class="table-warning">
                            <td colspan="3"><strong>Subtotal Produk</strong></td>
                            <td><strong>Rp<?= number_format($totalProduk, 0, ',', '.') ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="alert alert-info">Keranjang Anda masih kosong.</div>
                <?php endif; ?>

                <div class="mb-3 mt-3">
                    <label><strong>Total Harga (Produk + Ongkir)</strong></label>
                    <p id="total" class="text-success"><em>Silakan pilih layanan</em></p>
                </div>
                <div class="mb-3">
                    <label><strong>Estimasi Pengiriman</strong></label>
                    <p id="estimasi" class="text-muted"><em>Belum dipilih</em></p>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url($role . '/checkout/batalkan') ?>" class="btn btn-danger mt-3">Batal Checkout</a>
                    <button type="submit" class="btn btn-primary">Lanjut Checkout</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let totalProduk = <?= $totalProduk ?>;
let totalBerat = <?= $totalBerat ?>;
let ongkir = 0;

$(document).ready(function() {
    hitungTotal();

    $('#kelurahan').select2({
        placeholder: 'Ketik nama kelurahan...',
        ajax: {
            url: '<?= base_url('get-location') ?>',
            dataType: 'json',
            delay: 1500,
            data: params => ({ search: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: item.id,
                    text: `${item.subdistrict_name}, ${item.district_name}, ${item.city_name}, ${item.province_name}, ${item.zip_code}`
                }))
            }),
            cache: true
        },
        minimumInputLength: 3,
        language: {
            inputTooShort: () => 'Ketik minimal 3 huruf...',
            searching: () => '🔄 Mencari lokasi...',
            noResults: () => '❌ Tidak ditemukan.'
        }
    });

    $("#kelurahan").on('change', function() {
        let id_kelurahan = $(this).val();
        $("#layanan").empty();
        ongkir = 0;

        $.ajax({
            url: "<?= site_url('get-cost') ?>",
            type: 'GET',
            data: {
                destination: id_kelurahan,
                weight: totalBerat
            },
            dataType: 'json',
            success: function(data) {
                $("#layanan").empty();
                data.forEach(item => {
                    let biaya = 0;
                    if (typeof item.cost === 'number') {
                        biaya = item.cost;
                    } else if (Array.isArray(item.cost) && item.cost[0]?.value) {
                        biaya = parseInt(item.cost[0].value);
                        item.etd = item.cost[0].etd || item.etd;
                        item.cost = biaya;
                    }

                    if (item.description && item.service && biaya > 0) {
                        let label = `${item.description} (${item.service}) - Estimasi ${item.etd} Hari - Rp${biaya.toLocaleString('id-ID')}`;
                        $("#layanan").append($('<option>', {
                            value: JSON.stringify(item),
                            text: label
                        }));
                    }
                });

                if ($("#layanan option").length > 0) {
                    $("#layanan").val($("#layanan option:first").val()).trigger('change');
                }

                hitungTotal();
            }
        });
    });

    $("#layanan").on('change', function() {
        let selected = JSON.parse($(this).val() || '{}');
        let biaya = 0;

        if (typeof selected.cost === 'number') {
            biaya = selected.cost;
        } else if (Array.isArray(selected.cost) && selected.cost[0]?.value) {
            biaya = parseInt(selected.cost[0].value);
            selected.etd = selected.cost[0].etd || selected.etd;
        }

        ongkir = biaya;
        $("#ongkir").val(biaya);
        $("#service").val(selected.service || '');
        $("#etd").val(selected.etd || '');
        $("#estimasi").text(`${selected.etd || '-'} Hari`);
        hitungTotal();
    });

    function hitungTotal() {
        let total = totalProduk + ongkir;
        $("#total").text("Rp " + total.toLocaleString('id-ID'));
        $("#total_harga").val(total);
    }
});
</script>
<?= $this->endSection() ?>
