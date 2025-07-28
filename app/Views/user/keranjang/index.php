<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="text-center">
    <h2 class="mb-4 text-primary">
        <i class="bi bi-cart-fill me-2"></i> Keranjang Belanja
    </h2>
</div>


<?php if (empty($items)): ?>
    <div class="alert alert-info">Keranjang Anda kosong. Yuk belanja dulu! 🛍️</div>
<?php else: ?>

<form method="post" action="/<?= esc($role) ?>/checkout/pilih" id="cartForm">
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="checkAll">
        <label class="form-check-label fw-bold" for="checkAll">Pilih Semua</label>
    </div>

    <div class="row g-3">
        <?php foreach ($items as $item): ?>
            <?php
                $diskon = $item['diskon'] ?? 0;
                $hargaAwal = $item['price'];
                $hargaSetelahDiskon = $hargaAwal - ($hargaAwal * $diskon / 100);
            ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body d-flex align-items-center flex-wrap">
                        <input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>" class="form-check-input me-3 item-check">

                        <img src="<?= base_url('uploads/' . $item['image']) ?>" alt="<?= esc($item['name']) ?>"
                             class="img-thumbnail me-3" style="width: 80px; height: 80px; object-fit: cover;">

                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                            <small class="text-muted">Harga: Rp<?= number_format($hargaAwal, 0, ',', '.') ?> | Diskon: <?= $diskon ?>%</small>
                            <div class="d-flex align-items-center mt-2 gap-2">
                                <input type="number" name="quantities[<?= esc($item['id']) ?>]"
                                       value="<?= esc($item['quantity']) ?>" min="1"
                                       class="form-control form-control-sm text-center quantity-input"
                                       data-harga="<?= $hargaSetelahDiskon ?>" style="width: 80px;">
                                <span class="fw-semibold subtotal-text">Subtotal: Rp<?= number_format($hargaSetelahDiskon * $item['quantity'], 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <div class="ms-auto mt-3 mt-md-0">
                            <a href="/<?= esc($role) ?>/keranjang/hapus/<?= esc($item['id']) ?>"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Yakin hapus item ini?')">
                                <i class="bi bi-trash-fill"> Hapus</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Tombol dan total -->
    <div class="card mt-4 shadow-sm">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
            <h5 class="mb-2 mb-md-0">Total: <span id="totalHarga" class="text-success fw-bold">Rp0</span></h5>
            <button type="submit" class="btn btn-primary" id="checkoutBtn" style="pointer-events: none; opacity: 0.5;">
                <i class="bi bi-credit-card-fill"></i> Checkout
            </button>
        </div>
    </div>
</form>

<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const itemChecks = document.querySelectorAll('.item-check');
        const totalHarga = document.getElementById('totalHarga');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const quantityInputs = document.querySelectorAll('.quantity-input');

        function formatRupiah(angka) {
            return 'Rp' + angka.toLocaleString('id-ID');
        }

        function hitungTotal() {
            let total = 0;
            let aktif = false;

            itemChecks.forEach((check) => {
                const card = check.closest('.card-body');
                const subtotalSpan = card.querySelector('.subtotal-text');
                const qtyInput = card.querySelector('.quantity-input');
                const harga = parseInt(qtyInput.dataset.harga);
                const qty = parseInt(qtyInput.value);
                const subtotal = harga * qty;

                subtotalSpan.innerText = `Subtotal: ${formatRupiah(subtotal)}`;

                if (check.checked) {
                    total += subtotal;
                    aktif = true;
                }
            });

            totalHarga.innerText = formatRupiah(total);
            checkoutBtn.style.pointerEvents = aktif ? 'auto' : 'none';
            checkoutBtn.style.opacity = aktif ? '1' : '0.5';
        }

        checkAll.addEventListener('change', function () {
            itemChecks.forEach(c => c.checked = checkAll.checked);
            hitungTotal();
        });

        itemChecks.forEach(check => {
            check.addEventListener('change', () => {
                if (!check.checked) checkAll.checked = false;
                hitungTotal();
            });
        });

        quantityInputs.forEach(input => {
            input.addEventListener('input', () => {
                if (parseInt(input.value) < 1) input.value = 1;
                hitungTotal();
            });
        });

        hitungTotal();
    });
</script>

<?= $this->endSection() ?>
