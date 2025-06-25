<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Dashboard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', sans-serif;
            padding-top: 60px; /* space for sticky header */
        }

        .role-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
            background: linear-gradient(135deg,rgb(45, 191, 207) 0%,rgb(62, 196, 118)  100%);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
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

        .content-wrapper {
            flex: 1 0 auto;
            display: flex;
        }

        .sidebar {
            position: fixed;
            top: 50px;
            bottom: 0;
            left: 0;
            width: 230px;
            background: linear-gradient(135deg, rgb(45, 191, 207) 0%, rgb(62, 196, 118) 100%);
            color: #fff;
            overflow-y: auto;
        }

        .sidebar a {
            color: #fff;
            display: block;
            padding: 10px 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: rgb(102, 98, 98);
        }

        .main-content {
            margin-left: 250px;
            padding: 15px;
            width: 100%;
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="role-bar">
    <div class="left">
        <strong>ZETANI</strong>
    </div>
    <div class="right">
        <span>🙋🏻‍♂<?= esc(session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)</span>
    </div>
</div>