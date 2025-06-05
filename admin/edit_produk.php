<?php
session_start();
include '../connection.php';

$id = $_GET['id'];
$query = "SELECT * FROM produk WHERE id_produk = $id";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    
    if ($_FILES['gambar']['name']) {
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../src/" . $gambar);
    } else {
        $gambar = $data['gambar'];
    }

    $query = "UPDATE produk SET nama_produk='$nama', deskripsi='$deskripsi', stok=$stok, harga=$harga, gambar='$gambar' WHERE id_produk=$id";
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Produk</title></head>
<body>
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
        Nama: <input type="text" name="nama" value="<?= $data['nama_produk'] ?>"><br><br>
        Deskripsi: <textarea name="deskripsi"><?= $data['deskripsi'] ?></textarea><br><br>
        Stok: <input type="number" name="stok" value="<?= $data['stok'] ?>"><br><br>
        Harga: <input type="number" name="harga" value="<?= $data['harga'] ?>"><br><br>
        Gambar Lama: <img src="../src/<?= $data['gambar'] ?>" width="60"><br>
        Gambar Baru: <input type="file" name="gambar"><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
