<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | ZETANI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #007bff, #00bfff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-box {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            animation: fadeInUp 0.5s ease-in-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-box h2 {
            font-weight: 700;
            color: #007bff;
            text-align: center;
        }

        .login-box h5 {
            text-align: center;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .bottom-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .bottom-text a {
            color: #007bff;
            font-weight: 500;
            text-decoration: none;
        }

        .bottom-text a:hover {
            text-decoration: underline;
        }

        .alert {
            font-size: 0.9rem;
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
        }

        @media screen and (max-width: 420px) {
            .login-box {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>ZETANI</h2>
    <h5><i class="bi bi-leaf"></i> Solusi Petani Masa Kini</h5>

    <!-- ✅ Flash message sukses (misal setelah register) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <!-- ✅ Flash message error (misal login gagal) -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/login') ?>">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>

        <div class="bottom-text mt-3">
            Belum punya akun? <a href="<?= base_url('/register') ?>">Daftar</a><br>
            <a href="<?= base_url('/forgot-password') ?>">Lupa Password?</a>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
