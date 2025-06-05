<?php
include 'connection.php';
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    $_SESSION['pesan_error'] = "Anda harus login untuk mengakses keranjang.";
    header('Location: login.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];

// --- Fungsi Helper untuk mendapatkan/membuat Cart ID ---
function getOrCreateCartId($conn, $id_user) {
    // Cek apakah user sudah punya cart yang aktif
    $stmt = $conn->prepare("SELECT id_cart FROM cart WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['id_cart']; // Cart sudah ada
    } else {
        // Buat cart baru jika belum ada
        $stmt_insert = $conn->prepare("INSERT INTO cart (id_user) VALUES (?)");
        $stmt_insert->bind_param("i", $id_user);
        if ($stmt_insert->execute()) {
            return $conn->insert_id; // Mengembalikan ID cart yang baru dibuat
        } else {
            // Handle error jika gagal membuat cart
            error_log("Error creating cart: " . $stmt_insert->error);
            return false;
        }
    }
}

$id_cart = getOrCreateCartId($conn, $id_user);

if (!$id_cart) {
    $_SESSION['pesan_error'] = "Terjadi kesalahan saat membuat keranjang. Silakan coba lagi.";
    header('Location: index.php'); // Atau halaman yang sesuai
    exit;
}

// --- LOGIKA MENAMBAH/MENGUPDATE ITEM KE KERANJANG (dari product_detail.php) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produk']) && isset($_POST['jumlah'])) {
    $id_produk = (int) $_POST['id_produk'];
    $jumlah = (int) $_POST['jumlah'];

    // Validasi jumlah (pastikan tidak nol atau negatif)
    if ($jumlah <= 0) {
        $_SESSION['pesan_error'] = "Jumlah produk harus lebih dari 0.";
        header('Location: product_detail.php?id_produk=' . $id_produk);
        exit;
    }

    // Ambil detail produk dari database untuk validasi stok dan harga
    $stmt_produk = $conn->prepare("SELECT nama_produk, harga, stok FROM produk WHERE id_produk = ?");
    $stmt_produk->bind_param("i", $id_produk);
    $stmt_produk->execute();
    $result_produk = $stmt_produk->get_result();
    $produk = $result_produk->fetch_assoc();

    if (!$produk) {
        $_SESSION['pesan_error'] = "Produk tidak ditemukan.";
        header('Location: index.php');
        exit;
    }

    // Periksa stok
    if ($jumlah > $produk['stok']) {
        $_SESSION['pesan_peringatan'] = "Stok produk tidak mencukupi. Tersedia: " . $produk['stok'] . " tanaman.";
        header('Location: product_detail.php?id_produk=' . $id_produk);
        exit;
    }

    // Cek apakah produk sudah ada di cart_item untuk cart ini
    $stmt_check_item = $conn->prepare("SELECT id_cart_item, jumlah FROM cart_item WHERE id_cart = ? AND id_produk = ?");
    $stmt_check_item->bind_param("ii", $id_cart, $id_produk);
    $stmt_check_item->execute();
    $result_check_item = $stmt_check_item->get_result();

    if ($existing_item = $result_check_item->fetch_assoc()) {
        // Produk sudah ada, update jumlahnya
        $new_jumlah = $existing_item['jumlah'] + $jumlah;
        
        // Pastikan jumlah tidak melebihi stok
        if ($new_jumlah > $produk['stok']) {
            $new_jumlah = $produk['stok'];
            $_SESSION['pesan_peringatan'] = "Jumlah produk di keranjang disesuaikan karena melebihi stok. Jumlah maksimum: " . $produk['stok'];
        }

        $stmt_update_item = $conn->prepare("UPDATE cart_item SET jumlah = ? WHERE id_cart_item = ?");
        $stmt_update_item->bind_param("ii", $new_jumlah, $existing_item['id_cart_item']);
        if ($stmt_update_item->execute()) {
            $_SESSION['pesan_sukses'] = "Jumlah produk di keranjang berhasil diperbarui.";
        } else {
            $_SESSION['pesan_error'] = "Gagal memperbarui jumlah produk di keranjang.";
        }
    } else {
        // Produk belum ada, tambahkan sebagai item baru
        $stmt_insert_item = $conn->prepare("INSERT INTO cart_item (id_cart, id_produk, jumlah) VALUES (?, ?, ?)");
        $stmt_insert_item->bind_param("iii", $id_cart, $id_produk, $jumlah);
        if ($stmt_insert_item->execute()) {
            $_SESSION['pesan_sukses'] = "Produk berhasil ditambahkan ke keranjang!";
        } else {
            $_SESSION['pesan_error'] = "Gagal menambahkan produk ke keranjang.";
        }
    }
    header('Location: cart.php'); // Redirect ke halaman keranjang untuk menampilkan update
    exit;
}

