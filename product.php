<?php
include 'connection.php';
session_start();

// Ambil produk berdasarkan id
if (!isset($_GET['id_produk'])) {
    echo "Produk tidak ditemukan.";
    exit;
}


$IsLoggedIn = (isset($_SESSION['id_user']));

$id_produk = (int) $_GET['id_produk'];
$query = "SELECT * FROM produk WHERE id_produk = $id_produk";
$result = mysqli_query($conn, $query);
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "Produk tidak tersedia.";
    exit;
}
$harga = (int)$produk['harga'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produk['nama_produk']) ?> - Sereniflora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles_test.css">
</head>
<body>

<section id="header">
    <header class="header">
        <button onclick="window.location.href='catalog.php'" class="logo">Sereniflora</button>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
        </div>
        <div class="header-actions">
            <a href="cart.php" style="text-decoration:none">
                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 22C9.5523 22 10 21.5523 10 21C10 20.4477 9.5523 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z"></path>
                    <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z"></path>
                    <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6"></path>
                </svg>
            </a>
            <div class="divider"></div>
            <div class="dropdown">
            <?php if ($IsLoggedIn): ?>
                <button class="rounded-circle" style="width: 30px; height: 30px; background-color: #d9d9d9; border: none; cursor: pointer;" onclick="window.location.href='user.php'"></button>
                <div class="dropdown-content">
                    <a href="user.php">Profile</a>
                    <a href="logout.php">log out</a>
                </div>
            <?php else: ?>
                <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#exModal">log in</a>
            <?php endif; ?>
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
</section>

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

<section id="konten main">
    <main class="main-content">
        <!-- Product Images -->
        <div class="product-images">
            <div class="image-card">
                <div class="main-image">
                    <img src="src/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" id="mainImage">
                </div>
                <div class="thumbnails">
                    <div class="thumbnail" onclick="changeImage('src/<?= $produk['gambar'] ?>')">
                        <img src="src/<?= $produk['gambar'] ?>" alt="Thumbnail 1">
                    </div>
                </div>
                <div class="like-section">
                    <svg class="heart-icon" viewBox="0 0 24 24" fill="none" stroke="#878585" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <span class="like-text">461 orang menyukai ini</span>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <div class="product-header">
                <h1 class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
                <p class="product-subtitle"><em><?= htmlspecialchars($produk['latin']) ?></em></p>
                <p class="product-price">Rp. <?= number_format($produk['harga'], 0, ',', '.') ?></p>
            </div>

            <div class="tabs">
                <div class="tab active" onclick="switchTab(this, 'deskripsi')">Deskripsi</div>
                <div class="tab" onclick="switchTab(this, 'ulasan')">Ulasan</div>
                <div class="tab" onclick="switchTab(this, 'pertanyaan')">Pertanyaan</div>
            </div>

            <div class="description" id="tabContent">
                <p><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
            </div>
        </div>

        <!-- Purchase Card -->
        <div class="purchase-card">
            <h3 class="purchase-title">Atur jumlah</h3>

            <div class="quantity-section">
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity()">−</button>
                    <span class="quantity-display" id="quantity">1</span>
                    <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                </div>
                <span class="stock-info">Stok total: <?= $produk['stok'] ?> tanaman</span>
            </div>

            <div class="subtotal-section">
                <span class="subtotal-label">Subtotal</span>
                <span class="subtotal-price" id="subtotal">Rp. <?= number_format($harga, 0, ',', '.') ?></span>
            </div>

            <form action="cart.php" method="POST">
                <input type="hidden" name="id_produk" value="<?= $produk['id_produk'] ?>">
                <input type="hidden" name="jumlah" id="jumlahInput" value="1">
                <button class="btn btn-cart" type="submit">+ Keranjang</button>
                <button class="btn btn-buy" type="submit" formaction="checkout.php">Beli</button>
            </form>
        </div>
    </main>
</section>

