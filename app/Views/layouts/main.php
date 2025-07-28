<?= $this->include('layouts/header') ?>

<div class="d-flex">
    <?= $this->include('layouts/sidebar') ?>

    <div class="main-content flex-grow-1 p-4" style="background-color: #f4faff;">
        <!-- ✅ Notifikasi Flash Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="custom-toast toast-success shadow-sm" id="flashSuccess">
                <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="custom-toast toast-error shadow-sm" id="flashError">
                <i class="bi bi-x-circle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Konten halaman -->
        <?= $this->renderSection('content') ?>
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<!-- ✅ Select2 + jQuery -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ✅ Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- ✅ Flash Toast Styling -->
<style>
    .custom-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        padding: 12px 18px;
        border-radius: 10px;
        font-weight: 500;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease-in-out;
    }

    .toast-success {
        background-color: #e6ffef;
        color: #146c43;
        border-left: 6px solid #198754;
    }

    .toast-error {
        background-color: #fdecea;
        color: #842029;
        border-left: 6px solid #dc3545;
    }

    .custom-toast.fade-out {
        opacity: 0;
        transform: translateY(-10px);
        pointer-events: none;
    }
</style>

<!-- ✅ Flash Auto Dismiss -->
<script>
    setTimeout(() => {
        $('.custom-toast').addClass('fade-out');
    }, 3500);
</script>

<?= $this->renderSection('script') ?>
