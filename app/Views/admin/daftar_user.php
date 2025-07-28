<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="mb-4 text-primary text-center fw-bold">
        <i class="bi bi-people-fill me-2"></i> Daftar Pengguna
    </h2>

    <!-- Form Search dan Tambah -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <form class="d-flex flex-grow-1 me-md-2" method="get" action="">
            <input type="text" name="q" class="form-control me-2" placeholder="Cari username..." value="<?= esc($search) ?>">
            <button type="submit" class="btn btn-outline-primary me-2">
                <i class="bi bi-search"></i>
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Reset
                </a>
            <?php endif ?>
        </form>
        <a href="/admin/user/create" class="btn btn-success">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah User/Admin
        </a>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills nav-fill mb-3 shadow-sm rounded" id="userTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                <i class="bi bi-shield-lock-fill me-1"></i> Admin
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">
                <i class="bi bi-person-fill me-1"></i> User
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="userTabContent">
        <!-- Admin Tab -->
        <div class="tab-pane fade show active" id="admin" role="tabpanel">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Password</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminUsers as $user): ?>
                            <tr>
                                <td class="text-center"><?= esc($user['id']) ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email'] ?? '-') ?></td>
                                <td><code><?= esc($user['password']) ?></code></td>
                                <td class="text-center"><?= esc(date('d-m-Y H:i', strtotime($user['created_at'] ?? ''))) ?></td>
                                <td class="text-center">
                                    <a href="/admin/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="/admin/user/delete/<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Tab -->
        <div class="tab-pane fade" id="user" role="tabpanel">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle">
                   <thead class="table-primary text-center">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Password</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($normalUsers as $user): ?>
                            <tr>
                                <td class="text-center"><?= esc($user['id']) ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email'] ?? '-') ?></td>
                                <td><code><?= esc($user['password']) ?></code></td>
                                <td class="text-center"><?= esc(date('d-m-Y H:i', strtotime($user['created_at'] ?? ''))) ?></td>
                                <td class="text-center">
                                    <a href="/admin/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="/admin/user/delete/<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
