<?= $this->include('layouts/header') ?>

<div class="content-wrapper">
    <?= $this->include('layouts/sidebar') ?>

    <div class="main-content">
        <!-- ✅ Notifikasi Flash (Satu posisi, otomatis hilang, dan beda tampilan) -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="custom-toast success" id="flashSuccess">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="custom-toast error" id="flashError">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<!-- Select2 + jQuery -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ✅ Auto-dismiss Flash Toast -->
<style>
    .custom-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        padding: 15px 20px;
        border-radius: 10px;
        font-weight: 500;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        opacity: 1;
        transition: opacity 0.5s ease-in-out;
    }

    .custom-toast.success {
        background: #e1f7e6;
        color: #176c39;
        border-left: 6px solid #28a745;
    }

    .custom-toast.error {
        background: #ffe6e6;
        color: #a02f2f;
        border-left: 6px solid #dc3545;
    }

    .custom-toast.fade-out {
        opacity: 0;
        pointer-events: none;
    }
</style>

<script>
    setTimeout(() => {
        $('.custom-toast').addClass('fade-out');
    }, 3500); // hilang setelah 3.5 detik
</script>

<?= $this->renderSection('script') ?>
