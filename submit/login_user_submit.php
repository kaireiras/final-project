<?php
session_start();
include '../connection.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['error'] = 'email dan password wajib diisi';
    header("Location:../login.php");
    exit;
}

$query = "SELECT * FROM pengguna WHERE email = '$email' AND password = '$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['alamat'] = $user['alamat'];
    echo "<script>
        window.parent.postMessage('login-success', '*');
    </script>";
    exit;

} else {
    $_SESSION['error'] = 'email atau password salah, coba lagi!';
    header("Location:../login.php");
    exit;
}
