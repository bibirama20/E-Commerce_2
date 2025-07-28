<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah User / Admin
        </h2>
        <p class="text-muted">Silakan isi form berikut untuk menambahkan user atau admin baru dengan informasi yang lebih lengkap dan aman.</p>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show col-md-6 mx-auto" role="alert">
            <strong><i class="bi bi-exclamation-circle-fill me-2"></i>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="post" action="/admin/user/store" class="col-md-6 mx-auto bg-light p-4 rounded shadow-sm">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="<?= old('username') ?>" placeholder="Masukkan username" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" placeholder="Masukkan email aktif" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" minlength="6" required>
        </div>

        <div class="mb-4">
            <label for="role" class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select" required>
                <option value="">Pilih Role</option>
                <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= old('role') == 'user' ? 'selected' : '' ?>>User</option>
            </select>
        </div>

        <div class="text-center">
            <button class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i> Simpan
            </button>
            <a href="/admin/users" class="btn btn-outline-secondary ms-2 px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
