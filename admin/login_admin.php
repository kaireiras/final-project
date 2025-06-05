<?php
session_start();
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email = '$email' AND password = '$pass'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['id_admin'] = $admin['id_admin'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Sereniflora</title>
    <style>
        @font-face {
            font-family: Alkatra;
            src: url('../font/Alkatra-VariableFont_wght.ttf');
        }

        @font-face {
            font-family: PlusJakarta;
            src: url('../font/PlusJakartaSans-Variable.ttf');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: PlusJakarta, sans-serif;
            height: 100vh;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #fff;
            border: 2px solid #000;
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 4px 4px 0px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            font-family: Alkatra;
            color: #319935;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #000;
            border-radius: 10px;
            background: #fff;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #d9d9d9;
            color: #000;
            font-weight: bold;
            border: 2px solid #000;
            border-radius: 10px;
            cursor: pointer;
        }

        button:hover {
            background: #c9c9c9;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login Admin</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
