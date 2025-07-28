<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>
        <?php if (session()->getFlashdata('reset_link')): ?>
            Link Reset Terkirim
        <?php else: ?>
            Lupa Password
        <?php endif; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #007bff, #00bfff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            max-width: 500px;
            width: 100%;
            animation: slideFade 0.5s ease-in-out;
        }
        @keyframes slideFade {
            0% {opacity: 0; transform: translateY(20px);}
            100% {opacity: 1; transform: translateY(0);}
        }
        .form-box h2 {
            font-weight: 700;
            color: #2563eb;
        }
        .form-box p {
            color: #555;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .btn-primary {
            background-color: #2563eb;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .bottom-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
        }
        .bottom-text a {
            color: #6b21a8;
            font-weight: 600;
            text-decoration: none;
        }
        .bottom-text a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 8px;
            font-size: 0.95rem;
            word-break: break-word;
        }
        .alert-info a {
            color: #0c63e4;
        }
    </style>
</head>

<?php
    $reset_link = session()->getFlashdata('reset_link') ?? null;
    $message = session()->getFlashdata('message') ?? null;
    $error = session()->getFlashdata('error') ?? null;
?>

<body>
    <div class="form-box">
        <h2 class="mb-3 text-center">
            <i class="bi bi-lock"></i>
            <?= $reset_link ? 'Link Terkirim' : 'Lupa Password' ?>
        </h2>

        <p class="text-center mb-4">
            <?= $reset_link ? 'Kami telah mengirim link reset ke email Anda.' : 'Kami akan mengirim link reset ke email Anda.' ?>
        </p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= esc($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <?php if ($reset_link): ?>
            <div class="alert alert-info" id="resetLinkBox">
                Link reset password:<br>
                <a href="<?= esc($reset_link) ?>" onclick="hideLinkBox()">
                    <?= esc($reset_link) ?>
                </a>
            </div>
        <?php else: ?>
            <form method="post" action="<?= base_url('/forgot-password') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Masukkan Username" 
                        required
                    >
                </div>

                <div class="mb-4">
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Masukkan Email Terdaftar" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-envelope-arrow-up me-1"></i> Kirim Reset Link
                </button>
            </form>
        <?php endif; ?>

        <div class="bottom-text mt-3">
            <i class="bi bi-arrow-left"></i> Ingat password? 
            <a href="<?= base_url('/') ?>">Login</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function hideLinkBox() {
            const box = document.getElementById('resetLinkBox');
            if (box) {
                box.style.display = 'none';
            }
        }
    </script>
</body>
</html>
