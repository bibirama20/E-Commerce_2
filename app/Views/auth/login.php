<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:rgb(24, 123, 82);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: rgb(251, 252, 252);
            border-radius: 10px;
            box-shadow: linear-gradient(135deg,rgb(45, 191, 207) 0%,rgb(62, 196, 118)  100%);
            width: 350px;
            padding: 40px 30px;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(135deg,rgb(45, 191, 207) 0%,rgb(62, 196, 118)  100%);
            
            /* Tambahan agar lintas browser */
            background-clip: text;
            -webkit-background-clip: text;

            color: transparent;
            -webkit-text-fill-color: transparent;
        }

            .login-box h5 {
            text-align: center;
            margin-top: -30px; 
            margin-bottom: 10px;
            background: linear-gradient(135deg,rgb(62, 196, 118) 0%, rgb(45, 191, 207)  100%);
            
            /* Tambahan agar lintas browser */
            background-clip: text;
            -webkit-background-clip: text;

            color: transparent;
            -webkit-text-fill-color: transparent;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 95%;
            padding: 12px 8px;
            margin: 15px 0;
            border: 2px solid #ccc;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s;
        }

        .login-box input:focus {
            border-color: rgb(62, 196, 118);
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 10px;
           background: linear-gradient(135deg,rgb(45, 191, 207) 0%,rgb(62, 196, 118)  100%);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            opacity: 0.9;
        }

        .login-box .bottom-text {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .login-box .bottom-text a {
            color: #52248a;
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>ZETANI </h2>
    <h5> Solusi Petani Masa Kini</h5>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="/login">
        <input type="text" name="username" placeholder="Masukan Username" required>
        <input type="password" name="password" placeholder="Masukan Password" required>
        <button type="submit">Login</button>
    </form>

</div>

</body>
</html>