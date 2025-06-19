<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, rgb(86, 117, 241) 0%, #52248a 100%);
            color: #fff;
        }

        .sidebar a {
            color: #fff;
            display: block;
            padding: 10px 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background:rgb(102, 98, 98);
        }

        .role-bar {
            background: linear-gradient(135deg, rgb(86, 117, 241) 0%, #52248a 100%);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .role-bar .left {
            font-weight: 500;
        }

        .role-bar .right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-bar .right a {
            color: #fff;
            border: 1px solid #fff;
            padding: 3px 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .role-bar .right a:hover {
            background-color: rgba(250, 249, 249, 0.2);
        }
    </style>
</head>
<body>

    <!-- Role Info Bar / Header -->
    <div class="role-bar">
        <div class="left">
            <strong>AHS.CO</strong>
        </div>
        <div class="right">
            <span><?= esc(session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)</span>
        </div>
    </div>
