<?php
include '../connection.php';

$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$alamat = $_POST['alamat'] ?? '';

// Validasi
if (!$nama || !$email || !$password || !$alamat) {
    header("Location: register_user.php?error=Semua kolom wajib diisi");
    exit;
}

// Cek apakah email sudah dipakai
$cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    header("Location: register.php?error=Email sudah terdaftar");
    exit;
}

// Tambahkan user baru
$query = "INSERT INTO pengguna (nama, email, password, alamat) VALUES ('$nama', '$email', '$password', '$alamat')";
if (mysqli_query($conn, $query)) {
    header("Location: ../login.php");
} else {
    header("Location: register.php?error=Gagal menyimpan data");
}
