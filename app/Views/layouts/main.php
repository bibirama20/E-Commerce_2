<?= $this->include('layouts/header') ?>
<div class="content-wrapper">
    <?= $this->include('layouts/sidebar') ?>

    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>
</div>
<?= $this->include('layouts/footer') ?>

<!-- Tambahkan ini agar script checkout dijalankan -->
<?= $this->renderSection('script') ?>
