<?= $this->include('layouts/header') ?>
<?= $this->include('layouts/sidebar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <?= $this->renderSection('content') ?>
</div>

<?= $this->include('layouts/footer')?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger mt-3"><?= session()->getFlashdata('error') ?></div>
<?php endif;?>