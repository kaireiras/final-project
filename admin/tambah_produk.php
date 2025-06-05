<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $latin = $_POST['latin'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar']['name'];
    $id_admin = $_SESSION['id_admin'];

    move_uploaded_file($_FILES['gambar']['tmp_name'], "../src/" . $gambar);

    $query = "INSERT INTO produk (nama_produk, latin, deskripsi, stok, harga, gambar, id_admin) 
              VALUES ('$nama','$latin', '$deskripsi', $stok, $harga, '$gambar', $id_admin)";
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - Sereniflora Admin</title>
    <style>
        @font-face {
            font-family: Alkatra;
            src: url('../font/Alkatra-VariableFont_wght.ttf');
        }

        @font-face {
            font-family: PlusJakarta;
            src: url('../font/PlusJakartaSans-Variable.ttf');
        }

        body {
            font-family: PlusJakarta, sans-serif;
            background-color: #f5f5f5;
            padding: 2rem;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            border: 2px solid #000;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 4px 4px 0px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: Alkatra;
            color: #319935;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #000;
            border-radius: 10px;
            font-size: 1rem;
            background: #fff;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #d9d9d9;
            border: 2px solid #000;
            color: #000;
            font-weight: bold;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #c9c9c9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            text-decoration: none;
            font-size: 0.9rem;
            color: #319935;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Produk</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama">Nama Produk</label>
            <input type="text" name="nama" id="nama" required>
        </div>

        <div class="form-group">
            <label for="latin">Nama Latin</label>
            <input type="text" name="latin" id="latin" style="font-style:italic;" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" name="stok" id="stok" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" name="harga" id="harga" required>
        </div>

        <div class="form-group">
            <label for="gambar">Gambar Produk</label>
            <input type="file" name="gambar" id="gambar" accept="image/*" required>
        </div>

        <button type="submit">Simpan Produk</button>
    </form>

    <a href="dashboard.php" class="back-link">← Kembali ke Dashboard</a>
</div>

</body>
</html>
