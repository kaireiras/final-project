<?php
include 'connection.php';
session_start();

// Ambil semua produk dari database
$query = "SELECT * FROM produk";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sereniflora</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <header>
    <nav class="position-relative px-4 py-2" style="background-color: #d9d9d9;">
      <div class="d-inline-block">
        <a class="navbar-brand fw-bold fst-italic text-success m-0" style="font-face:Alkatra;" href="#">Sereniflora</a>
      </div>
      <div class="position-absolute top-50 start-50 translate-middle d-flex gap-5">
        <a href="#about" class="text-dark text-decoration-none">About</a>
        <a href="catalog.php" class="fw-bold text-dark text-decoration-none">Buy Now</a>
        <a href="cart.php" class="text-dark text-decoration-none">Cart</a>
      </div>
      <div class="d-inline block float-end">
        <?php if (!isset($_SESSION['id_user'])): ?>
          <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#exModal">log in</a>
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
        </div>
        <?php else: ?>
          <div class="dropdown">
            <button class="rounded-circle" style="width: 30px; height: 30px; background-color: white; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; overflow: hidden; " onclick="window.location.href='user.php'"></button>
          <div class="dropdown-content">
            <a href="user.php">Profie</a>
            <a href="logout.php">log out</a>
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
    </nav>
  </header>
  
  <section id="hero" class="text-center py-5">
    <main>
      <h1 style="font-family: 'Times New Roman', Times, serif;">Heal Thy World.</h1>
      <div class="position-relative">
        <img src="Group_1.png" alt="Group_1" width="1028" height="612" class="img-fluid">
      </div>
    </main>
  </section>
  
  <section id="kata-kata" class="text-center py-5">
    <h1 style="font-family: 'Times New Roman', Times, serif;">Tree isn’t for luck, it’s for life.</h1>
    <h2 style="font-family: PlusJakarta;">Our Best Seller</h2>
  </section>
  
  <section id="grid-populer">
    <div class="grid-container">
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="card">
          <a href="test.php?id_produk=<?= $row['id_produk'] ?>" style="text-decoration: none; color: inherit;">
            <div class="card-image-wrapper">
              <img src="src/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>" />
            </div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['nama_produk']) ?></h3>
              <p class="scientific-name"><em><?= htmlspecialchars($row['deskripsi']) ?></em></p>
              <p class="description">Stok: <?= $row['stok'] ?> | Harga: Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>
  </section>
  
  <section id="about">
    <div class="hero-section-wrapper">
      <div class="text-content">
        <h1 style="font-family: 'Times New Roman', Times, serif;">Let Nature In, Let Stillness Bloom.</h1>
        <p style="font-family: PlusJakarta;">Within every leaf lies a whisper of peace.</p>
        <p style="font-family: PlusJakarta;">In every stem, a quiet reminder that life, like nature, grows best when nurtured with patience and love.</p>
        <p style="font-family: PlusJakarta;">Plants are not just decorations—they are living symbols of harmony, resilience, and renewal. They teach us to breathe deeper, to be present, to honor the still moments between the chaos.</p>
        <p style="font-family: PlusJakarta;">Bringing a plant into your home is more than adding green to a room—it is inviting balance, welcoming calm, and choosing to live gently.</p>
        <p style="font-family: PlusJakarta;">Let your home be a sanctuary. Let green be your guide.</p>
        <p style="font-family: PlusJakarta;">Start your journey to inner peace—one plant at a time.</p>
        <a href="product.php" class="cta-button">SHOP NOW DISC 50%</a>
      </div>
      <div class="image-content">
        <img src="src/plant.png" alt="Green Plant" class="plant-image-absolute">
      </div>
    </div>
  </section>
  
  <!-- Footer -->
  <footer class="text-center text-lg-start bg-body-tertiary text-muted">
    <!-- Section: Social media -->
    <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
      <div class="me-5 d-none d-lg-block">
        <span>Get connected with us on social networks:</span>
      </div>
      <div>
        <a href="#" class="me-4 text-reset"><i class="fab fa-facebook"></i></a>
        <a href="#" class="me-4 text-reset"><i class="fab fa-x-twitter"></i></a>
        <a href="#" class="me-4 text-reset"><i class="fab fa-instagram"></i></a>
      </div>
    </section>
    
    <section>
      <div class="container text-center text-md-start mt-5">
        <div class="row mt-3">
          <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <h6 class="text-uppercase fw-bold mb-4" style="font-family: Alkatra; color: #319935; cursor: pointer;">
              <i class="fas fa-gem me-3"></i>Sereniflora
            </h6>
            <p>Heal Thy World.</p>
          </div>
          
          <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
            <h6 class="text-uppercase fw-bold mb-4">Useful links</h6>
            <p><a href="#!" class="text-reset">Store</a></p>
            <p><a href="#!" class="text-reset">Log in</a></p>
            <p><a href="#!" class="text-reset">Job</a></p>
          </div>
          
          <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
            <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
            <p><i class="fas fa-home me-3"></i> Jakarta, Indonesia</p>
            <p><i class="fas fa-envelope me-3"></i> cs@sereniflora.com</p>
            <p><i class="fas fa-phone me-3"></i> +62 234 567 88</p>
            <p><i class="fas fa-print me-3"></i> +62 234 567 89</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Copyright -->
     <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
      © 2021 Copyright:
      <a class="text-reset fw-bold" href="sereniflora.com">sereniflora.com</a>
    </div>
  </footer>
</body>
</html>