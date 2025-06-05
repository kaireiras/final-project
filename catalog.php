<?php
include 'connection.php';
session_start();

// Ambil parameter GET
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'terbaru';
$harga_min = $_GET['harga_min'] ?? '';
$harga_max = $_GET['harga_max'] ?? '';
$ready = isset($_GET['ready']);
$preorder = isset($_GET['preorder']);

// Query dasar
$query = "SELECT * FROM produk WHERE nama_produk LIKE '%$search%'";

// Filter harga
if ($harga_min !== '' && is_numeric($harga_min)) {
    $query .= " AND harga >= $harga_min";
}
if ($harga_max !== '' && is_numeric($harga_max)) {
    $query .= " AND harga <= $harga_max";
}

// Filter stok
if ($ready && !$preorder) {
    $query .= " AND stok > 0";
} elseif ($preorder && !$ready) {
    $query .= " AND stok = 0";
}

// Urutan
switch ($sort) {
    case 'terlama':
        $query .= " ORDER BY id_produk ASC";
        break;
    case 'termurah':
        $query .= " ORDER BY harga ASC";
        break;
    case 'termahal':
        $query .= " ORDER BY harga DESC";
        break;
    default:
        $query .= " ORDER BY id_produk DESC";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sereniflora - Plant Store</title>
    <link rel="stylesheet" href="/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="header">
    <a href="catalog.php" style="text-decoration:none">
        <div class="logo">Sereniflora</div>
    </a>

    <!-- Search Form -->
    <div class="search-container">
        <form method="GET">
            <input type="text" class="search-input" placeholder="Search..." name="search" value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>

    <div class="header-right">
        <a href="cart.php">
            <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 22C9.5523 22 10 21.5523 10 21C10 20.4477 9.5523 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z"></path>
                <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z"></path>
                <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6"></path>
            </svg>
        </a>
        <div class="divider"></div>
        <div class="dropdown">
            <?php if (!isset($_SESSION['id_user'])): ?>
            <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#exModal">log in</a>
        <?php else: ?>
            <button class="rounded-circle" style="width: 30px; height: 30px; background-color: #d9d9d9; border: none; cursor: pointer;" onclick="window.location.href='user.php'"></button>
            <div class="dropdown-content">
                <a href="user.php">Profile</a>
                <a href="logout.php">log out</a>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <script>
      window.addEventListener('message', function(event) {
        if (event.data === 'login-success') {
          const modalEl = document.getElementById('exModal');
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
          location.reload();
        }
      });
      </script>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="modal fade" id="exModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="height: 80vh;">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: calc(100% - 56px);">
                <iframe src="login.php" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="main-container">
    <!-- Sidebar Filter -->
    <aside class="sidebar">
        <h2 class="filter-title">Filter</h2>

        <form method="GET">
            <!-- Harga -->
            <div class="filter-section">
                <h3>Harga</h3>
                <div class="price-inputs">
                    <input type="text" name="harga_min" class="price-input" placeholder="Rp"
                        value="<?= htmlspecialchars($harga_min) ?>">
                    <input type="text" name="harga_max" class="price-input" placeholder="Rp"
                        value="<?= htmlspecialchars($harga_max) ?>">
                </div>
            </div>

            <!-- Rating -->
            <div class="filter-section">
                <h3>Rating</h3>
                <div class="rating-filter">
                    <input type="checkbox" class="checkbox" id="rating" disabled>
                    <label for="rating">Rating 4 ke atas</label>
                </div>
            </div>

            <!-- Ketersediaan -->
            <div class="filter-section">
                <h3>Lainnya</h3>
                <div class="filter-option">
                    <input type="checkbox" class="checkbox" name="preorder" id="preorder" <?= $preorder ? 'checked' : '' ?>>
                    <label for="preorder">PreOrder</label>
                </div>
                <div class="filter-option">
                    <input type="checkbox" class="checkbox" name="ready" id="ready" <?= $ready ? 'checked' : '' ?>>
                    <label for="ready">Ready Stock</label>
                </div>
            </div>

            <button type="submit" style="display:none;"></button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Sort -->
        <div class="sort-container">
            <form method="GET">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="harga_min" value="<?= htmlspecialchars($harga_min) ?>">
                <input type="hidden" name="harga_max" value="<?= htmlspecialchars($harga_max) ?>">
                <input type="hidden" name="ready" value="<?= $ready ? '1' : '' ?>">
                <input type="hidden" name="preorder" value="<?= $preorder ? '1' : '' ?>">

                <span class="sort-label">Urutkan:</span>
                <select class="sort-select" name="sort" onchange="this.form.submit()">
                    <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="terlama" <?= $sort === 'terlama' ? 'selected' : '' ?>>Terlama</option>
                    <option value="termurah" <?= $sort === 'termurah' ? 'selected' : '' ?>>Termurah</option>
                    <option value="termahal" <?= $sort === 'termahal' ? 'selected' : '' ?>>Termahal</option>
                </select>
            </form>
        </div>

        <!-- Grid Produk -->
        <div class="product-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <a href="product.php?id_produk=<?= $row['id_produk'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-card">
                            <div class="product-image-container">
                                <img src="src/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>" class="product-image">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></h3>
                                <p class="product-price">Rp. <?= number_format($row['harga'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            <?php else: ?>
                <p style="padding: 2rem;">Produk tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
