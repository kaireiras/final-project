<?php
session_start();
include '../connection.php';

$id = $_GET['id'];
$query = "SELECT * FROM produk WHERE id_produk = $id";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $latin = $_POST['latin'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    if ($_FILES['gambar']['name']) {
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../src/" . $gambar);
    } else {
        $gambar = $data['gambar'];
    }

    $query = "UPDATE produk SET nama_produk='$nama', latin='$latin', deskripsi='$deskripsi', stok=$stok, harga=$harga, gambar='$gambar' WHERE id_produk=$id";
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Sereniflora Admin</title>
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
            padding: 2.5rem;
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
            padding: 0.8rem;
            border: 2px solid #000;
            border-radius: 10px;
            font-size: 1rem;
            background: #fff;
            box-sizing: border-box;
        }

        input#latin {
            font-style: italic;
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

        .preview-img {
            display: block;
            margin-top: 0.5rem;
            max-width: 100px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Produk</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama">Nama Produk</label>
            <input type="text" name="nama" id="nama" value="<?= $data['nama_produk'] ?>" required>
        </div>

        <div class="form-group">
            <label for="latin">Nama Latin</label>
            <input type="text" name="latin" id="latin" value="<?= $data['latin'] ?>" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" required><?= $data['deskripsi'] ?></textarea>
        </div>

        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" name="stok" id="stok" value="<?= $data['stok'] ?>" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" name="harga" id="harga" value="<?= $data['harga'] ?>" required>
        </div>

        <div class="form-group">
            <label>Gambar Lama</label>
            <img src="../src/<?= $data['gambar'] ?>" alt="Gambar Lama" class="preview-img">
        </div>

        <div class="form-group">
            <label for="gambar">Gambar Baru (opsional)</label>
            <input type="file" name="gambar" id="gambar" accept="image/*">
        </div>

        <button type="submit">Update Produk</button>
    </form>

    <a
