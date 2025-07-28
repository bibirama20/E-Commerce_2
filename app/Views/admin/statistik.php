<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
     <h2 class="mb-4 text-center text-primary fw-bold">
    <i class="bi bi-bar-chart-fill me-2"></i> Statistik Penjualan
    </h2>
    <hr class="border-2 border-primary opacity-50 mb-4" />

    <div class="row">
        <!-- Total Pendapatan -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Pendapatan</div>
                <div class="card-body">
                    <h5 class="card-title">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>

        <!-- Jumlah Pesanan -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Jumlah Pesanan</div>
                <div class="card-body">
                    <h5 class="card-title"><?= $jumlahPesanan ?></h5>
                </div>
            </div>
        </div>

        <!-- Rata-rata Transaksi -->
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Rata-rata Transaksi</div>
                <div class="card-body">
                    <h5 class="card-title">Rp <?= number_format($rataRataTransaksi, 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>
    </div>

    <hr>

   <h2 class="text-primary text-center fw-bold">
    <i class="bi bi-calendar-week-fill me-2"></i> Grafik Penjualan Bulanan
    </h2>
    <hr class="border-2 border-primary opacity-50 mb-4" />

    <canvas id="chartPenjualan"></canvas>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartPenjualan');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($bulan) ?>,
        datasets: [{
            label: 'Total Penjualan',
            data: <?= json_encode($totalPerBulan) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