// --- LOGIKA PENGHAPUSAN ITEM DARI KERANJANG ---
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id_produk'])) {
    $id_produk_hapus = (int) $_GET['id_produk'];

    $stmt_delete = $conn->prepare("DELETE FROM cart_item WHERE id_cart = ? AND id_produk = ?");
    $stmt_delete->bind_param("ii", $id_cart, $id_produk_hapus);
    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            $_SESSION['pesan_sukses'] = "Produk berhasil dihapus dari keranjang.";
        } else {
            $_SESSION['pesan_error'] = "Produk tidak ditemukan di keranjang Anda.";
        }
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus produk dari keranjang.";
    }
    header('Location: cart.php');
    exit;
}

// --- LOGIKA UPDATE JUMLAH DARI HALAMAN KERANJANG ITU SENDIRI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_jumlah_cart_page']) && isset($_POST['id_produk']) && isset($_POST['jumlah_baru'])) {
    $id_produk_update = (int) $_POST['id_produk'];
    $jumlah_baru = (int) $_POST['jumlah_baru'];

    if ($jumlah_baru <= 0) {
        // Hapus item jika jumlahnya 0 atau kurang (opsional, bisa juga tampilkan error)
        $stmt_delete_zero = $conn->prepare("DELETE FROM cart_item WHERE id_cart = ? AND id_produk = ?");
        $stmt_delete_zero->bind_param("ii", $id_cart, $id_produk_update);
        $stmt_delete_zero->execute();
        $_SESSION['pesan_sukses'] = "Produk dihapus dari keranjang.";
        header('Location: cart.php');
        exit;
    }

    // Ambil stok terbaru dari database
    $stmt_stok = $conn->prepare("SELECT stok FROM produk WHERE id_produk = ?");
    $stmt_stok->bind_param("i", $id_produk_update);
    $stmt_stok->execute();
    $result_stok = $stmt_stok->get_result();
    $data_stok = $result_stok->fetch_assoc();
    $stok_tersedia = $data_stok['stok'];

    if ($jumlah_baru > $stok_tersedia) {
        $jumlah_baru = $stok_tersedia; // Sesuaikan dengan stok
        $_SESSION['pesan_peringatan'] = "Jumlah melebihi stok yang tersedia (" . $stok_tersedia . "). Jumlah disesuaikan.";
    }

    $stmt_update_quantity = $conn->prepare("UPDATE cart_item SET jumlah = ? WHERE id_cart = ? AND id_produk = ?");
    $stmt_update_quantity->bind_param("iii", $jumlah_baru, $id_cart, $id_produk_update);
    if ($stmt_update_quantity->execute()) {
        $_SESSION['pesan_sukses'] = "Jumlah produk berhasil diperbarui.";
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui jumlah produk.";
    }
    header('Location: cart.php');
    exit;
}

// --- Ambil data keranjang dari database untuk ditampilkan ---
// Menggabungkan cart_item dengan tabel produk untuk mendapatkan detail produk
$keranjang_db = [];
$total_harga_keranjang = 0;

