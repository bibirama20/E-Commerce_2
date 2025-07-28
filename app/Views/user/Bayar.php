<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .card-oy {
        border-radius: 1rem;
        overflow: hidden;
    }

    .oy-header {
        background: linear-gradient(to right, #4facfe, #00f2fe);
        color: white;
        padding: 1.25rem;
    }

    .oy-wallet {
        border-radius: 0.75rem;
        transition: 0.3s;
    }

    .oy-wallet:hover {
        transform: scale(1.02);
        background-color: #f8f9fa;
    }

    .oy-wallet img {
        width: 28px;
        margin-right: 10px;
    }

    .oy-countdown {
        font-weight: bold;
        color: #fff8dc;
    }
</style>

<?php
// Pastikan nilai expired_at tersedia
$expired_at = isset($order['expired_at']) ? $order['expired_at'] : date('Y-m-d H:i:s', strtotime('+15 minutes'));
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow card-oy">

                <!-- Header -->
                <div class="oy-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>OY! Indonesia</h5>
                        <small class="text-white">Bayar sebelum: <?= date('d-m-Y H:i:s', strtotime($expired_at)) ?></small>
                    </div>
                    <small class="oy-countdown" id="countdown">⏳ Memuat waktu...</small>
                </div>

                <!-- Body -->
                <div class="card-body bg-white p-4">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1 fw-semibold">BAYAR SEJUMLAH</p>
                        <h2 class="fw-bold text-primary">Rp <?= number_format($order['total'], 0, ',', '.') ?></h2>
                    </div>

                    <!-- QR Code -->
                    <div class="mb-3">
                        <div class="oy-wallet p-3 border d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">QR Code</div>
                                <small class="text-muted">Pembayaran dengan scan QR Code</small>
                            </div>
                            <i class="bi bi-chevron-right fs-5 text-secondary"></i>
                        </div>
                    </div>

                    <!-- E-wallet -->
                    <p class="fw-semibold mt-4 mb-2 text-primary">E-wallet</p>
                    <small class="text-muted">Pembayaran langsung ke aplikasi e-wallet</small>

                    <div class="mt-2">
                        <?php
                        $ewallets = [
                            ['name' => 'OVO', 'logo' => 'ovo.png'],
                            ['name' => 'ShopeePay', 'logo' => 'shopeepay.png'],
                            ['name' => 'DANA', 'logo' => 'dana.png'],
                            ['name' => 'LinkAja', 'logo' => 'linkaja.png']
                        ];
                        foreach ($ewallets as $wallet): ?>
                            <div class="oy-wallet p-3 border d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('assets/' . $wallet['logo']) ?>" alt="<?= $wallet['name'] ?>">
                                    <span><?= $wallet['name'] ?></span>
                                </div>
                                <i class="bi bi-chevron-right fs-5 text-secondary"></i>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <!-- Button -->
                    <div class="text-center mt-4">
                        <a href="<?= base_url('user/pesanan') ?>" class="btn btn-outline-secondary w-100">← Kembali ke Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Countdown Script -->
<script>
    const countdownElement = document.getElementById("countdown");
    const expiredAt = new Date("<?= $expired_at ?>").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiredAt - now;

        if (distance <= 0) {
            countdownElement.innerHTML = "❗ Link telah kedaluwarsa";
            countdownElement.classList.add('text-danger');
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownElement.innerHTML = `⏳ ${hours} jam : ${minutes} menit : ${seconds} detik`;
    }

    updateCountdown(); // awal
    setInterval(updateCountdown, 1000); // per detik
</script>
