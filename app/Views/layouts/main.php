<?= $this->include('layouts/header') ?>

<div class="content-wrapper">
    <?= $this->include('layouts/sidebar') ?>

    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<?= $this->include('layouts/footer') ?>

<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- ✅ Toast Flash Message Only -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="flash-message alert alert-success">
    <?= session()->getFlashdata('success') ?>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="flash-message alert alert-danger">
    <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>

<style>
  .flash-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 280px;
    max-width: 400px;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    font-weight: bold;
  }
  .flash-message.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-left: 5px solid #28a745;
  }
  .flash-message.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 5px solid #dc3545;
  }
</style>

<script>
  // Auto-hide flash message after 3 seconds
  setTimeout(() => {
    const flash = document.querySelector('.flash-message');
    if (flash) {
      flash.classList.add('hide');
      setTimeout(() => flash.remove(), 500);
    }
  }, 3000);
</script>

<?= $this->renderSection('script') ?>
