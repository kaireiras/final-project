<?php
include '../connection.php';
$id_produk = intval($_GET['id_produk'] ?? 0);
if ($id_produk <= 0) {
    exit("ID produk tidak valid");
}

$query = "SELECT nama, komentar, created_at FROM pertanyaan WHERE id_produk = $id_produk ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<p><b>" . htmlspecialchars($row['nama']) . "</b> (" . $row['created_at'] . ")<br>" .
            nl2br(htmlspecialchars($row['komentar'])) . "</p><hr>";
    }
} else {
    echo "<p>Belum ada pertanyaan.</p>";
}
?>
