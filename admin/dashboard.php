<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}

$id_admin = $_SESSION['id_admin'];
$query = "SELECT * FROM produk WHERE id_admin = $id_admin";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Sereniflora</title>
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
            margin: 0;
            padding: 2rem;
        }

        h2 {
            font-family: Alkatra;
            color: #319935;
            text-align: center;
        }

        .top-bar {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .top-bar a {
            text-decoration: none;
            color: #fff;
            background-color: #319935;
            border: 2px solid #000;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            font-weight: bold;
        }

        .top-bar a:hover {
            background-color: #267a28;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 2px solid #000;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 3px 3px 0px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #319935;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            border-radius: 8px;
        }

        .aksi a {
            text-decoration: none;
            color: #319935;
            font-weight: bold;
            margin: 0 5px;
        }

        .aksi a:hover {
            color: #000;
        }
    </style>
</head>
<body>

    <h2>Dashboard Admin</h2>

    <div class="top-bar">
        <a href="tambah_produk.php">+ Tambah Produk</a>
        <a href="logout.php">Logout</a>
    </div>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Nama Latin</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php $no = 1; while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td><?= htmlspecialchars($row['latin']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= $row['stok'] ?></td>
                    <td><img src="../src/<?= htmlspecialchars($row['gambar']) ?>" width="60"></td>
                    <td class="aksi">
                        <a href="edit_produk.php?id=<?= $row['id_produk'] ?>">Edit</a> |
                        <a href="hapus_produk.php?id=<?= $row['id_produk'] ?>" onclick="return confirm('yakin ga nih?')">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Belum ada produk ditambahkan.</td>
            </tr>
        <?php endif; ?>

    </table>

</body>
</html>
