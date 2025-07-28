<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f8f9fc;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 60px;
        }

        .role-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
            background: linear-gradient(135deg, #007bff 0%, #00bfff 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 24px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .role-bar .left .brand {
            font-size: 1.0rem;
            font-weight: bold;
        }

        .role-bar .left .tagline {
            font-size: 0.9rem;
            color: #eaf6ff;
        }

        .role-bar .right {
            font-size: 0.9rem;
        }

        .role-bar .right a {
            color: white;
            border: 1px solid #fff;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .role-bar .right a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .content-wrapper {
            flex: 1 0 auto;
            display: flex;
        }

        .sidebar {
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            width: 230px;
            background: linear-gradient(135deg, #007bff 0%, #00bfff 100%);
            color: white;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 230px;
            padding: 24px;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="role-bar">
    <div class="left">
        <div class="brand">ZETANI</div>
        <div class="tagline">Solusi Petani Masa Kini</div>
    </div>
    <div class="right">
        <span>🙋🏻‍♂ <?= esc(session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)</span>
    </div>
</div>
