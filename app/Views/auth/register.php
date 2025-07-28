<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #007bff, #00bfff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .flash-message {
            animation: fadeInDown 0.4s ease-in-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- ✅ Flash Success / Error Message -->
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show flash-message shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show flash-message shadow-sm rounded-3" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-header bg-primary text-white text-center rounded-top-4">
                    <h4 class="mb-0 fw-semibold"><i class="bi bi-person-plus-fill me-2"></i>Daftar Akun</h4>
                </div>
                <div class="card-body p-4">

                    <?php if (session()->getFlashdata('errors')) : ?>
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('/register') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="username" 
                                id="username" 
                                value="<?= old('username') ?>" 
                                placeholder="Masukkan username" 
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                name="email" 
                                id="email" 
                                value="<?= old('email') ?>" 
                                placeholder="Masukkan email aktif" 
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                name="password" 
                                id="password" 
                                placeholder="Masukkan password kuat" 
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Daftar
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted">Sudah punya akun?</small>
                        <a href="<?= base_url('/') ?>" class="fw-semibold text-decoration-none">Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Optional: Auto dismiss flash after 4s -->
<script>
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 4000);
</script>

</body>
</html>
