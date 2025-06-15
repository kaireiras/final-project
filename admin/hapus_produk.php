<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../connection.php';
$id = intval($_GET['id']);

mysqli_query($conn, "DELETE FROM cart_item WHERE id_produk = $id");

mysqli_query($conn, "DELETE FROM produk WHERE id_produk = $id");

header("Location: dashboard.php");
exit;
?>