$stmt_get_cart_items = $conn->prepare("
    SELECT ci.id_cart_item, ci.id_produk, ci.jumlah, p.nama_produk, p.harga, p.gambar, p.stok
    FROM cart_item ci
    JOIN produk p ON ci.id_produk = p.id_produk
    WHERE ci.id_cart = ?
");
$stmt_get_cart_items->bind_param("i", $id_cart);
$stmt_get_cart_items->execute();
$result_cart_items = $stmt_get_cart_items->get_result();

while ($item = $result_cart_items->fetch_assoc()) {
    $keranjang_db[] = $item;
    $total_harga_keranjang += $item['harga'] * $item['jumlah'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS yang sudah Anda berikan */
        body { background-color: #ffffff; font-family: -apple-system, BlinkMacMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .cart-container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        .cart-title { color: #000000; font-size: 2rem; font-weight: 500; text-align: center; margin-bottom: 3rem; }
        .table-header { border-bottom: 1px solid #000000; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .table-header .col { color: #000000; font-weight: 500; }
        .product-image { width: 128px; height: 128px; background-color: #d9d9d9; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem; }
        .product-image img { max-width: 100%; max-height: 100%; object-fit: cover; }
        .product-name { color: #000000; font-weight: 500; }
        .product-row { margin-bottom: 2rem; align-items: center; }
        .product-row .col { color: #000000; }
        .separator { border-bottom: 1px solid #000000; margin: 2rem 0; }
        .total-section { text-align: right; margin-bottom: 3rem; }
        .total-text { color: #000000; font-size: 1.125rem; }
        .total-amount { font-weight: 500; margin-left: 2rem; }
        .action-buttons { max-width: 400px; margin: 0 auto; }
        .cart-btn { background-color: #d9d9d9; border: none; color: #000000; padding: 1rem 2rem; font-size: 1.125rem; font-weight: 500; border-radius: 8px; width: 100%; margin-bottom: 1rem; transition: background-color 0.2s; }
        .cart-btn:hover { background-color: #c5c5c5; color: #000000; }
        @media (max-width: 768px) {
            .table-header { display: none; }
            .product-row { display: none; }
            .mobile-card { display: block !important; border: 1px solid #d9d9d9; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; }
            .mobile-card .product-image { width: 80px; height: 80px; margin-right: 1rem; }
        }
        .mobile-card { display: none; }
        .quantity-control-buttons { display: flex; align-items: center; justify-content: center; }
        .quantity-control-buttons button { background-color: #f0f0f0; border: 1px solid #ccc; padding: 0.2rem 0.6rem; cursor: pointer; border-radius: 4px; font-size: 1rem; }
        .quantity-control-buttons input { width: 50px; text-align: center; border: 1px solid #ccc; margin: 0 5px; border-radius: 4px; padding: 0.2rem 0.5rem; }
        .remove-btn { background-color: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; transition: background-color 0.2s; }
        .remove-btn:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="cart-container">
        <h1 class="cart-title">Cart</h1>

        <?php if (isset($_SESSION['pesan_sukses'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['pesan_error'])): ?>
            <div class="alert alert-danger text-center">
                <?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['pesan_peringatan'])): ?>
            <div class="alert alert-warning text-center">
                <?= $_SESSION['pesan_peringatan']; unset($_SESSION['pesan_peringatan']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($keranjang_db)): ?>
            <p class="text-center">Keranjang Anda kosong.</p>
            <div class="action-buttons">
                <button class="cart-btn" onclick="backToShopping()">Lanjutkan Belanja</button>
            </div>
        <?php else: ?>
            <div class="d-none d-md-block">
                <div class="row table-header">
                    <div class="col-4">Produk</div>
                    <div class="col-2 text-center">Harga Satuan</div>
                    <div class="col-2 text-center">Jumlah</div>
                    <div class="col-2 text-center">Total Harga</div>
                    <div class="col-2 text-center">Aksi</div>
                </div>

                <?php foreach ($keranjang_db as $item): ?>
                    <div class="row product-row">
                        <div class="col-4 d-flex align-items-center">
                            <div class="product-image me-3">
                                <img src="src/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                            </div>
                            <div class="product-name"><?= htmlspecialchars($item['nama_produk']) ?></div>
                        </div>
                        <div class="col-2 text-center align-self-center">Rp. <?= number_format($item['harga'], 0, ',', '.') ?></div>
                        <div class="col-2 text-center align-self-center">
                            <form action="cart.php" method="POST">
                                <input type="hidden" name="id_produk" value="<?= $item['id_produk'] ?>">
                                <input type="hidden" name="update_jumlah_cart_page" value="1"> <div class="quantity-control-buttons">
                                    <button type="button" onclick="this.form.elements['jumlah_baru'].value = Math.max(1, parseInt(this.form.elements['jumlah_baru'].value) - 1); this.form.submit();">-</button>
                                    <input type="number" name="jumlah_baru" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" onchange="this.form.submit()">
                                    <button type="button" onclick="this.form.elements['jumlah_baru'].value = Math.min(<?= $item['stok'] ?>, parseInt(this.form.elements['jumlah_baru'].value) + 1); this.form.submit();">+</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-2 text-center align-self-center">Rp. <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></div>
                        <div class="col-2 text-center align-self-center">
                            <a href="cart.php?action=hapus&id_produk=<?= $item['id_produk'] ?>" class="remove-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?');">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-md-none">
                <?php foreach ($keranjang_db as $item): ?>
                    <div class="mobile-card">
                        <div class="d-flex">
                            <div class="product-image">
                                <img src="src/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>">
                            </div>
                            <div class="flex-grow-1">
                                <div class="product-name mb-2"><?= htmlspecialchars($item['nama_produk']) ?></div>
                                <div class="row g-2">
                                    <div class="col-6"><small>Harga: Rp. <?= number_format($item['harga'], 0, ',', '.') ?></small></div>
                                    <div class="col-6 text-end">
                                        <form action="cart.php" method="POST">
                                            <input type="hidden" name="id_produk" value="<?= $item['id_produk'] ?>">
                                            <input type="hidden" name="update_jumlah_cart_page" value="1">
                                            <div class="quantity-control-buttons">
                                                <button type="button" onclick="this.form.elements['jumlah_baru'].value = Math.max(1, parseInt(this.form.elements['jumlah_baru'].value) - 1); this.form.submit();">-</button>
                                                <input type="number" name="jumlah_baru" value="<?= $item['jumlah'] ?>" min="1" max="<?= $item['stok'] ?>" style="width: 40px; margin: 0 5px;" onchange="this.form.submit()">
                                                <button type="button" onclick="this.form.elements['jumlah_baru'].value = Math.min(<?= $item['stok'] ?>, parseInt(this.form.elements['jumlah_baru'].value) + 1); this.form.submit();">+</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-6"><small>Total: <strong>Rp. <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong></small></div>
                                    <div class="col-6 text-end">
                                        <a href="cart.php?action=hapus&id_produk=<?= $item['id_produk'] ?>" class="remove-btn" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?');">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="separator"></div>

            <div class="total-section">
                <span class="total-text">Total :<span class="total-amount">Rp. <?= number_format($total_harga_keranjang, 0, ',', '.') ?></span></span>
            </div>

            <div class="action-buttons">
                <button class="cart-btn" onclick="checkout()">Checkout</button>
                <button class="cart-btn" onclick="backToShopping()">Lanjutkan Belanja</button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkout() {
            window.location.href = 'checkout.php';
        }

        function backToShopping() {
            window.location.href = 'catalog.php';
        }
    </script>
</body>
</html>