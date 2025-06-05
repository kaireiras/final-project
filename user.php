<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
require_once 'connection.php'; // pastikan file ini sesuai path dan koneksi berhasil

// Ambil data pengguna dari database
$id_user = $_SESSION['id_user'];
$query = "SELECT * FROM pengguna WHERE id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Simpan data ke session agar bisa dipakai langsung di HTML
$_SESSION['nama'] = $user['nama'];
$_SESSION['email'] = $user['email'];
$_SESSION['alamat'] = $user['alamat'];
?>
      
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile - Sereniflora</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/styles_test.css">
    </head>
    <body>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <section id="header">
    <header class="header">
        <button onclick="window.location.href='catalog.php'" class="logo">Sereniflora</button>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
        </div>
        <div class="header-actions">
            <a href="cart.php">
                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 22C9.5523 22 10 21.5523 10 21C10 20.4477 9.5523 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z"></path>
                    <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z"></path>
                    <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6"></path>
                </svg>
            </a>
            <div class="divider"></div>
            <div class="avatar" title="<?= $_SESSION['nama'] ?>">
                <img src="default-avatar.png" width="32" height="32" style="border-radius: 50%;">
            </div>
        </div>
    </header>
</section>

        <section class="d-flex align-items-center px-4 py-2">
            <div class="avatar me-3" style="width: 200px; height: 200px;">
            </div>
            <p class="m-0 fw-bold" style="font-size: 40px; font-family: PlusJakarta; color:black;"><?= $_SESSION['nama'] ?></p>
        </section>




        <section id="main-content">
            <main>
                <div class="tabs">
                    <div class="tab active" onclick="switchTab(this, 'biodata')">Biodata</div>
                    <div class="tab" onclick="switchTab(this, 'histori')">Histori Pembelian</div>
                    <div class="tab" onclick="switchTab(this, 'setting')">Setting</div>
                </div>

                <div id="tabContent">
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Alamat:</strong> <?= htmlspecialchars($user['alamat']) ?></p>
                    </form>
                    </p>
                </div>

            </main>
        </section>
        
        <script>
            function switchTab(tabElement, tabName) {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Add active class to clicked tab
                tabElement.classList.add('active');
                
                // Update content based on tab
                const content = document.getElementById('tabContent');

                switch(tabName) {
                    case 'biodata':
                        content.innerHTML = `
                                <p><strong>Nama:</strong> <?= $_SESSION['nama'] ?></p>
                                <p><strong>Email:</strong> <?= $_SESSION['email'] ?></p>
                                <p><strong>Alamat:</strong> <?= $_SESSION['alamat'] ?></p>
                        `;
                        break;
                    case 'histori':
                        content.innerHTML = `
                            <p>Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</p>
                        `;
                        break;
                    
                    case 'setting':
                        content.innerHTML = `
                         <p>lagi dibikin bentar</p>`
                }

            }
        </script>

    </body>
    </html>