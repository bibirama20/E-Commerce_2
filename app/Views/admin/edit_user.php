<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h3 class="text-primary fw-bold">
            <i class="bi bi-pencil-square me-2"></i> Edit User
        </h3>
        <p class="text-muted">Perbarui data user di bawah ini. Kosongkan password jika tidak ingin mengubahnya.</p>
    </div>

    <form action="<?= base_url('admin/user/update/' . $user['id']) ?>" method="post" class="col-md-6 mx-auto bg-light p-4 rounded shadow-sm">
        <?= csrf_field() ?>

        <!-- Username -->
        <div class="mb-3">
            <label for="username" class="form-label fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="<?= esc($user['username']) ?>" required placeholder="Masukkan username">
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required placeholder="Masukkan email">
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label for="role" class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            </select>
        </div>

        <!-- Tombol -->
        <div class="text-center">
            <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i> Simpan
            </button>
            <a href="/admin/users" class="btn btn-outline-secondary ms-2 px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
