<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    http_response_code(403);
    exit("Silakan login terlebih dahulu.");
}

$id_produk = intval($_POST['id_produk'] ?? 0);
$komentar = trim($_POST['komentar'] ?? '');
$user_name = $_SESSION['nama'] ?? 'Anonim';

if ($id_produk <= 0 || $komentar === '') {
    http_response_code(400);
    exit("Data tidak lengkap.");
}

$stmt = $conn->prepare("INSERT INTO pertanyaan (id_produk, nama, komentar) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $id_produk, $user_name, $komentar);

if ($stmt->execute()) {
    echo "Pertanyaan berhasil dikirim.";
} else {
    http_response_code(500);
    echo "Gagal mengirim pertanyaan.";
}
$stmt->close();
$conn->close();
?>
