<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | ZETANI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #007bff, #00bfff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .reset-box {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reset-box h2 {
            font-weight: 700;
            color: #007bff;
        }

        .btn-primary {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        a.text-decoration-none {
            font-size: 0.9rem;
            color: #007bff;
        }

        a.text-decoration-none:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="reset-box">
    <h2 class="mb-4 text-center">
        <i class="bi bi-shield-lock-fill me-1"></i> Reset Password
    </h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/reset-password/' . esc($token)) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="user_id" value="<?= esc($user_id) ?>">
        <input type="hidden" name="token" value="<?= esc($token) ?>">

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="form-control" 
                placeholder="Masukkan password baru" 
                required 
                minlength="6"
            >
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Password Baru
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <a href="<?= base_url('login') ?>" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Kembali ke Halaman Login
        </a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