<script>
    let quantity = 1;
    const basePrice = <?= $harga ?>;
    const isLoggedIn = <?= $IsLoggedIn ? 'true' : 'false' ?>;

    function increaseQuantity() {
        quantity++;
        updateDisplay();
    }

    function decreaseQuantity() {
        if (quantity > 1) {
            quantity--;
            updateDisplay();
        }
    }

    function updateDisplay() {
        document.getElementById('quantity').textContent = quantity;
        document.getElementById('subtotal').textContent = 'Rp. ' + (quantity * basePrice).toLocaleString('id-ID');
        document.getElementById('jumlahInput').value = quantity;
    }

    function switchTab(tabElement, tabName) {
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        tabElement.classList.add('active');

        const content = document.getElementById('tabContent');
        switch (tabName) {
            case 'deskripsi':
                content.innerHTML = `<p><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>`;
                break;
            
            case 'ulasan':
                fetch('/get/get_ulasan.php?id_produk=<?= $produk['id_produk'] ?>')
                .then(res => res.text())
                .then(html => {
                    let formHtml = '';
                    if (isLoggedIn) {
                        formHtml = `
                            <form id="formUlasan" style="margin-top:1rem;">
                                <input type="text" id="namaUlasan" placeholder="Nama" value="<?= htmlspecialchars($_SESSION['nama']) ?>" readonly style="width:100%; margin-bottom:0.5rem;"/>
                                <textarea id="komentarUlasan" placeholder="Tulis ulasan..." required style="width:100%; height:80px;"></textarea><br>
                                <button type="submit">Kirim Ulasan</button>
                            </form>
                        `;
                    } else {
                        formHtml = `
                            <div style="margin-top:1rem; padding:1rem; background:white; border-radius:8px; text-align:center; border:1px solid #d9d9d9;">
                                <p>Silakan <a href="login.php" style="color:black; text-decoration:none; ft-bold">log in</a> untuk memberikan ulasan</p>
                            </div>
                        `;
                    }
                    
                    content.innerHTML = html + formHtml;

                    if (isLoggedIn) {
                        document.getElementById('formUlasan').addEventListener('submit', function(e){
                            e.preventDefault();
                            let komentar = document.getElementById('komentarUlasan').value.trim();
                            if (!komentar) return alert('Komentar tidak boleh kosong.');

                            fetch('/submit/submit_ulasan.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: `id_produk=<?= $produk['id_produk'] ?>&komentar=${encodeURIComponent(komentar)}`
                            }).then(res => res.text())
                            .then(res => {
                                document.getElementById('komentarUlasan').value = '';
                                // Reload ulasan
                                switchTab(document.querySelector('.tab.active'), 'ulasan');
                            });
                        });
                    }
                });
                break;

                
            case 'pertanyaan':
                fetch('/get/get_pertanyaan.php?id_produk=<?= $produk['id_produk'] ?>')
                .then(res => res.text())
                .then(html => {
                    let formHtml = '';
                    if (isLoggedIn) {
                        formHtml = `
                            <form id="formPertanyaan" style="margin-top:1rem;">
                                <input type="text" id="namaPertanyaan" placeholder="Nama" value="<?= htmlspecialchars($_SESSION['nama']) ?>" readonly style="width:100%; margin-bottom:0.5rem;"/>
                                <textarea id="komentarPertanyaan" placeholder="Tulis pertanyaan..." required style="width:100%; height:80px;"></textarea><br>
                                <button type="submit">Kirim Pertanyaan</button>
                            </form>
                        `;
                    } else {
                        formHtml = `
                            <div style="margin-top:1rem; padding:1rem; background:white; border-radius:8px; text-align:center; border:1px solid #d9d9d9">
                                <p>Silakan <a href="login.php" style="color:black; text-decoration:none;">log in</a> untuk bertanya</p>
                            </div>
                        `;
                    }
                    
                    content.innerHTML = html + formHtml;

                    if (isLoggedIn) {
                        document.getElementById('formPertanyaan').addEventListener('submit', function(e){
                            e.preventDefault();
                            let komentar = document.getElementById('komentarPertanyaan').value.trim();
                            if (!komentar) return alert('Komentar tidak boleh kosong.');

                            fetch('/submit/submit_pertanyaan.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: `id_produk=<?= $produk['id_produk'] ?>&komentar=${encodeURIComponent(komentar)}`
                            }).then(res => res.text())
                            .then(res => {
                                document.getElementById('komentarPertanyaan').value = '';
                                switchTab(document.querySelector('.tab.active'), 'pertanyaan');
                            });
                        });
                    }
                });
                break;
        }
    }

    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }
</script>
</body>
</html>