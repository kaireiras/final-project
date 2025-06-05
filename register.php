<?php
session_start();
if (isset($_SESSION['id_user'])) {
    echo "<script>
        window.parent.postMessage('login-success', '*');
    </script>";
    exit;

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sereniflora - Login</title>
    <style>
        @font-face {
            font-family: Alkatra;
            src: url(font/Alkatra-VariableFont_wght.ttf);
        }

        @font-face {
            font-family: PlusJakarta;
            src: url(font/PlusJakartaSans-Variable.ttf);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: PlusJakarta;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .image-section {
            flex: 1;
            position: relative;
        }

        .plant-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: left;
        }

        .form-section {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border: 2px solid #000000;
            border-radius: 1.5rem;
            background-color: #ffffff;
        }

        .brand-name {
            font-size: 2.5rem;
            font-style: italic;
            color: #319935;
            text-align: center;
            margin-bottom: 2rem;
            font-family: Alkatra;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #000000;
            border-radius: 1rem;
            background-color: #ffffff;
            color: #000000;
            font-size: 1rem;
            outline: none;
        }

        .form-input::placeholder {
            color: #000000;
        }

        .form-input:focus {
            border-color: #319935;
        }

        .login-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #d9d9d9;
            color: #000000;
            border: 2px solid #000000;
            border-radius: 1rem;
            font-size: 1.125rem;
            cursor: pointer;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .login-button:hover {
            background-color: #c9c9c9;
        }

        .create-account {
            text-align: center;
        }

        .create-account a {
            color: #000000;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .create-account a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
        .info {
            color: green;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .image-section {
                height: 40vh;
            }
            .form-section {
                height: 60vh;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="src/Styling plant wall shelves adds visual appeal to….jpg" alt="kamar kalcer" class="plant-image">
        </div>

        <div class="form-section">
            <div class="login-card">
                <h1 class="brand-name">Sereniflora</h1>

                <?php if (isset($_SESSION['id_user'])): ?>
                    <div class="info">Kamu sudah login sebagai <strong><?= $_SESSION['nama'] ?></strong></div>
                    <a href="index.php" class="login-button">Kembali ke Beranda</a>
                    <a href="logout.php" class="login-button">Logout</a>
                <?php else: ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/submit/registrasi_user_submit.php">
                        <div class="form-group">
                            <input type="nama" name="nama" class="form-input" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-input" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-input" placeholder="Password" required>
                        </div>
                        <div class ="form-group">
                            <textarea type="alamat" name="alamat" class="form-input" placeholder="Alamat" required></textarea>
                        </div>
                        <button type="submit" class="login-button">Daftar</button>
                    </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
